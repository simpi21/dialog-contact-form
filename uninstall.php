<?php

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

if ( ! function_exists( 'dcf_delete_plugin_data' ) ) {
	/**
	 * Delete plugin data
	 */
	function dcf_delete_plugin_data() {

		// Delete all contact form posts
		$_posts = get_posts(
			array(
				'posts_per_page' => - 1,
				'post_type'      => DIALOG_CONTACT_FORM_POST_TYPE,
				'post_status'    => 'any',
			)
		);

		foreach ( $_posts as $_post ) {
			wp_delete_post( $_post->ID, true );
		}

		// Delete plugin options
		delete_option( 'dialog_contact_form' );

		// Delete tables
		global $wpdb;
		$table_name      = $wpdb->prefix . 'dcf_entries';
		$meta_table_name = $wpdb->prefix . 'dcf_entry_meta';

		$wpdb->query( "DROP TABLE IF EXISTS {$table_name}" );
		$wpdb->query( "DROP TABLE IF EXISTS {$meta_table_name}" );
	}
}

dcf_delete_plugin_data();
