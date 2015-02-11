<?php

/*

Plugin Name: Site Settings
Plugin URI: https://github.com/benhuson/site-settings
Description: Manage custom site settings and global content fields.
Author: Ben Huson
Author URI: https://github.com/benhuson/
Version: 0.1
Tested up to: 4.1
Minimum WordPress Version Required: 3.9

Released under the GPL:
http://www.opensource.org/licenses/gpl-license.php

*/

define( 'SITE_SETTINGS_VERSION', '1.0' );
define( 'SITE_SETTINGS_DB_VERSION', '1.0' );
define( 'SITE_SETTINGS_FILE', __FILE__ );
define( 'SITE_SETTINGS_TEXTDOMAIN', 'site-settings' );

// Global functionality
require_once( plugin_dir_path( SITE_SETTINGS_FILE ) . 'includes/global.php' );

// Admin Only
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
	require_once( plugin_dir_path( SITE_SETTINGS_FILE ) . 'includes/admin.php' );
}

// I18n
function site_settings_load_plugin_textdomain() {
	load_plugin_textdomain( SITE_SETTINGS_TEXTDOMAIN, false, dirname( plugin_basename( SITE_SETTINGS_FILE ) ) . '/languages/' );
}

add_action( 'plugins_loaded', 'site_settings_load_plugin_textdomain' );
