<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

$messages = dcf_validation_messages();

Dialog_Contact_Form_Metabox::textarea( array(
	'id'       => 'mail_sent_ok',
	'group'    => 'messages',
	'meta_key' => '_contact_form_messages',
	'label'    => __( 'Message sent successfully', 'dialog-contact-form' ),
	'default'  => $messages['mail_sent_ok'],
) );
Dialog_Contact_Form_Metabox::textarea( array(
	'id'       => 'mail_sent_ng',
	'group'    => 'messages',
	'meta_key' => '_contact_form_messages',
	'label'    => __( 'Message failed to sent', 'dialog-contact-form' ),
	'default'  => $messages['mail_sent_ng'],
) );
Dialog_Contact_Form_Metabox::textarea( array(
	'id'       => 'validation_error',
	'group'    => 'messages',
	'meta_key' => '_contact_form_messages',
	'label'    => __( 'Validation errors occurred', 'dialog-contact-form' ),
	'default'  => $messages['validation_error'],
) );
Dialog_Contact_Form_Metabox::text( array(
	'id'       => 'invalid_required',
	'group'    => 'messages',
	'meta_key' => '_contact_form_messages',
	'label'    => __( 'Required field', 'dialog-contact-form' ),
	'default'  => $messages['invalid_required'],
) );
Dialog_Contact_Form_Metabox::text( array(
	'id'       => 'invalid_email',
	'group'    => 'messages',
	'meta_key' => '_contact_form_messages',
	'label'    => __( 'Invalid email', 'dialog-contact-form' ),
	'default'  => $messages['invalid_email'],
) );
Dialog_Contact_Form_Metabox::text( array(
	'id'       => 'invalid_url',
	'group'    => 'messages',
	'meta_key' => '_contact_form_messages',
	'label'    => __( 'Invalid URL', 'dialog-contact-form' ),
	'default'  => $messages['invalid_url'],
) );
Dialog_Contact_Form_Metabox::text( array(
	'id'       => 'invalid_too_long',
	'group'    => 'messages',
	'meta_key' => '_contact_form_messages',
	'label'    => __( 'Exceed max length', 'dialog-contact-form' ),
	'default'  => $messages['invalid_too_long'],
) );
Dialog_Contact_Form_Metabox::text( array(
	'id'       => 'invalid_too_short',
	'group'    => 'messages',
	'meta_key' => '_contact_form_messages',
	'label'    => __( 'Too short', 'dialog-contact-form' ),
	'default'  => $messages['invalid_too_short'],
) );
Dialog_Contact_Form_Metabox::text( array(
	'id'       => 'invalid_number',
	'group'    => 'messages',
	'meta_key' => '_contact_form_messages',
	'label'    => __( 'Invalid number', 'dialog-contact-form' ),
	'default'  => $messages['invalid_number'],
) );
Dialog_Contact_Form_Metabox::text( array(
	'id'       => 'number_too_small',
	'group'    => 'messages',
	'meta_key' => '_contact_form_messages',
	'label'    => __( 'Number too short', 'dialog-contact-form' ),
	'default'  => $messages['number_too_small'],
) );
Dialog_Contact_Form_Metabox::text( array(
	'id'       => 'number_too_large',
	'group'    => 'messages',
	'meta_key' => '_contact_form_messages',
	'label'    => __( 'Number too large', 'dialog-contact-form' ),
	'default'  => $messages['number_too_large'],
) );
Dialog_Contact_Form_Metabox::text( array(
	'id'       => 'invalid_int',
	'group'    => 'messages',
	'meta_key' => '_contact_form_messages',
	'label'    => __( 'Invalid integer', 'dialog-contact-form' ),
	'default'  => $messages['invalid_int'],
) );
Dialog_Contact_Form_Metabox::text( array(
	'id'       => 'invalid_alpha',
	'group'    => 'messages',
	'meta_key' => '_contact_form_messages',
	'label'    => __( 'Invalid alphabetic letters', 'dialog-contact-form' ),
	'default'  => $messages['invalid_alpha'],
) );
Dialog_Contact_Form_Metabox::text( array(
	'id'       => 'invalid_alnum',
	'group'    => 'messages',
	'meta_key' => '_contact_form_messages',
	'label'    => __( 'Invalid alphanumeric characters', 'dialog-contact-form' ),
	'default'  => $messages['invalid_alnum'],
) );
Dialog_Contact_Form_Metabox::text( array(
	'id'       => 'invalid_alnumdash',
	'group'    => 'messages',
	'meta_key' => '_contact_form_messages',
	'label'    => __( 'Invalid alphanumeric characters, dashes and underscores', 'dialog-contact-form' ),
	'default'  => $messages['invalid_alnumdash'],
) );
Dialog_Contact_Form_Metabox::text( array(
	'id'       => 'invalid_date',
	'group'    => 'messages',
	'meta_key' => '_contact_form_messages',
	'label'    => __( 'Invalid date', 'dialog-contact-form' ),
	'default'  => $messages['invalid_date'],
) );
Dialog_Contact_Form_Metabox::text( array(
	'id'       => 'invalid_ip',
	'group'    => 'messages',
	'meta_key' => '_contact_form_messages',
	'label'    => __( 'Invalid IP address', 'dialog-contact-form' ),
	'default'  => $messages['invalid_ip'],
) );
Dialog_Contact_Form_Metabox::text( array(
	'id'       => 'invalid_user_login',
	'group'    => 'messages',
	'meta_key' => '_contact_form_messages',
	'label'    => __( 'Invalid user login', 'dialog-contact-form' ),
	'default'  => $messages['invalid_user_login'],
) );
Dialog_Contact_Form_Metabox::text( array(
	'id'       => 'invalid_username',
	'group'    => 'messages',
	'meta_key' => '_contact_form_messages',
	'label'    => __( 'Invalid username', 'dialog-contact-form' ),
	'default'  => $messages['invalid_username'],
) );
Dialog_Contact_Form_Metabox::text( array(
	'id'       => 'invalid_user_email',
	'group'    => 'messages',
	'meta_key' => '_contact_form_messages',
	'label'    => __( 'Invalid user email', 'dialog-contact-form' ),
	'default'  => $messages['invalid_user_email'],
) );
Dialog_Contact_Form_Metabox::text( array(
	'id'       => 'invalid_recaptcha',
	'group'    => 'messages',
	'meta_key' => '_contact_form_messages',
	'label'    => __( 'Invalid reCAPTCHA', 'dialog-contact-form' ),
	'default'  => $messages['invalid_recaptcha'],
) );
