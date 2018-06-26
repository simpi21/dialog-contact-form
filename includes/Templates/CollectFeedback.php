<?php

namespace DialogContactForm\Templates;

use DialogContactForm\Abstracts\Template;
use DialogContactForm\Supports\Utils;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CollectFeedback extends Template {

	public function __construct() {
		$this->priority    = 10;
		$this->id          = 'collect_feedback';
		$this->title       = __( 'Collect Feedback', 'dialog-contact-form' );
		$this->description = __( 'Collect feedback for an event, blog post, or anything else. You can add and remove fields as needed.',
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
				'required_field' => 'off',
				'field_width'    => 'is-6',
				'autocomplete'   => 'given-name',
				'placeholder'    => 'John',
			),
			array(
				'field_type'     => 'text',
				'field_id'       => 'last_name',
				'field_name'     => 'last_name',
				'field_title'    => __( 'Last Name', 'dialog-contact-form' ),
				'required_field' => 'off',
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
				'field_width'    => 'is-12',
				'autocomplete'   => 'email',
				'placeholder'    => 'mail@example.com',
			),
			array(
				'field_type'     => 'radio',
				'field_title'    => __( 'How would you rate our last newsletter?', 'dialog-contact-form' ),
				'field_id'       => 'last_newsletter_rate',
				'field_name'     => 'last_newsletter_rate',
				'required_field' => 'off',
				'field_width'    => 'is-12',
				'options'        => "Very Satisfied\nSatisfied\nUnsatisfied\nVery Unsatisfied"
			),
			array(
				'field_type'     => 'radio',
				'field_title'    => __( 'How would you rate our last blog article??', 'dialog-contact-form' ),
				'field_id'       => 'last_article_rate',
				'field_name'     => 'last_article_rate',
				'required_field' => 'off',
				'field_width'    => 'is-12',
				'options'        => "Very Satisfied\nSatisfied\nUnsatisfied\nVery Unsatisfied"
			),
			array(
				'field_type'     => 'textarea',
				'field_title'    => __( 'Any additional feedback?', 'dialog-contact-form' ),
				'field_id'       => 'additional_feedback',
				'field_name'     => 'additional_feedback',
				'required_field' => 'off',
				'field_width'    => 'is-12',
				'rows'           => 5,
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
				'subject'     => 'New Event Registration from [first_name] [last_name]',
				'body'        => '[all_fields_table]',
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
