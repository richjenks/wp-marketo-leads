<?php

/**
 * Lead
 *
 * Grabs post data and field config then creates a lead
 */

namespace RichJenks\MarketoLeads;

class Lead {

	/**
	 * @var array Field posts
	 */

	private $fields;

	/**
	 * @var array Lead data
	 */

	private $lead;

	/**
	 * __construct
	 *
	 * Start the magic...
	 */

	public function __construct() {

		if ( !empty( $_POST ) && !is_admin() ) {

			// Get field posts
			$posts = get_posts( array(
				'posts_per_page' => PHP_INT_MAX,
				'post_type'      => 'rj_ml_cpt_fields',
			) );

			// Sanitize field data so it's usable
			$this->fields = $this->sanitize_posts( $posts );

			// Construct Lead data
			$this->lead = $this->construct_lead( $this->fields, $_POST );

			// Debug?
			$this->debug();

			// Push $this->lead to API
			$client = new \GuzzleHttp\Client;

		}

	}

	/**
	 * debug
	 *
	 * Outputs data
	 */

	private function debug() {

		// Post data
		echo '<h1>$_POST</h1>';
		var_dump( $_POST );

		// Field data
		echo '<h1>Fields</h1>';
		var_dump( $this->fields );

		// Lead data
		echo '<h1>Lead</h1>';
		var_dump( $this->lead );

		// Stop execution to view before redirect
		die;

	}

	/**
	 * sanitize_posts
	 *
	 * When given results of `get_posts` will return usable array
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

		return $lead;

	}

}

new Lead;