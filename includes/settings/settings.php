<?php
$option_page = Dialog_Contact_Form_Settings_API::instance();

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
	'id'       => 'dcf_dialog_panel',
	'title'    => __( 'Dialog/Modal', 'dialog-contact-form' ),
	'priority' => 10,
) );
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

// Add Sections
$option_page->add_section( array(
	'id'          => 'dcf_dialog_section',
	'title'       => __( 'Dialog/Modal', 'dialog-contact-form' ),
	'description' => 'Configure fixed dialog/modal button at your site footer.',
	'panel'       => 'dcf_dialog_panel',
	'priority'    => 10,
) );
$option_page->add_section( array(
	'id'          => 'dcf_smpt_server_section',
	'title'       => __( 'SMTP Server Settings', 'dialog-contact-form' ),
	'description' => '',
	'panel'       => 'dcf_smpt_server_panel',
	'priority'    => 20,
) );

// Add SMTP Server settings page fields
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
