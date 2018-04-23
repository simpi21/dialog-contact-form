<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

$default_options = dcf_default_options();
$option_page     = Dialog_Contact_Form_Settings_API::instance();

// Add settings page
$option_page->add_menu( array(
	'parent_slug' => 'edit.php?post_type=dialog-contact-form',
	'page_title'  => __( 'Dialog Contact Form Settings', 'dialog-contact-form' ),
	'menu_title'  => __( 'Settings', 'dialog-contact-form' ),
	'capability'  => 'manage_options',
	'menu_slug'   => 'dcf-settings',
	'option_name' => 'dialog_contact_form',
) );

// Add settings page tab
$option_page->add_panel( array(
	'id'       => 'dcf_message_panel',
	'title'    => __( 'Messages', 'dialog-contact-form' ),
	'priority' => 20,
) );
$option_page->add_panel( array(
	'id'       => 'dcf_grecaptcha_panel',
	'title'    => __( 'reCAPTCHA', 'dialog-contact-form' ),
	'priority' => 30,
) );
$option_page->add_panel( array(
	'id'       => 'dcf_smpt_server_panel',
	'title'    => __( 'SMTP Settings', 'dialog-contact-form' ),
	'priority' => 40,
) );
$option_page->add_panel( array(
	'id'       => 'dcf_style_panel',
	'title'    => __( 'Form Style', 'dialog-contact-form' ),
	'priority' => 50,
) );

// Add Sections
$option_page->add_section( array(
	'id'          => 'dcf_smpt_server_section',
	'title'       => __( 'SMTP Server Settings', 'dialog-contact-form' ),
	'description' => '',
	'panel'       => 'dcf_smpt_server_panel',
	'priority'    => 20,
) );
$option_page->add_section( array(
	'id'          => 'dcf_grecaptcha_section',
	'title'       => __( 'Google reCAPTCHA', 'dialog-contact-form' ),
	'description' => __( 'reCAPTCHA is a free service from Google to protect your website from spam and abuse. To use reCAPTCHA, you need to install an API key pair.', 'dialog-contact-form' ),
	'panel'       => 'dcf_grecaptcha_panel',
	'priority'    => 30,
) );
$option_page->add_section( array(
	'id'          => 'dcf_message_section',
	'title'       => __( 'Validation Messages', 'dialog-contact-form' ),
	'description' => __( 'Define default validation message. This message can be overwrite from each form.', 'dialog-contact-form' ),
	'panel'       => 'dcf_message_panel',
	'priority'    => 40,
) );
$option_page->add_section( array(
	'id'          => 'dcf_style_section',
	'title'       => __( 'Style', 'dialog-contact-form' ),
	'description' => __( 'Define form style.', 'dialog-contact-form' ),
	'panel'       => 'dcf_style_panel',
	'priority'    => 10,
) );
$option_page->add_section( array(
	'id'          => 'dcf_dialog_section',
	'title'       => __( 'Dialog/Modal', 'dialog-contact-form' ),
	'description' => __( 'Configure fixed dialog/modal button at your site footer.', 'dialog-contact-form' ),
	'panel'       => 'dcf_style_panel',
	'priority'    => 20,
) );

// Add SMTP Server settings section fields
$option_page->add_field( array(
	'id'      => 'mailer',
	'type'    => 'checkbox',
	'name'    => __( 'Use SMTP', 'dialog-contact-form' ),
	'desc'    => __( 'Check to send all emails via SMTP', 'dialog-contact-form' ),
	'std'     => '',
	'section' => 'dcf_smpt_server_section'
) );
$option_page->add_field( array(
	'id'      => 'smpt_host',
	'type'    => 'text',
	'name'    => __( 'SMTP Host', 'dialog-contact-form' ),
	'desc'    => __( 'Specify your SMTP server hostname', 'dialog-contact-form' ),
	'std'     => '',
	'section' => 'dcf_smpt_server_section'
) );
$option_page->add_field( array(
	'id'      => 'smpt_username',
	'type'    => 'text',
	'name'    => __( 'SMTP Username', 'dialog-contact-form' ),
	'desc'    => __( 'Specify the username for your SMTP server', 'dialog-contact-form' ),
	'std'     => '',
	'section' => 'dcf_smpt_server_section'
) );
$option_page->add_field( array(
	'id'      => 'smpt_password',
	'type'    => 'text',
	'name'    => __( 'SMTP Password', 'dialog-contact-form' ),
	'desc'    => __( 'Specify the password for your SMTP server', 'dialog-contact-form' ),
	'std'     => '',
	'section' => 'dcf_smpt_server_section'
) );
$option_page->add_field( array(
	'id'      => 'smpt_port',
	'type'    => 'text',
	'name'    => __( 'SMTP Port', 'dialog-contact-form' ),
	'desc'    => __( 'Specify the password for your SMTP server', 'dialog-contact-form' ),
	'std'     => '',
	'section' => 'dcf_smpt_server_section'
) );
$option_page->add_field( array(
	'id'      => 'encryption',
	'type'    => 'radio',
	'name'    => __( 'Encryption', 'dialog-contact-form' ),
	'desc'    => __( 'If you have SSL/TLS encryption available for that hostname, choose it here', 'dialog-contact-form' ),
	'std'     => '',
	'section' => 'dcf_smpt_server_section',
	'options' => array(
		'no'  => esc_attr__( 'No encryption', 'dialog-contact-form' ),
		'tls' => esc_attr__( 'Use TLS encryption', 'dialog-contact-form' ),
		'ssl' => esc_attr__( 'Use SSL encryption', 'dialog-contact-form' ),
	)
) );

// Add Google reCAPTCHA fields
$option_page->add_field( array(
	'id'      => 'recaptcha_site_key',
	'type'    => 'text',
	'name'    => __( 'Site key', 'dialog-contact-form' ),
	'desc'    => __( 'Enter google reCAPTCHA API site key', 'dialog-contact-form' ),
	'std'     => '',
	'section' => 'dcf_grecaptcha_section'
) );
$option_page->add_field( array(
	'id'      => 'recaptcha_secret_key',
	'type'    => 'text',
	'name'    => __( 'Secret key', 'dialog-contact-form' ),
	'desc'    => __( 'Enter google reCAPTCHA API secret key', 'dialog-contact-form' ),
	'std'     => '',
	'section' => 'dcf_grecaptcha_section'
) );
$option_page->add_field( array(
	'id'      => 'recaptcha_lang',
	'type'    => 'select',
	'name'    => __( 'Language', 'dialog-contact-form' ),
	'desc'    => __( 'Enter google reCAPTCHA API secret key', 'dialog-contact-form' ),
	'std'     => 'en',
	'section' => 'dcf_grecaptcha_section',
	'options' => dcf_google_recaptcha_lang(),
) );
$option_page->add_field( array(
	'id'      => 'recaptcha_theme',
	'type'    => 'radio',
	'name'    => __( 'Theme', 'dialog-contact-form' ),
	'std'     => 'light',
	'section' => 'dcf_grecaptcha_section',
	'options' => array(
		'light' => esc_html__( 'Light', 'dialog-contact-form' ),
		'dark'  => esc_html__( 'Dark', 'dialog-contact-form' ),
	)
) );
// Add Validation Messages section fields
$option_page->add_field( array(
	'id'      => 'spam_message',
	'type'    => 'text',
	'name'    => __( 'Submission filtered as spam', 'dialog-contact-form' ),
	'std'     => $default_options['spam_message'],
	'section' => 'dcf_message_section'
) );
$option_page->add_field( array(
	'id'      => 'invalid_recaptcha',
	'type'    => 'text',
	'name'    => __( 'invalid reCAPTCHA', 'dialog-contact-form' ),
	'std'     => $default_options['invalid_recaptcha'],
	'section' => 'dcf_message_section'
) );

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

// Add Style section fields
$option_page->add_field( array(
	'id'      => 'default_style',
	'type'    => 'radio',
	'name'    => __( 'Default Style', 'dialog-contact-form' ),
	'desc'    => __( 'Disable plugin default style if you want to style by yourself.', 'dialog-contact-form' ),
	'std'     => 'enable',
	'section' => 'dcf_style_section',
	'options' => array(
		'enable'  => esc_html__( 'Enable', 'dialog-contact-form' ),
		'disable' => esc_html__( 'Disable', 'dialog-contact-form' ),
	)
) );