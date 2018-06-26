<?php

namespace DialogContactForm\Templates;

use DialogContactForm\Abstracts\Template;
use DialogContactForm\Supports\Utils;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class QuoteRequest extends Template {

	public function __construct() {
		$this->priority    = 70;
		$this->id          = 'quote_request';
		$this->title       = __( 'Quote Request', 'dialog-contact-form' );
		$this->description = __( 'Manage quote requests from your website easily with this template. You can add and remove fields as needed.',
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
				'field_type'  => 'html',
				'field_width' => 'is-12',
				'field_title' => __( 'Tell us about your project', 'dialog-contact-form' ),
				'html'        => '<h3>' . __( 'Tell us about your project', 'dialog-contact-form' ) . '</h3>',
			),
			array(
				'field_type'     => 'radio',
				'field_title'    => __( 'What services may we assist you with?', 'dialog-contact-form' ),
				'field_id'       => 'services',
				'field_name'     => 'services',
				'required_field' => 'on',
				'field_width'    => 'is-12',
				'options'        => "Consultation\nDevelopment\nDesign\nSupport"
			),
			array(
				'field_type'     => 'radio',
				'field_title'    => __( 'How urgent is this project?', 'dialog-contact-form' ),
				'field_id'       => 'is_urgent',
				'field_name'     => 'is_urgent',
				'required_field' => 'on',
				'field_width'    => 'is-12',
				'options'        => "Low\nMedium\nHigh"
			),
			array(
				'field_type'     => 'date',
				'field_title'    => __( 'Due Date', 'dialog-contact-form' ),
				'field_id'       => 'due_date',
				'field_name'     => 'due_date',
				'required_field' => 'on',
				'native_html5'   => 'on',
				'field_width'    => 'is-12',
			),
			array(
				'field_type'     => 'textarea',
				'field_title'    => __( 'Describe your project', 'dialog-contact-form' ),
				'field_id'       => 'project_description',
				'field_name'     => 'project_description',
				'required_field' => 'on',
				'field_width'    => 'is-12',
				'rows'           => 5,
			),
			array(
				'field_type'  => 'html',
				'field_width' => 'is-12',
				'field_title' => __( 'Tell us about you', 'dialog-contact-form' ),
				'html'        => '<h3>' . __( 'Tell us about you', 'dialog-contact-form' ) . '</h3>',
			),
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
				'field_type'     => 'text',
				'field_title'    => __( 'Street address', 'dialog-contact-form' ),
				'field_id'       => 'street_address',
				'field_name'     => 'street_address',
				'required_field' => 'off',
				'field_width'    => 'is-12',
				'autocomplete'   => 'address-line1',
			),
			array(
				'field_type'     => 'text',
				'field_title'    => __( 'City', 'dialog-contact-form' ),
				'field_id'       => 'city',
				'field_name'     => 'city',
				'required_field' => 'off',
				'field_width'    => 'is-6',
				'autocomplete'   => 'address-level2',
			),
			array(
				'field_type'     => 'text',
				'field_title'    => __( 'State or Province', 'dialog-contact-form' ),
				'field_id'       => 'state',
				'field_name'     => 'state',
				'required_field' => 'off',
				'field_width'    => 'is-6',
				'autocomplete'   => 'address-level1',
			),
			array(
				'field_type'     => 'text',
				'field_title'    => __( 'Zip code', 'dialog-contact-form' ),
				'field_id'       => 'postal_code',
				'field_name'     => 'postal_code',
				'required_field' => 'off',
				'field_width'    => 'is-6',
				'autocomplete'   => 'postal-code',
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
				'subject'     => 'Quote Request from [system:blogname]',
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
