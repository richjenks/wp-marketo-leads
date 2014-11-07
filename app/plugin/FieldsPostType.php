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

	private $post_type = 'rj_ml_cpt_fields';

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

		} );


		// Things specific to post type
		if ( $this->get_post_type() === $this->post_type ) {

			// Rename things
			$this->rename();

			// Sanitize data on save
			$this->sanitize_save();

		}

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
			'name'               => _x( 'Marketo Fields', 'post type general name', 'richjenks_marketoleads' ),
			'singular_name'      => _x( 'Marketo Field', 'post type singular name', 'richjenks_marketoleads' ),
			'menu_name'          => _x( 'Marketo Fields', 'admin menu', 'richjenks_marketoleads' ),
			'name_admin_bar'     => _x( 'Marketo Field', 'add new on admin bar', 'richjenks_marketoleads' ),
			'add_new'            => _x( 'Add New', 'Field', 'richjenks_marketoleads' ),
			'all_items'          => __( 'All Fields', 'richjenks_marketoleads' ),
			'add_new_item'       => __( 'Add Field', 'richjenks_marketoleads' ),
			'new_item'           => __( 'New Field', 'richjenks_marketoleads' ),
			'edit_item'          => __( 'Edit Field', 'richjenks_marketoleads' ),
			'view_item'          => __( 'View Field', 'richjenks_marketoleads' ),
			'search_items'       => __( 'Search Fields', 'richjenks_marketoleads' ),
			'parent_item_colon'  => __( 'Parent Field:', 'richjenks_marketoleads' ),
			'not_found'          => __( 'No Fields found.', 'richjenks_marketoleads' ),
			'not_found_in_trash' => __( 'No Fields found in Trash.', 'richjenks_marketoleads' ),
		);
	}

	/**
	 * get_post_type
	 *
	 * @return string Post type for the current page in admin
	 */

	private function get_post_type() {
		if ( isset( $_GET['post_type'] ) ) {
			return $_GET['post_type'];
		} elseif ( isset( $_GET['post'] ) ) {
			return get_post_type( $_GET['post'] );
		}
	}

	/**
	 * rename
	 *
	 * Renames thing for post type
	 */

	private function rename() {

		// Rename Title column heading
		add_filter( 'manage_' . $this->post_type . '_posts_columns', function ( $columns ) {
			$columns['title'] = 'Field';
			return $columns;
		} );

		// Others
		add_filter( 'gettext', function( $translation, $text ) {
			$strings = array(
				'Enter title here' => 'Marketo field',
				'Excerpt'          => 'Form fields',
				'Excerpts are optional hand-crafted summaries of your content that can be used in your theme. <a href="http://codex.wordpress.org/Excerpt" target="_blank">Learn more about manual excerpts.</a>' => '<code>name</code> or <code>id</code> of form field in HTML. One per line, spaces are ignored. You can add a comment after a slash to remind you where the field came from, e.g. <code>field_name / Contact form</code>.',
			);
			foreach ( $strings as $old => $new ) if ( $text === $old ) return $new;
			return $translation;
		}, 10, 2 );

	}

	/**
	 * sanitize_save
	 *
	 * Sanitize data on save
	 */

	private function sanitize_save() {

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

}
