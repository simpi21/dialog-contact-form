<?php

namespace DialogContactForm\Templates;

use DialogContactForm\Abstracts\Template;
use DialogContactForm\Supports\Utils;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EventRegistration extends Template {

	public function __construct() {
		$this->priority    = 30;
		$this->id          = 'event_registration';
		$this->title       = __( 'Event Registration', 'dialog-contact-form' );
		$this->description = __( 'Allow user to register for your next event this easy to complete form. You can add and remove fields as needed.',
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
				'field_title' => __( 'Event Intro Description', 'dialog-contact-form' ),
				'html'        => __( 'If you would like to take part in our event, please fill in your details in this Event Registration Form below and you will be automatically registered. Event registration must be completed at least seven (7) days prior to the event.',
					'dialog-contact-form' ),
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
				'field_title'    => __( 'Company', 'dialog-contact-form' ),
				'field_id'       => 'company',
				'field_name'     => 'company',
				'required_field' => 'off',
				'field_width'    => 'is-6',
				'autocomplete'   => 'organization',
			),
			array(
				'field_type'     => 'url',
				'field_title'    => __( 'Website', 'dialog-contact-form' ),
				'field_id'       => 'website',
				'field_name'     => 'website',
				'required_field' => 'off',
				'field_width'    => 'is-6',
				'autocomplete'   => 'url',
			),
			array(
				'field_type'     => 'select',
				'field_title'    => __( 'How many friends will you bring along?', 'dialog-contact-form' ),
				'field_id'       => 'friends',
				'field_name'     => 'friends',
				'required_field' => 'on',
				'field_width'    => 'is-12',
				'options'        => "None\nOne\nTwo\nThree\nFour\nFive"
			),
			array(
				'field_type'     => 'radio',
				'field_title'    => __( 'Any food requirements?', 'dialog-contact-form' ),
				'field_id'       => 'food_requirements',
				'field_name'     => 'food_requirements',
				'required_field' => 'on',
				'field_width'    => 'is-6',
				'options'        => "None\nVegan\nVegitarian\nGluten Free"
			),
			array(
				'field_type'     => 'radio',
				'field_title'    => __( 'Preferred drink?', 'dialog-contact-form' ),
				'field_id'       => 'preferred_drink',
				'field_name'     => 'preferred_drink',
				'required_field' => 'on',
				'field_width'    => 'is-6',
				'options'        => "No Preference\nWhite wine\nRed wine\nBeer"
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
			'btnLabel'      => esc_html__( 'Register', 'dialog-contact-form' ),
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
				'message' => __( 'Thank you for registering for our event.', 'dialog-contact-form' ),
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
