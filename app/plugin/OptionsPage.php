<?php

/**
 * OptionsPage
 *
 * Creates page for options & API test
 */

namespace RichJenks\MarketoLeads;

class OptionsPage extends Options {

	/**
	 * @var string Post Type
	 */

	private $post_type = 'rj_ml_cpt_fields';

	/**
	 * @var array Options array
	 */

	private $options;

	/**
	 * __construct
	 *
	 * Start the magic...
	 */

	public function __construct() {

		// Add submenu page
		add_action( 'admin_menu', function() {
			add_submenu_page(
				'edit.php?post_type=' . $this->post_type,
				'Options',
				'Options',
				'manage_options',
				'rj_ml_options',
				array( $this, 'content' )
			);
		} );

		// Check if options were submitted
		if ( isset( $_POST['rj_ml_options_submitted'] ) ) {

			// Save options
			$this->set_options( $_POST );

			// Show notice
			$this->add_notice( 'updated', 'Options updated!' );

		}

		// Get current values
		$this->options = $this->get_options();

	}

	/**
	 * content
	 *
	 * Render view for Options page
	 */

	public function content() { require 'OptionsView.php'; }

}
