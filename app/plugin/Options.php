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
	 * @return array Options array
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

			// Extra Fields
			'fields' => array(
				'current_url' => array(
					'status'        => 'Disabled',
					'marketo_field' => '',
				),
				'ip_address' => array(
					'status'        => 'Disabled',
					'marketo_field' => '',
				),
			),

			// Global fields
			'global_fields' => '',

		);

		// Get current options or default
		$options = json_decode( get_option( 'rj_ml_options', new \stdClass ) );

		// Convert options from object to array
		$options = json_decode( json_encode( $options ), true );

		// Merge options & defaults
		$options = array_replace_recursive( $defaults, $options );

		// Return constructed options as an object
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

}