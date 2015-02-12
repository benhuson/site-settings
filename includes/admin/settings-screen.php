<?php

/**
 * Admin Functionality
 */

if ( ! class_exists( 'Site_Settings_Admin_Screen' ) ) {

	add_action( 'admin_menu', array( 'Site_Settings_Admin_Screen', 'options_pages' ) );
	add_action( 'admin_init', array( 'Site_Settings_Admin_Screen', 'register_settings' ) );

	class Site_Settings_Admin_Screen {

		/**
		 * Options Pages
		 */
		public static function options_pages() {

			add_options_page( __( 'Site Settings', SITE_SETTINGS_TEXTDOMAIN ), __( 'Site Settings', SITE_SETTINGS_TEXTDOMAIN ), 'manage_options', 'site-settings', array( 'Site_Settings_Admin_Screen', 'site_settings_page' ) );

		}

		/**
		 * Site Settings Page
		 */
		public static function site_settings_page() {

			?>

			<div>
				<h2><?php _e( 'Site Settings', SITE_SETTINGS_TEXTDOMAIN ); ?></h2>
				<p><?php _e( 'Manage fields and settings relating to your site content.', SITE_SETTINGS_TEXTDOMAIN ); ?></p>
				<p><?php printf( __( 'To find out how to add sections and fields, visit <a%s>the documentation</a>.', SITE_SETTINGS_TEXTDOMAIN ), ' href="https://github.com/benhuson/site-settings/wiki"' ); ?></p>

				<form action="options.php" method="post">
					<?php settings_fields( 'site_settings_options' ); ?>
					<?php do_settings_sections( 'site_settings' ); ?>
					<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_attr_e( 'Save Changes' ); ?>"></p>
				</form>

			</div>

			<?php

		}

		/**
		 * Register Settings
		 */
		public static function register_settings() {

			register_setting( 'site_settings_options', 'site_settings_options', array( 'Site_Settings_Admin_Screen', 'validate_settings_options' ) );

			// Add a default 'main' section.
			self::add_section( 'main', '' );

			do_action( 'site_settings_register' );

		}

		/**
		 * Add Settings Section
		 *
		 * Don't add any section description.
		 */
		public static function add_settings_section() {
		}

		/**
		 * Add Section
		 *
		 * @param  string  $section  Section name/ID.
		 * @param  string  $title    Section display title.
		 */
		public static function add_section( $section, $title ) {

			$section = self::get_section_id( $section );

			add_settings_section( $section, $title, array( 'Site_Settings_Admin_Screen', 'add_settings_section' ), 'site_settings' );

		}

		/**
		 * Add Field
		 *
		 * @param  string  $name   Setting name/key.
		 * @param  string  $title  Setting display title.
		 * @param  array   $args   Setting parameters.
		 */
		public static function add_field( $name, $title, $args = array() ) {

			$args['name'] = $name;

			// Set section to main if none provided.
			if ( ! isset( $args['section'] ) ) {
				$args['section'] = 'main';
			}

			$args['section'] = self::get_section_id( $args['section'] );

			add_settings_field( $name, $title, array( 'Site_Settings_Admin_Screen', 'add_settings_field' ), 'site_settings', $args['section'], $args );

		}

		/**
		 * Add Settings Field
		 *
		 * @param  array  $args  Setting parameters.
		 */
		public static function add_settings_field( $args ) {

			// Ensure all expected parameters have default values.
			$args = wp_parse_args( $args, array(
				'input'     => 'text',
				'name'      => '',
				'rows'      => '',
				'post_type' => '',
				'taxonomy'  => '',
				'data'      => array()
			) );

			$name  = $args['name'];
			$id    = self::get_field_id( $name );
			$value = Site_Settings::get( $name );

			if ( $args['input'] == 'textarea' ) {

				// Text area field.
				printf( '<textarea id="%s" name="site_settings_options[%s]" size="40" rows="%s" class="large-text">%s</textarea>', $id, $name, $args['rows'], $value );

			} elseif ( $args['input'] == 'select' ) {

				// Select menu field.

				if ( 'page' == $args['post_type']  ) {

					// Page dropdown menu select field.
					// Requires the 'post_type' parameter to be set to 'page'.

					$select_args = wp_parse_args( $args['data'], array(
						'show_option_none' => sprintf( '–– %s ––', __( 'Not Set', SITE_SETTINGS_TEXTDOMAIN ) )
					) );

					$select_args['name']     = 'site_settings_options[' . $name . ']';
					$select_args['id']       = $id;
					$select_args['selected'] = $value;

					wp_dropdown_pages( $select_args );

				} elseif ( ! empty( $args['taxonomy'] ) ) {

					// Taxonomy dropdown menu select field.
					// Requires the 'taxonomy' parameter to be set.

					$select_args = wp_parse_args( $args['data'], array(
						'hide_empty'       => 0,
						'show_option_none' => sprintf( '–– %s ––', __( 'Not Set', SITE_SETTINGS_TEXTDOMAIN ) )
					) );

					$select_args['name']     = 'site_settings_options[' . $name . ']';
					$select_args['id']       = $id;
					$select_args['selected'] = $value;
					$select_args['taxonomy'] = $args['taxonomy'];

					wp_dropdown_categories( $select_args );

				} else {

					// Custom select menu.
					// Expects 'data' parameters to be an array of key => value pairs.

					$options = '';

					if ( is_array( $args['data'] ) ) {
						foreach ( $args['data'] as $option => $title ) {
							$options .= sprinf( '<option value="%s"%s>%s</option>', esc_attr( $option ), selected( $option, $value ), esc_html( $title ) );
						}
					}

					printf( '<select id="%s" name="site_settings_options[%s]">%s</select>', $id, $name, $options );

				}

			} else {

				// Default text field.
				printf( '<input id="%s" name="site_settings_options[%s]" size="40" type="text" value="%s" class="regular-text" />', $id, $name, $value );

			}

		}

		/**
		 * Validate Settings Options
		 */
		public static function validate_settings_options( $input ) {

			if ( is_array( $input ) ) {

				foreach ( $input as $key => $value ) {
					$input[ $key ] = apply_filters( 'site_settings_validate_field', $value, $key );
				}

			}

			return $input;

		}

		private static function get_section_id( $section ) {

			return 'site_settings_' . sanitize_key( $section );

		}

		private static function get_field_id( $name ) {

			return 'site_settings_options_' . sanitize_key( $name );

		}

	}

}
