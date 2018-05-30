<?php

use DialogContactForm\Supports\Metabox;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

Metabox::select( array(
	'id'          => 'labelPosition',
	'group'       => 'config',
	'meta_key'    => '_contact_form_config',
	'label'       => __( 'Position of the field title', 'dialog-contact-form' ),
	'description' => __( 'choose the position of the field title', 'dialog-contact-form' ),
	'default'     => 'both',
	'options'     => array(
		'label'       => esc_html__( 'Label', 'dialog-contact-form' ),
		'placeholder' => esc_html__( 'Placeholder', 'dialog-contact-form' ),
		'both'        => esc_html__( 'Both label and placeholder', 'dialog-contact-form' ),
	),
) );
Metabox::text( array(
	'id'          => 'btnLabel',
	'group'       => 'config',
	'meta_key'    => '_contact_form_config',
	'label'       => __( 'Submit Button Label', 'dialog-contact-form' ),
	'description' => __( 'Define the label of submit button.', 'dialog-contact-form' ),
	'default'     => esc_html__( 'Send', 'dialog-contact-form' ),
) );
Metabox::buttonset( array(
	'id'          => 'btnAlign',
	'group'       => 'config',
	'meta_key'    => '_contact_form_config',
	'label'       => __( 'Submit Button Alignment', 'dialog-contact-form' ),
	'description' => __( 'Set the alignment of submit button.', 'dialog-contact-form' ),
	'default'     => 'left',
	'options'     => array(
		'left'  => esc_html__( 'Left', 'dialog-contact-form' ),
		'right' => esc_html__( 'Right', 'dialog-contact-form' ),
	),
) );
Metabox::buttonset( array(
	'id'          => 'reset_form',
	'group'       => 'config',
	'meta_key'    => '_contact_form_config',
	'label'       => __( 'Reset form', 'dialog-contact-form' ),
	'description' => __( 'Choose Yes to reset form after successfully submission.', 'dialog-contact-form' ),
	'default'     => 'yes',
	'options'     => array(
		'no'  => esc_html__( 'No', 'dialog-contact-form' ),
		'yes' => esc_html__( 'Yes', 'dialog-contact-form' ),
	),
) );
Metabox::buttonset( array(
	'id'       => 'recaptcha',
	'group'    => 'config',
	'meta_key' => '_contact_form_config',
	'label'    => __( 'Enable Google reCAPTCHA', 'dialog-contact-form' ),
	'default'  => 'no',
	'options'  => array(
		'no'  => esc_html__( 'No', 'dialog-contact-form' ),
		'yes' => esc_html__( 'Yes', 'dialog-contact-form' ),
	),
) );

/*
Dialog_Contact_Form_Metabox::buttonset( array(
	'id'       => 'formType',
	'group'    => 'config',
	'meta_key' => '_contact_form_config',
	'label'    => __( 'Form Type', 'dialog-contact-form' ),
	'default'  => 'internal',
	'options'  => array(
		'internal' => esc_html__( 'Internal', 'dialog-contact-form' ),
		'popup'    => esc_html__( 'External popup window', 'dialog-contact-form' ),
	),
) );
*/
