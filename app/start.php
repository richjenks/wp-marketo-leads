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

	// Register Post Type
	new RichJenks\MarketoLeads\FieldsPostType;

	// Add Options page
	new RichJenks\MarketoLeads\OptionsPage;

	// Add API Test page
	new RichJenks\MarketoLeads\TestPage;

	// Grab posted forms & create lead
	new RichJenks\MarketoLeads\Lead;

} );
