<?php

namespace DialogContactForm;

use DialogContactForm\Abstracts\Template;
use WP_CLI;
use WP_CLI_Command;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CLI_Command extends WP_CLI_Command {
	/**
	 * Display Dialog Contact Form Information
	 *
	 * @subcommand info
	 */
	public function info() {
		WP_CLI::success( 'Welcome to the Dialog Contact Form WP-CLI Extension!' );
		WP_CLI::line( '' );
		WP_CLI::line( '- Dialog Contact Form Version: ' . DIALOG_CONTACT_FORM_VERSION );
		WP_CLI::line( '- Dialog Contact Form Directory: ' . DIALOG_CONTACT_FORM_PATH );
		WP_CLI::line( '- Dialog Contact Form Public URL: ' . DIALOG_CONTACT_FORM_URL );
		WP_CLI::line( '' );
	}

	/**
	 * Display form information by form id
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : The form id.
	 *
	 * @param $args
	 */
	public function get_form( $args ) {
		list( $id ) = $args;

		$form = get_post( $id );

		WP_CLI::line( '#' . $form->ID . ' - ' . $form->post_title );

		$fields = get_post_meta( $form->ID, '_contact_form_fields', true );

		if ( $fields ) {
			WP_CLI::line( '==============================' );
			WP_CLI::line( 'Fields:' );
			WP_CLI::line( '------------------------------' );

			foreach ( $fields as $field ) {
				WP_CLI::line( sprintf( '\'%s\' : %s', $field["field_id"], $field["field_title"] ) );
			}
		}
	}

	/**
	 * Creates a Form
	 *
	 * ## OPTIONS
	 *
	 * <template>
	 * [--template=<template>]
	 * : The form template unique id.
	 * ---
	 * default: blank
	 * options:
	 *   - blank
	 *   - collect_feedback
	 *   - contact_us
	 *   - data_erasure_request
	 *   - data_export_request
	 *   - event_registration
	 *   - general_enquiry
	 *   - job_application
	 *   - quote_request
	 * ---
	 *
	 * ## EXAMPLES
	 *     wp dialog-contact-form create_form --template='contact_us'
	 *
	 */
	public function create_form( $args, $assoc_args ) {
		$template  = ! empty( $assoc_args['template'] ) ? $assoc_args['template'] : 'blank';
		$templates = TemplateManager::init();
		$className = $templates->get( $template );
		$template  = new $className;
		if ( ! $template instanceof Template ) {
			WP_CLI::line( __( 'Form template is not available.', 'dialog-contact-form' ) );

			return;
		}

		$post_id = wp_insert_post( array(
			'post_title'     => $template->getTitle(),
			'post_status'    => 'publish',
			'post_type'      => DIALOG_CONTACT_FORM_POST_TYPE,
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		) );

		if ( is_wp_error( $post_id ) ) {
			WP_CLI::line( __( 'Could not create form.', 'dialog-contact-form' ) );

			return;
		}

		$template->run( $post_id );

		$response = sprintf( __( "#%s - %s has been created successfully.", "dialog-contact-form" ),
			$post_id, $template->getTitle() );
		WP_CLI::line( $response );
	}

	/**
	 * Delete a form by form id
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : The form id.
	 *
	 * @param $args
	 */
	public function delete_form( $args ) {
		list( $id ) = $args;

		if ( wp_delete_post( $id, true ) ) {
			// @TODO delete all entries related to form id

			WP_CLI::line( "#{$id} has been deleted successfully." );
		}
	}

	/**
	 * Delete all form entries
	 */
	public function delete_entries() {
		global $wpdb;
		$table_name      = $wpdb->prefix . 'dcf_entries';
		$meta_table_name = $wpdb->prefix . 'dcf_entry_meta';

		$wpdb->query( "TRUNCATE TABLE {$table_name}" );
		$wpdb->query( "TRUNCATE TABLE {$meta_table_name}" );

		WP_CLI::success( 'Dialog Contact Form: all entries has been deleted successfully.' );
	}
}
