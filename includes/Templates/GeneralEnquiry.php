<?php

namespace DialogContactForm\Templates;

use DialogContactForm\Abstracts\Template;
use DialogContactForm\Supports\Utils;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class GeneralEnquiry extends Template {

	public function __construct() {
		$this->priority    = 60;
		$this->id          = 'general_enquiry';
		$this->title       = __( 'General Enquiry', 'dialog-contact-form' );
		$this->description = __( 'Collect user enquiries with this simple, generic form. You can add and remove fields as needed.',
			'dialog-contact-form' );
	}

	/**
	 * Form fields
	 *
	 * @return array
	 */
	protected function formFields() {
		return array(
			array(
				'field_type'     => 'text',
				'field_id'       => 'first_name',
				'field_name'     => 'first_name',
				'field_title'    => __( 'First Name', 'dialog-contact-form' ),
				'required_field' => 'on',
				'field_width'    => 'is-6',
				'autocomplete'   => 'given-name',
				'placeholder'    => 'John',
			),
			array(
				'field_type'     => 'text',
				'field_id'       => 'last_name',
				'field_name'     => 'last_name',
				'field_title'    => __( 'Last Name', 'dialog-contact-form' ),
				'required_field' => 'on',
				'field_width'    => 'is-6',
				'autocomplete'   => 'family-name',
				'placeholder'    => 'Doe',
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
				'field_title'    => __( 'Phone', 'dialog-contact-form' ),
				'field_id'       => 'phone',
				'field_name'     => 'phone',
				'required_field' => 'off',
				'field_width'    => 'is-6',
				'autocomplete'   => 'tel',
			),
			array(
				'field_type'     => 'radio',
				'field_title'    => __( 'Occupation', 'dialog-contact-form' ),
				'field_id'       => 'occupation',
				'field_name'     => 'occupation',
				'required_field' => 'on',
				'field_width'    => 'is-12',
				'options'        => "Choice 1\nChoice 2\nChoice 3\nChoice 4"
			),
			array(
				'field_type'     => 'select',
				'field_title'    => __( 'Enquiry Type', 'dialog-contact-form' ),
				'field_id'       => 'enquiry_type',
				'field_name'     => 'enquiry_type',
				'required_field' => 'on',
				'field_width'    => 'is-12',
				'options'        => "Choice 1\nChoice 2\nChoice 3\nChoice 4"
			),
			array(
				'field_type'     => 'textarea',
				'field_title'    => __( 'Details', 'dialog-contact-form' ),
				'field_id'       => 'details',
				'field_name'     => 'details',
				'required_field' => 'on',
				'field_width'    => 'is-12',
				'rows'           => 5,
			),
			array(
				'field_type'      => 'acceptance',
				'field_title'     => __( 'May We Contact You?', 'dialog-contact-form' ),
				'acceptance_text' => __( 'May We Contact You?', 'dialog-contact-form' ),
				'field_id'        => 'may_we_contact_you',
				'field_name'      => 'may_we_contact_you',
				'field_width'     => 'is-12',
			),
			array(
				'field_type'  => 'checkbox',
				'field_title' => __( 'Best Time to Call', 'dialog-contact-form' ),
				'field_id'    => 'best_time_to_call',
				'field_name'  => 'best_time_to_call',
				'field_width' => 'is-12',
				'options'     => "Morning\nAfternoon\nEvening"
			),
		);
	}

	/**
	 * Form settings
	 *
	 * @return array
	 */
	protected function formSettings() {
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
	protected function formActions() {
		return array(
			'store_submission'   => array(),
			'email_notification' => array(
				'receiver'    => '[system:admin_email]',
				'senderEmail' => '[email]',
				'senderName'  => '[first_name] [last_name]',
				'subject'     => 'General Enquiry from [system:blogname]',
				'body'        => '[all_fields_table] ',
			),
			'success_message'    => array(
				'message' => __( 'Your form has been successfully submitted.', 'dialog-contact-form' ),
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
	protected function formValidationMessages() {
		return array(
			'mail_sent_ng'     => Utils::get_option( 'mail_sent_ng' ),
			'validation_error' => Utils::get_option( 'validation_error' ),
		);
	}
}
