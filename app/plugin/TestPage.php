<?php

/**
 * TestPage
 *
 * Creates page for testing API settings
 */

namespace RichJenks\MarketoLeads;

class TestPage extends Options {

	/**
	 * @var string Post Type
	 */

	private $post_type = 'rj_ml_cpt_fields';

	/**
	 * __construct
	 *
	 * Start the magic...
	 */

	public function __construct() {

		// Add submenu page
		// add_action( 'admin_menu', function() {
		// 	add_submenu_page(
		// 		'edit.php?post_type=' . $this->post_type,
		// 		'Test API',
		// 		'Test API',
		// 		'manage_options',
		// 		'rj_ml_test',
		// 		array( $this, 'content' )
		// 	);
		// } );

		// Get current values
		$this->options = $this->get_options();

		// Check if options were submitted
		if ( isset( $_POST['rj_ml_run_test'] ) ) {
			$result = $this->test_api();
			if ( !$result['success'] ) {
				$this->add_notice( 'error', $result['message'] );
			} else {
				$this->add_notice( 'updated', $result['message'] );
			}
		}

	}

	/**
	 * content
	 *
	 * Render view for Options page
	 */

	public function content() { require 'TestView.php'; }

	/**
	 * test_api
	 *
	 * @return bool Whether API is succesful or not
	 */

	private function test_api() {

		// Test Munckin ID
		$url = sprintf( 'https://%s.mktorest.com', $this->options['munchkin_id'] );
		@$headers = get_headers( $url );
		if ( !$headers ) {
			return array(
				'success' => false,
				'message' => 'Incorrect Munchkin ID&thinsp;&mdash;&thinsp;<a href="edit.php?post_type=rj_ml_cpt_fields&page=rj_ml_options">check settings</a>!',
			);
		}

		// Create lead
		$this->options['client_id'] = '598ec513-51fb-49bc-8b44-6633b0637ddb';
		@$response = $this->create_lead( array( 'email' => 'dummy@email.com' ), $this->options );

		// var_dump( $url );
		// var_dump( $headers );
		var_dump( $response );

		return array(
			'success' => true,
			// 'message' => 'Passed all tests&thinsp;&mdash;&thinsp;everything should work correctly!',
			'message' => 'Not failed yet&hellip;',
		);

		// try {
		// 	$response = $this->create_lead( array( 'email' => '123456@domain.com' ), $this->options );
		// 	var_dump( $response );
		// } catch (Exception $e) {
		// 	echo $e;
		// }
	}

}
