<?php

use DialogContactForm\Supports\Metabox;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$messages = dcf_validation_messages();

Metabox::textarea( array(
	'id'       => 'mail_sent_ng',
	'group'    => 'messages',
	'meta_key' => '_contact_form_messages',
	'label'    => __( 'Message failed to sent', 'dialog-contact-form' ),
	'default'  => $messages['mail_sent_ng'],
) );
Metabox::textarea( array(
	'id'       => 'validation_error',
	'group'    => 'messages',
	'meta_key' => '_contact_form_messages',
	'label'    => __( 'Validation errors occurred', 'dialog-contact-form' ),
	'default'  => $messages['validation_error'],
) );
