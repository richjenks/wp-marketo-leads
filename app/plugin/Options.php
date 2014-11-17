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
		$default = array(

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
			),

			// Global fields
			'global_fields' => '',

		);

		// Get current options or default
		$options = json_decode( get_option( 'rj_ml_options', json_encode( $default ) ) );

		// Return object of merged properties (in case options were added)
		return (object) array_merge( $default, (array) $options );

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