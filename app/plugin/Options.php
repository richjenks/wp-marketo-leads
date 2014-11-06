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
		$default = array(
			'client_id'     => '',
			'client_secret' => '',
			'munchkin_id'   => '',
			'status'        => 'Disabled',
		);
		return $this->deobfuscate( get_option( 'rj_ml_options', $this->obfuscate( $default ) ) );
	}

	/**
	 * set_options
	 *
	 * @param array $options Options array
	 */

	protected function set_options( $options ) {
		update_option( 'rj_ml_options', $this->obfuscate( array(
			'client_id'     => $options['client_id'],
			'client_secret' => $options['client_secret'],
			'munchkin_id'   => $options['munchkin_id'],
			'status'        => $options['status'],
		) ) );
	}

	/**
	 * obfuscate
	 *
	 * Obfuscates a variable in preparation for storage
	 *
	 * @param mixed $input Variable to be prepared
	 * @return string String ready for storage
	 */

	private function obfuscate( $input ) {
		$input = serialize( $input );
		$input = sanitize_text_field( $input );
		$input = base64_encode( $input );
		return $input;
	}

	/**
	 * deobfuscate
	 *
	 * Deobfuscates a variable retrieved from storage
	 *
	 * @param mixed $input Variable to be prepared
	 * @return string String ready for storage
	 */

	private function deobfuscate( $input ) {
		$input = base64_decode( $input );
		$input = sanitize_text_field( $input );
		$input = unserialize( $input );
		return $input;
	}

}