<?php

namespace DialogContactForm\Templates;

use DialogContactForm\Abstracts\Template;
use DialogContactForm\Supports\Utils;

class JobApplication extends Template {

	public function __construct() {
		$this->priority    = 70;
		$this->id          = 'job_application';
		$this->title       = __( 'Job Application', 'dialog-contact-form' );
		$this->description = __( 'Allow users to apply for a job. You can add and remove fields as needed.', 'dialog-contact-form' );
	}

	/**
	 * Form fields
	 *
	 * @return array
	 */
	protected function form_fields() {
		return array(
			array(
				'field_type'  => 'html',
				'field_width' => 'is-12',
				'field_title' => __( 'Personal Information', 'dialog-contact-form' ),
				'html'        => '<h3>' . __( 'Personal Information', 'dialog-contact-form' ) . '</h3>',
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
			array(
				'field_type'     => 'text',
				'field_title'    => __( 'Country', 'dialog-contact-form' ),
				'field_id'       => 'country',
				'field_name'     => 'country',
				'required_field' => 'off',
				'field_width'    => 'is-6',
				'autocomplete'   => 'country',
			),
			array(
				'field_type'  => 'html',
				'field_width' => 'is-12',
				'field_title' => __( 'Work History', 'dialog-contact-form' ),
				'html'        => '<h3>' . __( 'Work History', 'dialog-contact-form' ) . '</h3>',
			),
			array(
				'field_type'     => 'text',
				'field_title'    => __( 'Previous Job Title', 'dialog-contact-form' ),
				'field_id'       => 'previous_job_title',
				'field_name'     => 'previous_job_title',
				'required_field' => 'off',
				'field_width'    => 'is-12',
				'autocomplete'   => 'organization-title',
			),
			array(
				'field_type'     => 'date',
				'field_title'    => __( 'Previous Job Started Date', 'dialog-contact-form' ),
				'field_id'       => 'previous_job_started_date',
				'field_name'     => 'previous_job_started_date',
				'required_field' => 'off',
				'native_html5'   => 'on',
				'field_width'    => 'is-6',
			),
			array(
				'field_type'     => 'date',
				'field_title'    => __( 'Previous Job Ended Date', 'dialog-contact-form' ),
				'field_id'       => 'previous_job_ended_date',
				'field_name'     => 'previous_job_ended_date',
				'required_field' => 'off',
				'native_html5'   => 'on',
				'field_width'    => 'is-6',
			),
			array(
				'field_type'     => 'textarea',
				'field_title'    => __( 'Previous Job Description', 'dialog-contact-form' ),
				'field_id'       => 'previous_job_description',
				'field_name'     => 'previous_job_description',
				'required_field' => 'off',
				'field_width'    => 'is-12',
				'rows'           => 5,
			),
			array(
				'field_type'  => 'html',
				'field_width' => 'is-12',
				'field_title' => __( 'References', 'dialog-contact-form' ),
				'html'        => '<h3>' . __( 'References', 'dialog-contact-form' ) . '</h3>',
			),
			array(
				'field_type'     => 'text',
				'field_title'    => __( 'Name 1', 'dialog-contact-form' ),
				'field_id'       => 'reference_name_1',
				'field_name'     => 'reference_name_1',
				'required_field' => 'off',
				'field_width'    => 'is-6',
			),
			array(
				'field_type'     => 'text',
				'field_title'    => __( 'Phone 1', 'dialog-contact-form' ),
				'field_id'       => 'reference_phone_1',
				'field_name'     => 'reference_phone_1',
				'required_field' => 'off',
				'field_width'    => 'is-6',
			),
			array(
				'field_type'     => 'text',
				'field_title'    => __( 'Name 2', 'dialog-contact-form' ),
				'field_id'       => 'reference_name_2',
				'field_name'     => 'reference_name_2',
				'required_field' => 'off',
				'field_width'    => 'is-6',
			),
			array(
				'field_type'     => 'text',
				'field_title'    => __( 'Phone 2', 'dialog-contact-form' ),
				'field_id'       => 'reference_phone_2',
				'field_name'     => 'reference_phone_2',
				'required_field' => 'off',
				'field_width'    => 'is-6',
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
				'senderName'  => '[first_name] [last_name]',
				'subject'     => 'Job Application from [system:blogname]',
				'body'        => '[all_fields_table] ',
			),
			'success_message'    => array(
				'message' => __( 'Thank you for your application! We will be in touch soon.', 'dialog-contact-form' ),
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