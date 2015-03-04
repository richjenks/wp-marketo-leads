<?php

/**
 * start
 *
 * Starts the plugin & makes things happen!
 */

// Composer
require 'vendor/autoload.php';

/**
 * Don't do anything until `plugins_loaded`
 * so other devs can use this plugin's actions and filters
 */
add_action( 'plugins_loaded', function () {

	// Enqueue scripts placeholder
	// add_action( 'wp_enqueue_scripts', function () {
	// 	$name = 'wp-marketo-leads-scripts';
	// 	wp_register_script(
	// 		$name,
	// 		plugins_url( 'assets/scripts.js', __FILE__ ),
	// 		array('jquery'),
	// 		'1.1',
	// 		true
	// 	);
	// 	wp_enqueue_script( $name );
	// } );

	// Register Post Type
	new RichJenks\MarketoLeads\FieldsPostType;

	// Add Options page
	new RichJenks\MarketoLeads\OptionsPage;

	// Add API Test page
	new RichJenks\MarketoLeads\TestPage;

	// Grab posted forms & create lead
	new RichJenks\MarketoLeads\Lead;

} );
