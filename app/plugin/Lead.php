<?php

/**
 * Lead
 *
 * Grabs post data and field config then creates a lead
 */

namespace RichJenks\MarketoLeads;

class Lead extends Options {

	/**
	 * @var array Field post data
	 */

	private $fields;

	/**
	 * @var array Lead data
	 */

	private $lead;

	/**
	 * @var array API options
	 */

	private $options;

	/**
	 * __construct
	 *
	 * Don't do anything until `wp_loaded`!
	 *
	 * Late enough for everything to be available
	 * but early enough to send headers if needed
	 */

	public function __construct() {

		add_action( 'wp_loaded', function () {

			if (
				!empty( $_POST )
				&& !is_admin()
				&& $GLOBALS['pagenow'] !== 'wp-login.php'
				&& $GLOBALS['pagenow'] !== 'wp-register.php'
			) {

				// Get API options
				$this->options = $this->get_options();

				// Check if plugin or debug mode is enabled
				if ( $this->options->status === 'Enabled' || $this->options->debug === 'Enabled' ) {

					// Get field posts
					$posts = get_posts( array(
						'posts_per_page' => PHP_INT_MAX,
						'post_type'      => 'rj_ml_cpt_fields',
					) );

					// Sanitize field data so it's usable
					$this->fields = $this->sanitize_posts( $posts );

					// Construct Lead data
					$this->lead = $this->construct_lead( $this->fields, $_POST );

					// If plugin enabled, create lead
					if ( $this->options->status === 'Enabled' ) {
						$this->create_lead( $this->lead, $this->options );
					}

					// If debug enabled, show debug info
					if ( $this->options->debug === 'Enabled' && \is_user_logged_in() )  {
						require 'DebugView.php';
						die;
					}

				}

			}

		} );

	}

	/**
	 * sanitize_posts
	 *
	 * When given results of `get_posts` will return usable array
	 * Note this is WordPress posts, not $_POST data
	 *
	 * @param array $posts Results of `get_posts`
	 * @return array Array of marketo_field => element_name
	 */

	private function sanitize_posts( $posts ) {

		// Array to return
		$fields = array();

		// Extract title (Marketo field) & excerpt (element names)
		foreach ( $posts as $key => $post ) {
			$fields[ $post->post_title ] = explode( "\n", $post->post_excerpt );
		}

		return $fields;

	}

	/**
	 * construct_lead
	 *
	 * When given list of registered fields and array of submitted form fields,
	 * constructs an array of data which can be send to Marketo
	 *
	 * @param array $fields Array of marketo_field => element_names
	 * @param array $posted Array of submitted data ($_POST superglobal)
	 *
	 * @return array List of marketo_field => submitted_value
	 */

	private function construct_lead( $fields, $posted ) {

		/**
		 * @todo Flatten array here to accommodate for multidimentional field names
		 */

		// Array to return
		$lead = array();

		// Iterate through post data to get lead data
		foreach ( $posted as $posted_key => $posted_value ) {

			// Ignore WordPress fields
			if ( substr( $posted_key, 0, 3 ) !== '_wp' ) {

				/**
				 * Iterate through marketo fields for a match
				 *
				 * $fields is an array with a marketo field as a key and an
				 * array of element names as a value. Looping through these, we
				 * can then loop through the inner arrays of element names to
				 * see if one of the expected field names matches on that was
				 * posted.
				 *
				 * Then it's a simple matter of populating an array of marketo
				 * field => posted value.
				 */
				foreach ( $fields as $marketo_field => $element_names ) {
					foreach ( $element_names as $element_name ) {

						// Remove comment (if present)
						if ( strpos( $element_name, '/' ) !== false ) {
							$parts = explode( '/' , $element_name );
							$element_name = trim( $parts[0] );
						}

						// Check for match
						if ( $posted_key === $element_name ) {

							// Add field to lead
							$lead[ $marketo_field ] = $posted_value;

							// Pretty-print arrays
							if ( is_array( $lead[ $marketo_field ] ) ) {
								$string = '';
								foreach ( $lead[ $marketo_field ] as $key => $value) {
									$string .= $key . ': ' . $value . ', ';
								}
								$lead[ $marketo_field ] = rtrim( $string, ', ' );
							}

						}

					}
				}

			}

		}

		// Add default fields to lead — submitted values take priority!
		$lead = array_merge( $this->get_default_fields( $this->options->default_fields ), $lead );

		return $lead;

	}

	/**
	 * get_default_fields
	 *
	 * Constructs array of default fields to be added to lead data
	 *
	 * @param string $fields Value of default fields textarea in options page
	 * @return array 'marketo_field' => 'field_value'
	 */

	private function get_default_fields( $fields ) {

		$global_fields = array();

		// Split lines
		$fields = explode( "\n", $fields );

		// Get keys and values
		foreach ( $fields as $field ) {

			// Check line contains pipe
			if ( strpos( $field, '|' ) !== false ) {

				$parts = explode( '|', $field );
				$parts[0] = trim( $parts[0] );
				$parts[1] = trim( $parts[1] );

				// Only store if both key and value aren't empty
				if ( $parts[0] !== '' && $parts[1] !== '' )
					$global_fields[ $parts[0] ] = $parts[1];

			}

		}

		return $global_fields;

	}

	/**
	 * flatten_array
	 *
	 * Generates a flat array from a multidimentional array by concatenating keys
	 *
	 * @see http://stackoverflow.com/a/9546215/1562799
	 *
	 * @param array $multi Multidimentional array
	 * @return array Flat array
	 */

	private function flatten_array( $multi, $prefix = '' ) {

		$flat = array();

		// Flatten array by recursively running this function
		foreach ( $multi as $key => $value ) {

			// TEST
			$suffix = '';

			if ( is_array( $value ) ) {

				// $suffix = ( substr( $prefix, -1 ) === ']' ) ? '][' : '[';

				// If array, call this function to flatten it
				$flat = $flat + $this->flatten_array( $value, $prefix . $key . $suffix );

			} else {

				// If prefix exists, it was originally an array item, so close it
				$suffix = ( $prefix !== '' ) ? ']' : '';

				// Not array, so just set it as is
				$flat[ $prefix . $key . $suffix ] = $value;

			}

		}

		return $flat;

	}

	/**
	 * create_lead
	 *
	 * Sends the lead to the Marketo API
	 */

	private function create_lead( $lead, $options ) {

		// Is there a lead?
		if ( count( $lead ) !== 0 ) {

			// API Options
			$api_options = array(
				'client_id'     => $options->client_id,
				'client_secret' => $options->client_secret,
				'munchkin_id'   => $options->munchkin_id,
			);

			// Create API client using Options class
			$client = \CSD\Marketo\Client::factory( $api_options );

			// Construct leads array (must be array of lead arrays for API)
			$lead = array( $lead );

			// How to handle lead?
			switch ( $options->action ) {

				case 'Create only':
					$client->createLeads( $lead );
					break;

				case 'Update only':
					$client->updateLeads( $lead );
					break;

				case 'Always Create':
					$client->createDuplicateLeads( $lead );
					break;

				default:
					$client->createOrUpdateLeads( $lead );
					break;

			}

		}

	}

}
