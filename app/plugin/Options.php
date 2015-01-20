<?php

/**
 * Options
 *
 * Interface for serialized options
 */

namespace RichJenks\MarketoLeads;

class Options {

	/**
	 * @var array Current options
	 */

	protected $options = false;

	/**
	 * @var array Default options
	 */

	private $defaults = array(

		// Marketo API
		'client_id'     => '',
		'client_secret' => '',
		'munchkin_id'   => '',

		// Plugin Options
		'status' => 'Disabled',
		'debug'  => 'Disabled',
		'action' => 'Create/Update',
		'hooks'  => 'wp_loaded',

		// Default Fields
		'default_fields' => '',

	);

	/**
	 * get_options
	 *
	 * @return array Plugin options
	 */

	protected function get_options( $option = false ) {

		// If options not constructed, get them
		if ( !$this->options ) {

			// Get encrypted options
			$options = get_option( 'rj_ml_options', '[]' );

			// If stored options are returned and are encrypted, decrypt them
			if ( $options !== '[]' && substr( $options, 0, 1 ) !== '{' ) {
				$options = $this->decrypt( $options, AUTH_KEY );
			}

			// If first character not `{`, decryption failed and should revert to defaults
			if ( substr( $options, 0, 1 ) !== '{' ) $options = '[]';

			// Convert to array
			$options = json_decode( $options, true );

			// Save merged options
			$this->options = array_replace_recursive( $this->defaults, $options );

		}

		// Return specific option or all options
		return ( $option ) ? $this->options[ $option ] : $this->options;

	}

	/**
	 * set_options
	 *
	 * @param array $options Options array
	 */

	protected function set_options( $options ) {

		// Remove invalid options
		foreach ( $options as $option => $value ) {
			if ( !isset( $this->defaults[ $option ] ) ) {
				unset( $options[ $option ] );
			}
		}

		// Other sanitization
		foreach ( $options as $option => $value ) {

			// Trim values
			$options[ $option ] = trim( $options[ $option ] );

			// Trim multiline values
			$options[ $option ] = $this->format_tabular_data( $options[ $option ], "\n", '|' );

		}

		// Apply filter
		$options = apply_filters( 'rj_ml_save_options', $options );

		// Serialize & encrypt data
		$options = $this->encrypt( json_encode( $options ), AUTH_KEY );

		update_option( 'rj_ml_options', $options );

	}

	/**
	 * format_tabular_data
	 *
	 * Fixes whitespace issues in tabular plaintext data
	 *
	 * @param string $data Plaintext data
	 * @param string $line_sep Line separator
	 * @param string $cell_sep Cell separator
	 *
	 * @return string Formatted data
	 */

	protected function format_tabular_data( $data, $line_sep = false, $cell_sep = false ) {
		if ( $line_sep && strpos( $data, $line_sep ) !== false ) {
			$lines = explode( $line_sep, $data );
			foreach ( $lines as $line_key => $line_value ) {
				$lines[ $line_key ] = trim( $lines[ $line_key ] );
				if ( $lines[ $line_key ] === '' ) {
					unset( $lines[ $line_key ] );
				} else {
					$lines[ $line_key ] = preg_replace( '!\s+!', ' ', $lines[ $line_key ] );
					if ( $cell_sep && strpos( $lines[ $line_key ], $cell_sep ) !== false ) {
						$cells = explode( $cell_sep, $lines[ $line_key ] );
						foreach ( $cells as $cell_key => $cell_value ) {
							$cells[ $cell_key ] = trim( $cell_value );
						}
						$lines[ $line_key ] = implode( $cell_sep, $cells );
						if ( $cell_sep !== ' ' ) {
							$lines[ $line_key ] = str_replace( $cell_sep, ' ' . $cell_sep . ' ', $lines[ $line_key ]);
						}
					}
				}
			}
			$data = implode( $line_sep, $lines );
		}
		return $data;
	}

	/**
	 * add_notice
	 *
	 * Adds a simple notice to the admin area
	 *
	 * @param string $type Notice type, e.g. `updated`, `error`, or `update-nag`
	 * @param string $text Text to be shown in the notice
	 */

	protected function add_notice( $type, $text ) {
		add_action( 'admin_notices', function () use ( $type, $text ) {
			echo '<div class="' . $type . '"><p>' . $text . '</p></div>';
		} );
	}

	/**
	 * create_lead
	 *
	 * Sends the lead to the Marketo API
	 */

	protected function create_lead( $lead, $options ) {

		// Is there a lead?
		if ( count( $lead ) !== 0 ) {

			// API Options
			$api_options = array(
				'client_id'     => $options['client_id'],
				'client_secret' => $options['client_secret'],
				'munchkin_id'   => $options['munchkin_id'],
			);

			// Create API client using Options class
			$client = \CSD\Marketo\Client::factory( $api_options );

			// Construct leads array (must be array of lead arrays for API)
			$lead = array( $lead );

			// How to handle lead?
			switch ( $options['action'] ) {

				case 'Create only':
					$response = $client->createLeads( $lead );
					break;

				case 'Update only':
					$response = $client->updateLeads( $lead );
					break;

				case 'Always Create':
					$response = $client->createDuplicateLeads( $lead );
					break;

				default:
					$response = $client->createOrUpdateLeads( $lead );
					break;

			}

			return $response;

		}

		return false;

	}

	/**
	 * encrypt
	 *
	 * @see http://wordpress.stackexchange.com/questions/25062/
	 *
	 * @param string $input_string Text to be encrypted
	 * @param string $key Encryption key (use `AUTH_KEY`)
	 *
	 * @return string Encrypted data
	 */

	private function encrypt( $input_string, $key ) {
		$iv_size = mcrypt_get_iv_size( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB );
		$iv = mcrypt_create_iv( $iv_size, MCRYPT_RAND );
		$h_key = hash( 'sha256', $key, TRUE );
		return base64_encode( mcrypt_encrypt(
			MCRYPT_RIJNDAEL_256,
			$h_key,
			$input_string,
			MCRYPT_MODE_ECB,
			$iv
		) );
	}

	/**
	 * decrypt
	 *
	 * @see http://wordpress.stackexchange.com/questions/25062/
	 *
	 * @param string $encrypted_input_string Encrypted data
	 * @param string $key Encryption key (use `AUTH_KEY`)
	 *
	 * @return string Plaintext data
	 */

	private function decrypt( $encrypted_input_string, $key ) {
		$iv_size = mcrypt_get_iv_size( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB );
		$iv = mcrypt_create_iv( $iv_size, MCRYPT_RAND );
		$h_key = hash( 'sha256', $key, TRUE );
		return trim( mcrypt_decrypt(
			MCRYPT_RIJNDAEL_256,
			$h_key,
			base64_decode( $encrypted_input_string ),
			MCRYPT_MODE_ECB,
			$iv
		) );
	}

}