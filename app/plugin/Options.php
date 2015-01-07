<?php

/**
 * Options
 *
 * Interface for serialized options
 */

namespace RichJenks\MarketoLeads;

class Options {

	/**
	 * get_options
	 *
	 * @return object Plugin options
	 */

	protected function get_options() {

		// Default options
		$defaults = array(

			// Marketo API
			'client_id'     => '',
			'client_secret' => '',
			'munchkin_id'   => '',

			// Plugin Options
			'status' => 'Disabled',
			'debug'  => 'Disabled',
			'action' => 'Create/Update',

			// Default Fields
			'default_fields' => '',

		);

		// Get current options as array
		$options = json_decode( get_option( 'rj_ml_options', '[]' ), true );

		// Merge options & defaults
		$options = array_replace_recursive( $defaults, $options );

		// Return full options as an object
		return json_decode( json_encode( $options ) );

	}

	/**
	 * set_options
	 *
	 * @param array $options Options array
	 */

	protected function set_options( $options ) {
		update_option( 'rj_ml_options', json_encode( $options ) );
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

}