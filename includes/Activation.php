<?php

namespace DialogContactForm;

use DialogContactForm\Supports\Utils;
use DialogContactForm\Templates\ContactUs;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Activation {

	/**
	 * Entry database table name
	 *
	 * @var string
	 */
	private static $table_name = 'dcf_entries';

	/**
	 * Entry meta table name
	 *
	 * @var string
	 */
	private static $meta_table_name = 'dcf_entry_meta';

	/**
	 * Plugin install functionality
	 */
	public static function install() {
		self::add_default_form();
		self::create_tables();
	}

	/**
	 * Add sample form on plugin activation
	 */
	public static function add_default_form() {
		$contact_forms = get_posts( array(
			'post_type'      => DIALOG_CONTACT_FORM_POST_TYPE,
			'posts_per_page' => 5,
			'post_status'    => 'any'
		) );

		if ( count( $contact_forms ) > 0 ) {
			self::upgrade_to_version_3( $contact_forms );

			return;
		}

		$contact_form = new ContactUs();

		$post_id = wp_insert_post( array(
			'post_title'     => $contact_form->getTitle(),
			'post_status'    => 'publish',
			'post_type'      => DIALOG_CONTACT_FORM_POST_TYPE,
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		) );

		if ( is_int( $post_id ) ) {
			$contact_form->run( $post_id );

			self::upgrade_to_version_2( $post_id );
		}
	}


	/**
	 * Upgrade to version 2 from version 1
	 *
	 * @param int $post_id
	 *
	 * @return bool
	 */
	public static function upgrade_to_version_2( $post_id = 0 ) {
		$old_option = get_option( 'dialogcf_options' );
		if ( ! isset( $old_option['display_dialog'], $old_option['dialog_color'] ) ) {
			return false;
		}

		if ( 'show' != $old_option['display_dialog'] ) {
			return false;
		}

		$option                             = Utils::default_options();
		$option['dialog_button_background'] = $old_option['dialog_color'];
		$option['dialog_form_id']           = $post_id;

		return update_option( 'dialog_contact_form', $option );
	}

	/**
	 * @param array $contact_forms array of \WP_Post class
	 */
	private static function upgrade_to_version_3( $contact_forms ) {
		$version = get_option( 'dialog_contact_form_version' );
		if ( version_compare( $version, '3.0.0', '>=' ) ) {
			return;
		}
		// Update field validation for required field
		foreach ( $contact_forms as $contact_form ) {
			if ( ! $contact_form instanceof \WP_Post ) {
				continue;
			}
			$fields     = get_post_meta( $contact_form->ID, '_contact_form_fields', true );
			$new_fields = array();
			foreach ( $fields as $field ) {
				$validation = isset( $field['validation'] ) ? $field['validation'] : array();
				if ( in_array( 'required', $validation ) ) {
					$field['required_field'] = 'on';
				} else {
					$field['required_field'] = 'off';
				}

				$new_fields[] = $field;
			}

			update_post_meta( $contact_form->ID, '_contact_form_fields', $new_fields );
			update_post_meta( $contact_form->ID, '_contact_form_actions', array(
				'store_submission',
				'email_notification',
				'success_message',
				'redirect'
			) );
			update_post_meta( $contact_form->ID, '_action_success_message', array(
				'message' => Utils::get_option( 'mail_sent_ok' )
			) );
			update_post_meta( $contact_form->ID, '_action_redirect', array(
				'redirect_to' => 'same',
			) );
		}

		update_option( 'dialog_contact_form_version', DIALOG_CONTACT_FORM_VERSION, false );
	}

	/**
	 * Create plugin tables
	 */
	public static function create_tables() {
		global $wpdb;

		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			if ( ! empty( $wpdb->charset ) ) {
				$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
			}

			if ( ! empty( $wpdb->collate ) ) {
				$collate .= " COLLATE $wpdb->collate";
			}
		}

		$table_name      = $wpdb->prefix . self::$table_name;
		$meta_table_name = $wpdb->prefix . self::$meta_table_name;

		$entries_table_schema = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `form_id` bigint(20) unsigned DEFAULT NULL,
                `user_id` bigint(20) unsigned DEFAULT NULL,
                `user_ip` varchar(45) DEFAULT NULL,
                `user_agent` varchar(255) DEFAULT NULL,
                `referer` varchar(255) DEFAULT NULL,
                `status` varchar(20) DEFAULT 'unread',
                `created_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `form_id` (`form_id`)
            ) $collate;";

		$meta_table_schema = "CREATE TABLE IF NOT EXISTS `{$meta_table_name}` (
                `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `entry_id` bigint(20) unsigned DEFAULT NULL,
                `meta_key` varchar(255) DEFAULT NULL,
                `meta_value` longtext,
                PRIMARY KEY (`meta_id`),
                KEY `meta_key` (`meta_key`),
                KEY `entry_id` (`entry_id`)
            ) $collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $entries_table_schema );
		dbDelta( $meta_table_schema );
	}
}
