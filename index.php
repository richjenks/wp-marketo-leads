<?php

/**
 * Plugin Name: Marketo Leads
 * Plugin URI: http://gitlab.cmoglobal.com/cmo/marketo-leads/
 * Description: Allows WordPress forms to create leads within Marketo
 * Version: 1.0
 * Author: Rich Jenks <rich@richjenks.com>
 * Author URI: http://richjenks.com
 * License: GPL2
 */

// WP Utils
require __DIR__ . '/../wp-utils/WPUtils.php';
if ( !defined( 'WPUTILS' ) || version_compare( WPUTILS, '1.0.0' ) ) die( 'Marketo Leads requires WPUtils 1.0.0 or above' );

// Libraries
require __DIR__ . '/inc/API.php';

// Objects
require __DIR__ . '/inc/FieldsPostType.php';
require __DIR__ . '/inc/OptionsPage.php';
require __DIR__ . '/inc/Lead.php';