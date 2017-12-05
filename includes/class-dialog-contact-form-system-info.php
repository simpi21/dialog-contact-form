<?php

class Dialog_Contact_Form_System_Info {

	private static $instance;

	/**
	 * @return Dialog_Contact_Form_System_Info
	 */
	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Get server information
	 *
	 * @return array
	 */
	public static function server_environment() {
		global $wpdb;

		$server_ip = '';
		if ( array_key_exists( 'SERVER_ADDR', $_SERVER ) ) {
			$server_ip = $_SERVER['SERVER_ADDR'];
		} elseif ( array_key_exists( 'LOCAL_ADDR', $_SERVER ) ) {
			$server_ip = $_SERVER['LOCAL_ADDR'];
		}

		return array(
			'Operating System'        => PHP_OS,
			'Software'                => $_SERVER['SERVER_SOFTWARE'],
			'Server IP Address'       => $server_ip,
			'Host Name'               => gethostbyaddr( $server_ip ),
			'MySQL version'           => $wpdb->db_version(),
			'PHP Version'             => PHP_VERSION,
			'PHP Max Input Vars'      => ini_get( 'max_input_vars' ),
			'Max Input Nesting Level' => ini_get( 'max_input_nesting_level' ),
			'PHP Max Post Size'       => ini_get( 'post_max_size' ),
			'PHP Time Limit'          => ini_get( 'max_execution_time' ),
		);
	}

	/**
	 * Get WordPress environment information
	 *
	 * @return array
	 */
	public static function wp_environment() {
		global $wp_rewrite;

		$permalink = $wp_rewrite->permalink_structure;
		if ( ! $permalink ) {
			$permalink = 'Plain';
		}

		$timezone = get_option( 'timezone_string' );
		if ( ! $timezone ) {
			$timezone = get_option( 'gmt_offset' );
		}

		return array(
			'Site URL'            => site_url(),
			'Home URL'            => home_url(),
			'Version'             => get_bloginfo( 'version' ),
			'Multisite'           => is_multisite() ? "Yes" : "No",
			'Max Upload Size'     => size_format( wp_max_upload_size() ),
			'Memory Limit'        => WP_MEMORY_LIMIT,
			'Permalink Structure' => $permalink,
			'Language'            => get_bloginfo( 'language' ),
			'Timezone'            => $timezone,
			'Debug Mode'          => defined( 'WP_DEBUG' ) && WP_DEBUG ? 'Active' : 'Inactive',
		);
	}

	/**
	 * Get WordPress theme information
	 *
	 * @return array
	 */
	public static function theme() {
		$theme        = wp_get_theme();
		$parent_theme = $theme->parent();
		$theme_info   = array(
			'Name'        => $theme->get( 'Name' ),
			'Version'     => $theme->get( 'Version' ),
			'Author'      => $theme->get( 'Author' ),
			'Child Theme' => is_child_theme() ? 'Yes' : 'No',
		);
		if ( $parent_theme ) {
			$parent_fields = array(
				'Parent Theme Name'    => $parent_theme->get( 'Name' ),
				'Parent Theme Version' => $parent_theme->get( 'Version' ),
				'Parent Theme Author'  => $parent_theme->get( 'Author' ),
			);
			$theme_info    = array_merge( $theme_info, $parent_fields );
		}

		return $theme_info;
	}

	public static function active_plugins() {

		// Ensure get_plugins function is loaded
		if ( ! function_exists( 'get_plugins' ) ) {
			include ABSPATH . '/wp-admin/includes/plugin.php';
		}

		$active_plugins = get_option( 'active_plugins' );
		$plugins        = array_intersect_key( get_plugins(), array_flip( $active_plugins ) );

		return $plugins;
	}
}