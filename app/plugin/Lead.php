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
	 * Start the magic...
	 */

	public function __construct() {

		if ( !empty( $_POST ) && !is_admin() ) {

			// Get API options
			$this->options = $this->get_options();

			// Check if plugin is enabled
			if ( $this->options->status === 'Enabled' ) {

				// Get field posts
				$posts = get_posts( array(
					'posts_per_page' => PHP_INT_MAX,
					'post_type'      => 'rj_ml_cpt_fields',
				) );

				// Sanitize field data so it's usable
				$this->fields = $this->sanitize_posts( $posts );

				// Construct Lead data
				$this->lead = $this->construct_lead( $this->fields, $_POST );

				// Show debug info?
				if ( $this->options->debug === 'Enabled' ) {
					require 'DebugView.php';
					die;
				}

				// Is there a lead?
				if ( count( $this->lead ) !== 0 ) {

					// API Options
					$options = array(
						'client_id'     => $this->options->client_id,
						'client_secret' => $this->options->client_secret,
						'munchkin_id'   => $this->options->munchkin_id,
					);

					// Create API client using Options class
					$client = \CSD\Marketo\Client::factory( $options );

					// Construct leads array (must be array of lead arrays for API)
					$this->lead = array( $this->lead );

					// Create the lead!
					$client->createOrUpdateLeads( $this->lead, 'email' );

				}

			}

		}

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

		// Add extra fields to lead — submitted values take priority!
		$lead = array_merge( $this->get_extra_fields( $this->options->fields, $_POST ), $lead );

		return $lead;

	}

	/**
	 * get_extra_fields
	 *
	 * Constructs array of extra fields to be added to lead data
	 *
	 * @param array $fields Extra fields configured in Options
	 * @param array $post   $_POST array
	 *
	 * @return array Array of extra fields, key being Marketo field
	 */

	private function get_extra_fields( $fields, $post ) {
		$extra_fields = array();
		foreach ( $fields as $field => $field_options )
			if ( $field_options->status === 'Enabled' )
				$extra_fields[ $field_options->marketo_field ] = $this->get_extra_field_value( $field );
		return $extra_fields;
	}

	/**
	 * get_extra_field_value
	 *
	 * Determines the value of extra fields which all have bespoke purpose
	 *
	 * @param string $field Name of extra field
	 * @return string Value of custom field for this submission
	 */

	private function get_extra_field_value( $field ) {
		switch ( $field ) {
			case 'current_url':
				$protocol  = @( $_SERVER["HTTPS"] != 'on' ) ? 'http://' : 'https://';
				return $protocol . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		}
	}

}
