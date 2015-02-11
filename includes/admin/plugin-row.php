<?php

/**
 * Plugin Template: Plugin Row
 */

if ( ! class_exists( 'Site_Settings_Plugin_Row' ) ) {

	class Site_Settings_Plugin_Row {

		var $plugin_basename = '';
		var $meta = array();
		var $actions = array();

		/**
		 * Constructor
		 *
		 * @param  string  $plugin_basename  Plugin basename.
		 */
		public function __construct( $plugin_basename ) {

			$this->plugin_basename = $plugin_basename;

			add_filter( 'plugin_row_meta', array( $this, '_plugin_row_meta' ), 10, 4 );
			add_filter( 'plugin_action_links_' . $this->plugin_basename, array( $this, '_plugin_action_links' ) );

		}

		/**
		 * Add Meta
		 *
		 * Adds a meta link to the plugin row.
		 *
		 * @param   string  $text  Meta link text.
		 * @param   string  $url   Optional. Meta link URL.
		 */
		public function add_meta( $text, $url = '' ) {

			$this->meta[] = array(
				'text' => $text,
				'url'  => $url
			);

		}

		/**
		 * Add Action
		 *
		 * Adds an action link to the plugin row.
		 *
		 * @param   string  $text  Action link text.
		 * @param   string  $url   Action link URL.
		 */
		public function add_action( $text, $url ) {

			$this->actions[] = array(
				'text' => $text,
				'url'  => $url
			);

		}

		/**
		 * Plugin Row Meta
		 *
		 * Adds GitHub link below the plugin description on the plugins page.
		 *
		 * @param   array   $plugin_meta  Plugin meta display array.
		 * @param   string  $plugin_file  Plugin reference.
		 * @param   array   $plugin_data  Plugin data.
		 * @param   string  $status       Plugin status.
		 * @return  array                 Plugin meta array.
		 */
		public function _plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {

			if ( $this->plugin_basename == $plugin_file ) {
				foreach ( $this->meta as $meta ) {

					if ( empty( $meta['url'] ) ) {
						$plugin_meta[] = $meta['text'];
					} else {
						$plugin_meta[] = sprintf( '<a href="%s">%s</a>', $meta['url'], $meta['text'] );
					}

				}
			}

			return $plugin_meta;

		}

		/**
		 * Plugin Action Links
		 *
		 * Adds settings link on the plugins page.
		 *
		 * @param   array  $actions  Plugin action links array.
		 * @return  array            Plugin action links array.
		 */
		public function _plugin_action_links( $actions ) {

			foreach ( $this->actions as $action ) {
				$actions[] = sprintf( '<a href="%s">%s</a>', $action['url'], $action['text'] );
			}

			return $actions;

		}

	}

}
