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

}