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
