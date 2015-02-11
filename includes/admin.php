<?php

/**
 * Admin Functionality
 */

if ( ! class_exists( 'Site_Settings_Admin' ) ) {

	// Settings screen functionality
	require_once( plugin_dir_path( SITE_SETTINGS_FILE ) . 'includes/admin/settings-screen.php' );

	add_action( 'current_screen', array( 'Site_Settings_Admin', 'plugin_row' ) );

	class Site_Settings_Admin {

		/**
		 * Plugin Row
		 *
		 * Adds settings and GitHub link to plugin row.
		 */
		public static function plugin_row() {

			// Only if on plugins screen
			$screen = get_current_screen();
			if ( ! $screen || 'plugins' != $screen->id ) {
				return;
			}

			require_once( plugin_dir_path( SITE_SETTINGS_FILE ) . 'includes/admin/plugin-row.php' );

			$myplugin_plugin_row = new Site_Settings_Plugin_Row( plugin_basename( SITE_SETTINGS_FILE ) );

			$myplugin_plugin_row->add_action( __( 'Settings', SITE_SETTINGS_TEXTDOMAIN ), admin_url( '/options-general.php?page=site-settings' ) );
			$myplugin_plugin_row->add_meta( __( 'Documentation', SITE_SETTINGS_TEXTDOMAIN ), 'https://github.com/benhuson/site-settings/wiki' );
			$myplugin_plugin_row->add_meta( __( 'GitHub', SITE_SETTINGS_TEXTDOMAIN ), 'https://github.com/benhuson/site-settings' );

		}

	}

}
