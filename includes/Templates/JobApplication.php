<?php

namespace DialogContactForm\Templates;

use DialogContactForm\Abstracts\Abstract_Form_Template;
use DialogContactForm\Utils;

class JobApplication extends Abstract_Form_Template {

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
		// TODO: Implement form_fields() method.
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