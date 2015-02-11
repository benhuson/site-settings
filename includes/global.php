<?php

/**
 * Global Functionality
 */

if ( ! class_exists( 'Site_Settings' ) ) {

	class Site_Settings {

		/**
		 * Get (Option)
		 *
		 * @param   string       $option  Setting name/key.
		 * @return  string|null           Setting value.
		 */
		public static function get( $option ) {

			$options = self::get_options();

			if ( isset( $options[ $option ] ) ) {
				return $options[ $option ];
			}

			return null;

		}

		/**
		 * Get Options
		 *
		 * @return  array  Site settings.
		 */
		public static function get_options() {

			return (array) get_option( 'site_settings_options' );

		}

	}

}
