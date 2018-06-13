<?php

namespace DialogContactForm\Templates;

use DialogContactForm\Abstracts\Abstract_Form_Template;
use DialogContactForm\Utils;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ContactUs extends Abstract_Form_Template {

	public function __construct() {
		$this->id          = 'contact_us';
		$this->title       = __( 'Contact Us', 'dialog-contact-form' );
		$this->description = __( 'Allow your users to contact you with this simple contact form. You can add and remove fields as needed.', 'dialog-contact-form' );
	}

	/**
	 * Form fields
	 *
	 * @return array
	 */
	protected function form_fields() {
		return array(
			array(
				'field_type'     => 'text',
				'field_id'       => 'name',
				'field_name'     => 'name',
				'field_title'    => __( 'Name', 'dialog-contact-form' ),
				'required_field' => 'off',
				'field_width'    => 'is-6',
				'autocomplete'   => 'name',
				'placeholder'    => 'John Doe',
			),
			array(
				'field_type'     => 'email',
				'field_title'    => __( 'Email', 'dialog-contact-form' ),
				'field_id'       => 'email',
				'field_name'     => 'email',
				'required_field' => 'on',
				'field_width'    => 'is-6',
				'autocomplete'   => 'email',
				'placeholder'    => 'mail@example.com',
			),
			array(
				'field_type'     => 'text',
				'field_title'    => __( 'Subject', 'dialog-contact-form' ),
				'field_id'       => 'subject',
				'field_name'     => 'subject',
				'required_field' => 'off',
				'field_width'    => 'is-12',
			),
			array(
				'field_type'     => 'textarea',
				'field_title'    => __( 'Message', 'dialog-contact-form' ),
				'field_id'       => 'message',
				'field_name'     => 'message',
				'required_field' => 'on',
				'field_width'    => 'is-12',
			),
		);
	}

	/**
	 * Form settings
	 *
	 * @return array
	 */
	protected function form_settings() {
		return array(
			'labelPosition' => 'both',
			'btnLabel'      => esc_html__( 'Submit', 'dialog-contact-form' ),
			'btnAlign'      => 'left',
			'reset_form'    => 'yes',
			'recaptcha'     => 'no',
		);
	}

	/**
	 * Form actions
	 *
	 * @return array
	 */
	protected function form_actions() {
		return array(
			'store_submission'   => array(),
			'email_notification' => array(
				'receiver'    => '[system:admin_email]',
				'senderEmail' => '[email]',
				'senderName'  => '[name]',
				'subject'     => '[system:blogname] : [subject]',
				'body'        => '[all_fields_table]',
			),
			'success_message'    => array(
				'message' => Utils::get_option( 'mail_sent_ok' )
			),
			'redirect'           => array(
				'redirect_to' => 'same',
			),
		);
	}

	/**
	 * Form validation messages
	 *
	 * @return array
	 */
	protected function form_validation_messages() {
		return array(
			'mail_sent_ng'     => Utils::get_option( 'mail_sent_ng' ),
			'validation_error' => Utils::get_option( 'validation_error' ),
		);
	}
}
