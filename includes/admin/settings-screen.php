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

			add_options_page( __( 'Site Settings', 'site-settings' ), __( 'Site Settings', 'site-settings' ), 'manage_options', 'site-settings', array( 'Site_Settings_Admin_Screen', 'site_settings_page' ) );

		}

		/**
		 * Site Settings Page
		 */
		public static function site_settings_page() {

			?>

			<div>
				<h2><?php _e( 'Site Settings', 'site-settings' ); ?></h2>
				<p><?php _e( 'Manage fields and settings relating to your site content.', 'site-settings' ); ?></p>
				<p><?php printf( __( 'To find out how to add sections and fields, visit <a%s>the documentation</a>.', 'site-settings' ), ' href="https://github.com/benhuson/site-settings/wiki"' ); ?></p>

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

			$args['id'] = self::get_field_id( $args['name'] );
			$args['value'] = Site_Settings::get( $args['name'] );

			switch ( $args['input'] ) {

				// Textarea
				case 'textarea':
					self::add_settings_textarea_field( $args );
					break;

				// Select
				case 'select':
					self::add_settings_select_field( $args );
					break;

				// Checkbox / Radio
				case 'checkbox':
				case 'radio':
					self::add_settings_checkbox_field( $args );
					break;

				// Text
				default:
					self::add_settings_text_field( $args );
					break;

			}

		}

		/**
		 * Add Textarea Settings Field
		 *
		 * @param  array  $args  Setting parameters.
		 */
		private static function add_settings_textarea_field( $args ) {

			printf( '<textarea id="%s" name="site_settings_options[%s]" size="40" rows="%s" class="large-text">%s</textarea>', $args['id'], $args['name'], $args['rows'], $args['value'] );

		}

		/**
		 * Add Select Settings Field
		 *
		 * @param  array  $args  Setting parameters.
		 */
		private static function add_settings_select_field( $args ) {

			if ( ! empty( $args['post_type'] ) && post_type_exists( $args['post_type'] ) ) {

				// Page dropdown menu select field.
				// Requires the 'post_type' parameter to be set to 'page'.

				$select_args = wp_parse_args( $args['data'], array(
					'show_option_none' => sprintf( '–– %s ––', __( 'Not Set', 'site-settings' ) )
				) );

				$select_args['name']     = 'site_settings_options[' . $args['name'] . ']';
				$select_args['id']       = $args['id'];
				$select_args['selected'] = $args['value'];
				$select_args['post_type'] = $args['post_type'];

				wp_dropdown_pages( $select_args );

			} elseif ( ! empty( $args['taxonomy'] ) ) {

				// Taxonomy dropdown menu select field.
				// Requires the 'taxonomy' parameter to be set.

				$select_args = wp_parse_args( $args['data'], array(
					'hide_empty'       => 0,
					'show_option_none' => sprintf( '–– %s ––', __( 'Not Set', 'site-settings' ) )
				) );

				$select_args['name']     = 'site_settings_options[' . $args['name'] . ']';
				$select_args['id']       = $args['id'];
				$select_args['selected'] = $args['value'];
				$select_args['taxonomy'] = $args['taxonomy'];

				wp_dropdown_categories( $select_args );

			} else {

				// Custom select menu.
				// Expects 'data' parameters to be an array of key => value pairs.

				$options = '';

				if ( is_array( $args['data'] ) ) {
					foreach ( $args['data'] as $option => $title ) {
						$options .= sprintf( '<option value="%s"%s>%s</option>', esc_attr( $option ), selected( $option, $args['value'], false ), esc_html( $title ) );
					}
				}

				printf( '<select id="%s" name="site_settings_options[%s]">%s</select>', $args['id'], $args['name'], $options );

			}

		}

		/**
		 * Add Checkbox Settings Field
		 *
		 * @param  array  $args  Setting parameters.
		 */
		private static function add_settings_checkbox_field( $args ) {

			if ( is_array( $args['data'] ) && ! empty( $args['data'] ) ) {

				self::add_settings_checkboxes_field( $args );

			} else {

				printf( '<input id="%s" name="site_settings_options[%s]" size="40" type="%s" value="1"%s />', $args['id'], $args['name'], $args['input'], checked( 1, $args['value'], false ) );

			}

		}

		/**
		 * Add Checkboxes Settings Field
		 *
		 * @param  array  $args  Setting parameters.
		 */
		private static function add_settings_checkboxes_field( $args ) {

			echo '<ul style="margin: 0;">';
			foreach ( $args['data'] as $key => $label ) {
				$name_attr = sprintf( 'site_settings_options[%s]', $args['name'] );
				if ( 'checkbox' == $args['input'] ) {
					$name_attr .= '[]';
				}
				$checked = is_array( $args['value'] ) ? checked( in_array( $key, $args['value'] ), true, false ) : checked( $key, $args['value'], false );
				printf( '<li><label><input id="%s" name="%s" size="40" type="%s" value="%s"%s /> %s</label></li>', $args['id'], $name_attr, $args['input'], $key, $checked, esc_html( $label ) );
			}
			echo '</ul>';

		}

		/**
		 * Add Text Settings Field
		 *
		 * @param  array  $args  Setting parameters.
		 */
		private static function add_settings_text_field( $args ) {

			printf( '<input id="%s" name="site_settings_options[%s]" size="40" type="text" value="%s" class="regular-text" />', $args['id'], $args['name'], $args['value'] );

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
