<?php

namespace DialogContactForm\Templates;

use DialogContactForm\Abstracts\Abstract_Form_Template;
use DialogContactForm\Utils;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DataExportRequest extends Abstract_Form_Template {

	public function __construct() {
		$this->priority    = 50;
		$this->id          = 'data_export_request';
		$this->title       = __( 'Data Export Request', 'dialog-contact-form' );
		$this->description = __( 'Includes action to add users to WordPress\' personal data export tool, allowing admins to comply with the GDPR and other privacy regulations from the site\'s front end.', 'dialog-contact-form' );
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
				'field_title' => __( 'HTML', 'dialog-contact-form' ),
				'html'        => __( 'Submit this form to request a personal data export from the site administrator.', 'dialog-contact-form' ),
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
				'senderName'  => '',
				'subject'     => 'Data Export Request from [email]',
				'body'        => '[email] has requested an export of the data you have collected from them on [system:siteurl]. ',
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
	protected function form_validation_messages() {
		return array(
			'mail_sent_ng'     => Utils::get_option( 'mail_sent_ng' ),
			'validation_error' => Utils::get_option( 'validation_error' ),
		);
	}
}
