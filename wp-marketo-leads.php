<?php

/**
 * Plugin Name: Marketo Leads
 * Plugin URI: https://github.com/richjenks/wp-marketo-leads
 * Description: Create a lead in Marketo from any form!
 * Version: 1.0
 * Author: Rich Jenks <rich@richjenks.com>
 * Author URI: http://richjenks.com
 * License: GPL2
 */

require 'app/start.php';

if ( !is_admin() && $GLOBALS['pagenow'] !== 'wp-login.php' && $GLOBALS['pagenow'] !== 'wp-register.php' ) {
	echo 'Front';
} else {
	echo 'Back';
}