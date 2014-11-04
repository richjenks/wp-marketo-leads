<?php

/**
 * start
 *
 * Starts the plugin & makes things happen!
 */

// Composer
require 'vendor/autoload.php';

// Register Post Type
new RichJenks\MarketoLeads\FieldsPostType;

// Add Options page
new RichJenks\MarketoLeads\OptionsPage;

// Grab posted forms & create lead
new RichJenks\MarketoLeads\Lead;
