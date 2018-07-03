<?php

namespace DialogContactForm\Admin;

use DialogContactForm\Fields\Recaptcha2;
use DialogContactForm\Supports\SettingHandler;
use DialogContactForm\Supports\Utils;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Settings {

	/**
	 * @var Settings
	 */
	private static $instance;

	/**
	 * @return Settings
	 */
	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();

			self::settings();
		}

		return self::$instance;
	}

	/**
	 * Plugin settings
	 */
	public static function settings() {
		$default_options = Utils::default_options();
		$option_page     = SettingHandler::init();

		// Add settings page
		$option_page->add_menu( array(
			'parent_slug' => 'edit.php?post_type=dialog-contact-form',
			'page_title'  => __( 'Dialog Contact Form Settings', 'dialog-contact-form' ),
			'menu_title'  => __( 'Settings', 'dialog-contact-form' ),
			'capability'  => 'manage_options',
			'menu_slug'   => 'dcf-settings',
			'option_name' => 'dialog_contact_form',
		) );

		$panels = array(
			array(
				'id'       => 'general_settings_panel',
				'title'    => __( 'General Settings', 'dialog-contact-form' ),
				'priority' => 10,
			),
			array(
				'id'       => 'message_panel',
				'title'    => __( 'Validation Messages', 'dialog-contact-form' ),
				'priority' => 20,
			),
			array(
				'id'       => 'smtp_server_panel',
				'title'    => __( 'SMTP Settings', 'dialog-contact-form' ),
				'priority' => 30,
			),
			array(
				'id'       => 'integrations_panel',
				'title'    => __( 'Integrations', 'dialog-contact-form' ),
				'priority' => 40,
			),
		);

		// Add settings page tab
		$option_page->setPanels( apply_filters( 'dialog_contact_form/settings/panels', $panels ) );

		$sections = array(
			array(
				'id'          => 'dcf_style_section',
				'title'       => __( 'General Options', 'dialog-contact-form' ),
				'description' => __( 'Plugin general options.', 'dialog-contact-form' ),
				'panel'       => 'general_settings_panel',
				'priority'    => 10,
			),
			array(
				'id'          => 'dcf_dialog_section',
				'title'       => __( 'Dialog/Modal', 'dialog-contact-form' ),
				'description' => __( 'Configure fixed dialog/modal button at your site footer.', 'dialog-contact-form' ),
				'panel'       => 'general_settings_panel',
				'priority'    => 20,
			),
			array(
				'id'          => 'dcf_message_section',
				'title'       => __( 'General Validation Messages', 'dialog-contact-form' ),
				'description' => __( 'Define general validation message. This message can be overwrite from each form.',
					'dialog-contact-form' ),
				'panel'       => 'message_panel',
				'priority'    => 30,
			),
			array(
				'id'          => 'dcf_field_message_section',
				'title'       => __( 'Field Validation Messages', 'dialog-contact-form' ),
				'description' => __( 'Define validation message for form fields. These message will be used for all forms.',
					'dialog-contact-form' ),
				'panel'       => 'message_panel',
				'priority'    => 40,
			),
			array(
				'id'          => 'dcf_smpt_server_section',
				'title'       => __( 'SMTP Server Settings', 'dialog-contact-form' ),
				'description' => '',
				'panel'       => 'smtp_server_panel',
				'priority'    => 50,
			),
			array(
				'id'          => 'dcf_grecaptcha_section',
				'title'       => __( 'reCAPTCHA', 'dialog-contact-form' ),
				'description' => sprintf( __( 'reCAPTCHA is a free service from Google to protect your website from spam and abuse. To use reCAPTCHA, you need to install an API key pair. %sGet your API Keys%s.',
					'dialog-contact-form' ),
					'<a target="_blank" href="https://www.google.com/recaptcha/admin#list">', '</a>' ),
				'panel'       => 'integrations_panel',
				'priority'    => 60,
			),
			array(
				'id'          => 'dcf_mailChimp_section',
				'title'       => __( 'MailChimp', 'dialog-contact-form' ),
				'description' => sprintf(
					__( 'To integrate MailChimp with our forms you need an %sAPI Key%s.', 'dialog-contact-form' ),
					'<a href="https://kb.mailchimp.com/integrations/api-integrations/about-api-keys" target="_blank">',
					'</a>'
				),
				'panel'       => 'integrations_panel',
				'priority'    => 70,
			)
		);

		// Add Sections
		$option_page->setSections( apply_filters( 'dialog_contact_form/settings/sections', $sections ) );


		// SMTP Server Settings
		self::general_settings( $option_page );
		self::dialog_settings( $option_page, $default_options );
		self::smtp_server_settings( $option_page );
		self::validation_messages( $option_page, $default_options );
		self::recaptcha_settings( $option_page );
		self::mailchimp_settings( $option_page );
	}

	/**
	 * SMTP Server settings
	 *
	 * @param SettingHandler $option_page
	 */
	private static function smtp_server_settings( $option_page ) {
		$option_page->add_field( array(
			'id'       => 'mailer',
			'type'     => 'checkbox',
			'name'     => __( 'Use SMTP', 'dialog-contact-form' ),
			'desc'     => __( 'Check to send all emails via SMTP', 'dialog-contact-form' ),
			'std'      => '',
			'section'  => 'dcf_smpt_server_section',
			'priority' => 10,
		) );
		$option_page->add_field( array(
			'id'       => 'smpt_host',
			'type'     => 'text',
			'name'     => __( 'SMTP Host', 'dialog-contact-form' ),
			'desc'     => __( 'Specify your SMTP server hostname', 'dialog-contact-form' ),
			'std'      => '',
			'section'  => 'dcf_smpt_server_section',
			'priority' => 20,
		) );
		$option_page->add_field( array(
			'id'       => 'smpt_username',
			'type'     => 'text',
			'name'     => __( 'SMTP Username', 'dialog-contact-form' ),
			'desc'     => __( 'Specify the username for your SMTP server', 'dialog-contact-form' ),
			'std'      => '',
			'section'  => 'dcf_smpt_server_section',
			'priority' => 30,
		) );
		$option_page->add_field( array(
			'id'       => 'smpt_password',
			'type'     => 'text',
			'name'     => __( 'SMTP Password', 'dialog-contact-form' ),
			'desc'     => __( 'Specify the password for your SMTP server', 'dialog-contact-form' ),
			'std'      => '',
			'section'  => 'dcf_smpt_server_section',
			'priority' => 40,
		) );
		$option_page->add_field( array(
			'id'       => 'smpt_port',
			'type'     => 'text',
			'name'     => __( 'SMTP Port', 'dialog-contact-form' ),
			'desc'     => __( 'Specify the password for your SMTP server', 'dialog-contact-form' ),
			'std'      => '',
			'section'  => 'dcf_smpt_server_section',
			'priority' => 50,
		) );
		$option_page->add_field( array(
			'id'       => 'encryption',
			'type'     => 'radio',
			'name'     => __( 'Encryption', 'dialog-contact-form' ),
			'desc'     => __( 'If you have SSL/TLS encryption available for that hostname, choose it here',
				'dialog-contact-form' ),
			'std'      => '',
			'section'  => 'dcf_smpt_server_section',
			'priority' => 60,
			'options'  => array(
				'no'  => esc_attr__( 'No encryption', 'dialog-contact-form' ),
				'tls' => esc_attr__( 'Use TLS encryption', 'dialog-contact-form' ),
				'ssl' => esc_attr__( 'Use SSL encryption', 'dialog-contact-form' ),
			)
		) );
	}

	/**
	 * Validation messages
	 *
	 * @param SettingHandler $option_page
	 * @param array $default_options
	 */
	private static function validation_messages( $option_page, $default_options ) {
		// Add Validation Messages section fields
		$option_page->add_field( array(
			'id'       => 'spam_message',
			'type'     => 'textarea',
			'rows'     => 2,
			'name'     => __( 'Submission filtered as spam', 'dialog-contact-form' ),
			'std'      => $default_options['spam_message'],
			'section'  => 'dcf_message_section',
			'priority' => 10,
		) );
		$option_page->add_field( array(
			'id'       => 'mail_sent_ok',
			'type'     => 'textarea',
			'rows'     => 2,
			'name'     => __( 'Message sent successfully', 'dialog-contact-form' ),
			'std'      => $default_options['mail_sent_ok'],
			'section'  => 'dcf_message_section',
			'priority' => 20,
		) );
		$option_page->add_field( array(
			'id'       => 'mail_sent_ng',
			'type'     => 'textarea',
			'rows'     => 2,
			'name'     => __( 'Message failed to sent', 'dialog-contact-form' ),
			'std'      => $default_options['mail_sent_ng'],
			'section'  => 'dcf_message_section',
			'priority' => 30,
		) );
		$option_page->add_field( array(
			'id'       => 'validation_error',
			'type'     => 'textarea',
			'rows'     => 2,
			'name'     => __( 'Validation errors occurred', 'dialog-contact-form' ),
			'std'      => $default_options['validation_error'],
			'section'  => 'dcf_message_section',
			'priority' => 40,
		) );

		// Field Validation messages
		$option_page->add_field( array(
			'id'       => 'invalid_required',
			'type'     => 'textarea',
			'rows'     => 2,
			'name'     => __( 'Required input', 'dialog-contact-form' ),
			'desc'     => __( 'Required field message for input, textarea except radio, select and checkbox field.',
				'dialog-contact-form' ),
			'std'      => $default_options['invalid_required'],
			'section'  => 'dcf_field_message_section',
			'priority' => 10,
		) );
		$option_page->add_field( array(
			'id'       => 'required_checkbox',
			'type'     => 'textarea',
			'rows'     => 2,
			'name'     => __( 'Required checkbox', 'dialog-contact-form' ),
			'desc'     => __( 'Required field message for checkbox field.', 'dialog-contact-form' ),
			'std'      => $default_options['required_checkbox'],
			'section'  => 'dcf_field_message_section',
			'priority' => 20,
		) );
		$option_page->add_field( array(
			'id'       => 'required_select',
			'type'     => 'textarea',
			'rows'     => 2,
			'name'     => __( 'Required select', 'dialog-contact-form' ),
			'desc'     => __( 'Required field message for radio and select field.', 'dialog-contact-form' ),
			'std'      => $default_options['required_select'],
			'section'  => 'dcf_field_message_section',
			'priority' => 30,
		) );
		$option_page->add_field( array(
			'id'       => 'required_select_multi',
			'type'     => 'textarea',
			'rows'     => 2,
			'name'     => __( 'Required select multiple', 'dialog-contact-form' ),
			'desc'     => __( 'Required field message for select and checkbox fields with multiple values.',
				'dialog-contact-form' ),
			'std'      => $default_options['required_select_multi'],
			'section'  => 'dcf_field_message_section',
			'priority' => 40,
		) );
		$option_page->add_field( array(
			'id'       => 'invalid_too_long',
			'type'     => 'textarea',
			'rows'     => 3,
			'name'     => __( 'Too long', 'dialog-contact-form' ),
			'desc'     => sprintf(
				__( 'You can use %s for showing maximum allowed characters and %s for user input value length.',
					'dialog-contact-form' ),
				'<strong>{maxLength}</strong>',
				'<strong>{length}</strong>'
			),
			'std'      => $default_options['invalid_too_long'],
			'section'  => 'dcf_field_message_section',
			'priority' => 50,
		) );
		$option_page->add_field( array(
			'id'       => 'invalid_too_short',
			'type'     => 'textarea',
			'rows'     => 3,
			'name'     => __( 'Too short', 'dialog-contact-form' ),
			'desc'     => sprintf(
				__( 'You can use %s for showing minimum allowed characters and %s for user input value length.',
					'dialog-contact-form' ),
				'<strong>{minLength}</strong>',
				'<strong>{length}</strong>'
			),
			'std'      => $default_options['invalid_too_short'],
			'section'  => 'dcf_field_message_section',
			'priority' => 60,
		) );
		$option_page->add_field( array(
			'id'       => 'number_too_large',
			'type'     => 'textarea',
			'rows'     => 2,
			'name'     => __( 'Number too large', 'dialog-contact-form' ),
			'desc'     => sprintf(
				__( 'You can use %s for showing maximum allowed number.', 'dialog-contact-form' ),
				'<strong>{max}</strong>'
			),
			'std'      => $default_options['number_too_large'],
			'section'  => 'dcf_field_message_section',
			'priority' => 70,
		) );
		$option_page->add_field( array(
			'id'       => 'number_too_small',
			'type'     => 'textarea',
			'rows'     => 2,
			'name'     => __( 'Number too short', 'dialog-contact-form' ),
			'desc'     => sprintf(
				__( 'You can use %s for showing minimum allowed number.', 'dialog-contact-form' ),
				'<strong>{min}</strong>'
			),
			'std'      => $default_options['number_too_small'],
			'section'  => 'dcf_field_message_section',
			'priority' => 80,
		) );
		$option_page->add_field( array(
			'id'       => 'invalid_email',
			'type'     => 'textarea',
			'rows'     => 2,
			'name'     => __( 'Invalid email', 'dialog-contact-form' ),
			'std'      => $default_options['invalid_email'],
			'section'  => 'dcf_field_message_section',
			'priority' => 90,
		) );
		$option_page->add_field( array(
			'id'       => 'invalid_url',
			'type'     => 'textarea',
			'rows'     => 2,
			'name'     => __( 'Invalid URL', 'dialog-contact-form' ),
			'std'      => $default_options['invalid_url'],
			'section'  => 'dcf_field_message_section',
			'priority' => 100,
		) );
		$option_page->add_field( array(
			'id'       => 'invalid_number',
			'type'     => 'textarea',
			'rows'     => 2,
			'name'     => __( 'Invalid numeric value', 'dialog-contact-form' ),
			'desc'     => __( 'Numeric strings consist of optional sign, any number of digits, optional decimal part and optional exponential part.',
				'dialog-contact-form' ),
			'std'      => $default_options['invalid_number'],
			'section'  => 'dcf_field_message_section',
			'priority' => 110,
		) );
		$option_page->add_field( array(
			'id'       => 'invalid_date',
			'type'     => 'textarea',
			'rows'     => 2,
			'name'     => __( 'Invalid date', 'dialog-contact-form' ),
			'std'      => $default_options['invalid_date'],
			'section'  => 'dcf_field_message_section',
			'priority' => 120,
		) );
		$option_page->add_field( array(
			'id'       => 'invalid_ip',
			'type'     => 'textarea',
			'rows'     => 2,
			'name'     => __( 'Invalid IP', 'dialog-contact-form' ),
			'std'      => $default_options['invalid_ip'],
			'section'  => 'dcf_field_message_section',
			'priority' => 130,
		) );
		$option_page->add_field( array(
			'id'       => 'invalid_recaptcha',
			'type'     => 'textarea',
			'rows'     => 2,
			'name'     => __( 'invalid reCAPTCHA', 'dialog-contact-form' ),
			'std'      => $default_options['invalid_recaptcha'],
			'section'  => 'dcf_field_message_section',
			'priority' => 140,
		) );
		$option_page->add_field( array(
			'id'       => 'generic_error',
			'type'     => 'textarea',
			'rows'     => 2,
			'name'     => __( 'Generic error', 'dialog-contact-form' ),
			'std'      => $default_options['generic_error'],
			'section'  => 'dcf_field_message_section',
			'priority' => 300,
		) );
	}

	/**
	 * Dialog/Model settings
	 *
	 * @param SettingHandler $option_page
	 * @param array $default_options
	 */
	private static function dialog_settings( $option_page, $default_options ) {
		// Add Dialog/Modal section fields
		$option_page->add_field( array(
			'id'      => 'dialog_button_text',
			'type'    => 'text',
			'name'    => __( 'Dialog button text', 'dialog-contact-form' ),
			'std'     => $default_options['dialog_button_text'],
			'section' => 'dcf_dialog_section'
		) );
		$option_page->add_field( array(
			'id'      => 'dialog_button_background',
			'type'    => 'color',
			'name'    => __( 'Dialog button background', 'dialog-contact-form' ),
			'std'     => $default_options['dialog_button_background'],
			'section' => 'dcf_dialog_section'
		) );
		$option_page->add_field( array(
			'id'      => 'dialog_button_color',
			'type'    => 'color',
			'name'    => __( 'Dialog button color', 'dialog-contact-form' ),
			'std'     => $default_options['dialog_button_color'],
			'section' => 'dcf_dialog_section'
		) );
		$option_page->add_field( array(
			'id'      => 'dialog_form_id',
			'type'    => 'form_list',
			'name'    => __( 'Choose Form', 'dialog-contact-form' ),
			'std'     => '',
			'section' => 'dcf_dialog_section',
			'options' => array(),
		) );
	}

	/**
	 * Dialog/Model settings
	 *
	 * @param SettingHandler $option_page
	 */
	private static function recaptcha_settings( $option_page ) {
		// Add Google reCAPTCHA fields
		$option_page->add_field( array(
			'id'       => 'recaptcha_site_key',
			'type'     => 'text',
			'name'     => __( 'Site key', 'dialog-contact-form' ),
			'desc'     => __( 'Enter google reCAPTCHA API site key', 'dialog-contact-form' ),
			'std'      => '',
			'priority' => 10,
			'section'  => 'dcf_grecaptcha_section'
		) );
		$option_page->add_field( array(
			'id'       => 'recaptcha_secret_key',
			'type'     => 'text',
			'name'     => __( 'Secret key', 'dialog-contact-form' ),
			'desc'     => __( 'Enter google reCAPTCHA API secret key', 'dialog-contact-form' ),
			'std'      => '',
			'priority' => 20,
			'section'  => 'dcf_grecaptcha_section'
		) );
		$option_page->add_field( array(
			'id'       => 'recaptcha_lang',
			'type'     => 'select',
			'name'     => __( 'Language', 'dialog-contact-form' ),
			'desc'     => __( 'Enter google reCAPTCHA API secret key', 'dialog-contact-form' ),
			'std'      => 'en',
			'section'  => 'dcf_grecaptcha_section',
			'priority' => 30,
			'options'  => Recaptcha2::lang(),
		) );
		$option_page->add_field( array(
			'id'       => 'recaptcha_theme',
			'type'     => 'radio',
			'name'     => __( 'Theme', 'dialog-contact-form' ),
			'std'      => 'light',
			'section'  => 'dcf_grecaptcha_section',
			'priority' => 40,
			'options'  => array(
				'light' => esc_html__( 'Light', 'dialog-contact-form' ),
				'dark'  => esc_html__( 'Dark', 'dialog-contact-form' ),
			)
		) );
	}

	/**
	 * Dialog/Model settings
	 *
	 * @param SettingHandler $option_page
	 */
	private static function mailchimp_settings( $option_page ) {
		// Add MailChimp section fields
		$option_page->add_field( array(
			'id'       => 'mailchimp_api_key',
			'type'     => 'text',
			'name'     => __( 'API Key', 'dialog-contact-form' ),
			'std'      => '',
			'section'  => 'dcf_mailChimp_section',
			'priority' => 10,
		) );
	}

	/**
	 * Dialog/Model settings
	 *
	 * @param SettingHandler $option_page
	 */
	private static function general_settings( $option_page ) {
		// Add Style section fields
		$option_page->add_field( array(
			'id'       => 'default_style',
			'type'     => 'radio',
			'name'     => __( 'Default Style', 'dialog-contact-form' ),
			'desc'     => __( 'Disable plugin default style if you want to style by yourself.', 'dialog-contact-form' ),
			'std'      => 'enable',
			'section'  => 'dcf_style_section',
			'priority' => 10,
			'options'  => array(
				'enable'  => esc_html__( 'Enable', 'dialog-contact-form' ),
				'disable' => esc_html__( 'Disable', 'dialog-contact-form' ),
			)
		) );
		$option_page->add_field( array(
			'id'       => 'delete_data_on_uninstallation',
			'type'     => 'radio',
			'name'     => __( 'Delete plugin data', 'dialog-contact-form' ),
			'desc'     => __( 'Choose Yes to delete all "Dialog Contact Form" data when uninstall this plugin.',
				'dialog-contact-form' ),
			'std'      => 'no',
			'section'  => 'dcf_style_section',
			'priority' => 20,
			'options'  => array(
				'yes' => esc_html__( 'Yes', 'dialog-contact-form' ),
				'no'  => esc_html__( 'No', 'dialog-contact-form' ),
			)
		) );
	}
}
