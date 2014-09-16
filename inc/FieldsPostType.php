<?php

/**
 * FieldsPostType
 *
 * Custom Post Type for form fields
 */

namespace RichJenks\MarketoLeads;

class FieldsPostType {

	/**
	 * @var string Post Type
	 */

	private $post_type = 'marketoleads_fields';

	/**
	 * __construct
	 *
	 * Start the magic...
	 */

	public function __construct() {

		// Init Actions
		add_action( 'init', function() {

			// Register post type
			register_post_type( $this->post_type, $this->get_args() );

			// Rename stuff
			\WPUtils\Posts::rename( array(
				'Enter title here' => 'Marketo Field',
				'Title'            => 'Field',
				'Excerpt'          => 'Form field name(s)',
			), $this->post_type );

		} );

		// Sanitize data on save
		add_filter( 'wp_insert_post_data', function( $data , $postarr ) {

			// Title
			$data['post_title'] = trim( $data['post_title'] );

			// Excerpt
			$lines = explode( "\n", $data['post_excerpt'] );
			foreach ( $lines as $key => $line ) {
				$lines[ $key ] = trim( $line );
				if ( $lines[ $key ] === '' ) unset( $lines[ $key ] );
			}
			$data['post_excerpt'] = implode( "\n", $lines);

			return $data;

		}, 99, 2 );

	}

	/**
	 * get_args
	 *
	 * @return array Hard-coded arguments for post type
	 */

	private function get_args() {
		return array(
			'labels'             => $this->get_labels(),
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'menu_icon'          => 'dashicons-forms',
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => false,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'excerpt' ),
		);
	}

	/**
	 * get_labels
	 *
	 * @return array Hard-coded labels for post type
	 */

	private function get_labels() {
		return array(
			'name'               => _x( 'Fields', 'post type general name', 'your-plugin-textdomain' ),
			'singular_name'      => _x( 'Field', 'post type singular name', 'your-plugin-textdomain' ),
			'menu_name'          => _x( 'Marketo Leads', 'admin menu', 'your-plugin-textdomain' ),
			'name_admin_bar'     => _x( 'Field', 'add new on admin bar', 'your-plugin-textdomain' ),
			'add_new'            => _x( 'Add New', 'Field', 'your-plugin-textdomain' ),
			'all_items'          => __( 'All Fields', 'your-plugin-textdomain' ),
			'add_new_item'       => __( 'Add Field', 'your-plugin-textdomain' ),
			'new_item'           => __( 'New Field', 'your-plugin-textdomain' ),
			'edit_item'          => __( 'Edit Field', 'your-plugin-textdomain' ),
			'view_item'          => __( 'View Field', 'your-plugin-textdomain' ),
			'search_items'       => __( 'Search Fields', 'your-plugin-textdomain' ),
			'parent_item_colon'  => __( 'Parent Field:', 'your-plugin-textdomain' ),
			'not_found'          => __( 'No Fields found.', 'your-plugin-textdomain' ),
			'not_found_in_trash' => __( 'No Fields found in Trash.', 'your-plugin-textdomain' ),
		);
	}

}

new FieldsPostType;