<?php

/**
 * Lead
 *
 * Grabs post data and field config then creates a lead
 */

namespace RichJenks\MarketoLeads;

class Lead extends Options {

	/**
	 * @var array Sanitized $_POST array
	 */

	private $post;

	/**
	 * @var array Field post data
	 */

	private $fields;

	/**
	 * @var array Lead data
	 */

	private $lead;

	/**
	 * __construct
	 *
	 * Don't do anything until `wp_loaded`!
	 *
	 * Late enough for everything to be available
	 * but early enough to send headers if needed
	 */

	public function __construct() {

		// Used to ensure we only run once
		$GLOBALS['rj_ml_ran'] = false;

		// When to run...
		$hooks = explode( "\n", $this->get_options( 'hooks' ) );
		$hooks = apply_filters( 'rj_ml_hooks', $hooks );

		/**
		 * Start for each hook
		 * Won't actually run if has already done so
		 */
		foreach ( $hooks as $hook ) {
			add_action( trim( $hook ), function () use ( $hook ) {
				$this->start( $hook );
			} );
		}

	}

	/**
	 * start
	 *
	 * Starts the lead creation process
	 * Hooks added in constructor
	 */

	public function start() {

		/**
		 * Only run if:
		 *
		 * 1. We haven't run already
		 * 2. Data is posted
		 * 3. Not on login or registration page
		 */
		if (
			!$GLOBALS['rj_ml_ran']
			&& !empty( $_POST )
			&& $GLOBALS['pagenow'] !== 'wp-login.php'
			&& $GLOBALS['pagenow'] !== 'wp-register.php'
		) {

			// Sanitize $_POST
			$this->post = $this->post2name( $_POST );

			// Check if plugin or debug mode is enabled
			if ( $this->get_options( 'status' ) === 'Enabled' || $this->get_options( 'debug' ) === 'Enabled' ) {

				// Get field posts
				$posts = get_posts( array(
					'posts_per_page' => -1,
					'post_type'      => 'rj_ml_cpt_fields',
				) );

				// Sanitize field data so it's usable
				$this->fields = $this->sanitize_posts( $posts );

				// Construct Lead data
				$this->lead = $this->construct_lead( $this->fields, $this->post );

				// From here on, options are in a flat var for filtering, etc.
				$options = $this->get_options();

				// Filter data
				$this->lead = apply_filters( 'rj_ml_lead', $this->lead );
				$options    = apply_filters( 'rj_ml_options', $options );

				// If plugin enabled, create lead
				if ( $options['status'] === 'Enabled' ) {

					// Before create lead hook
					do_action( 'rj_ml_before_create_lead', $this->lead, $options );

					// Create the lead
					$this->create_lead( $this->lead, $options );

					// After create lead hook
					do_action( 'rj_ml_after_create_lead', $this->lead, $options );

					// So it only runs once!
					$GLOBALS['rj_ml_ran'] = true;

				}

				// If debug enabled, show debug info
				if ( $options['debug'] === 'Enabled' && \is_user_logged_in() )  {
					require 'DebugView.php';
					die;
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
						if ( strpos( $element_name, '|' ) !== false ) {
							$parts = explode( '|' , $element_name );
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
		$lead = array_merge( $this->get_default_fields( $this->options['default_fields'] ), $lead );

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
	 * post2name
	 *
	 * Converts a $_POST array to a flat array of the form element `name`s which would have produced it.
	 *
	 * For example, the PHP array:
	 *
	 *     'foo' => 'bar',
	 *     'baz' => [
	 *         'foo' => 'bar',
	 *     ],
	 *
	 * would turn into:
	 *
	 *     'foo' => 'bar',
	 *     'baz[foo]' => 'bar',
	 *
	 * @param  array $array Array to be converted
	 * @return array Flat array of HTML `name`s
	 */

	private function post2name( $array ) {
		$result = array();
		$array = $this->flatten_array( $array );
		foreach ( $array as $key => $value ) {
			$parts = explode( '.', $key );
			$i = 0;
			$new_key = '';
			foreach ( $parts as $part ) {
				if ( $i !== 0 ) $part = '[' . $part . ']';
				$new_key .= $part;
				$i++;
			}
			$i = 0;
			$result[ $new_key ] = $value;
		}
		return $result;
	}

	/**
	 * flatten_array
	 *
	 * Turns a multi-dimensional array into a flat one separated by dots
	 *
	 * @param array $array Array to be flattened
	 * @param string $prefix Received previous value from recursive call
	 *
	 * @return array Flat array
	 */

	private function flatten_array( $array, $prefix = '' ) {
		$result = array();
		foreach( $array as $key => $value ) {
			if ( is_array( $value ) ) {
				$result = $result + $this->flatten_array( $value, $prefix . $key . '.' );
			} else {
				$result[ $prefix . $key ] = $value;
			}
		}
		return $result;
	}

}
