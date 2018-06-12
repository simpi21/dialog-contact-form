<?php

namespace DialogContactForm;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Activation {

	/**
	 * Instance of the class
	 *
	 * @var object
	 */
	private static $instance;

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
	 * @return Activation
	 */
	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		add_action( 'dialog_contact_form_activation', array( self::$instance, 'add_default_form' ) );
		add_action( 'dialog_contact_form_activation', array( self::$instance, 'create_tables' ) );
		add_action( 'dialog_contact_form_deactivate', array( self::$instance, 'delete_tables' ) );

		return self::$instance;
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
			return;
		}

		$post_title = esc_html__( 'Contact Form 1', 'dialog-contact-form' );

		$post_id = wp_insert_post( array(
			'post_title'     => $post_title,
			'post_status'    => 'publish',
			'post_type'      => DIALOG_CONTACT_FORM_POST_TYPE,
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		) );

		if ( is_int( $post_id ) ) {
			update_post_meta( $post_id, '_contact_form_fields', dcf_default_fields() );
			update_post_meta( $post_id, '_contact_form_messages', Utils::validation_messages() );
			update_post_meta( $post_id, '_contact_form_mail', array(
				'receiver'    => '[system:admin_email]',
				'senderEmail' => '[your_email]',
				'senderName'  => '[your_name]',
				'subject'     => '[system:blogname] : [subject]',
				'body'        => '[all_fields_table]',
			) );
			update_post_meta( $post_id, '_contact_form_config', array(
				'labelPosition' => 'both',
				'btnLabel'      => esc_html__( 'Send', 'dialog-contact-form' ),
				'btnAlign'      => 'left',
				'reset_form'    => 'yes',
				'recaptcha'     => 'no',
			) );

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

	public function delete_tables() {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			global $wpdb;
			$table_name      = $wpdb->prefix . self::$table_name;
			$meta_table_name = $wpdb->prefix . self::$meta_table_name;

			$wpdb->query( "DROP TABLE IF EXISTS `{$table_name}`" );
			$wpdb->query( "DROP TABLE IF EXISTS `{$meta_table_name}`" );
		}
	}
}
