<?php

namespace DialogContactForm\Templates;

use DialogContactForm\Abstracts\Abstract_Form_Template;
use DialogContactForm\Utils;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Blank extends Abstract_Form_Template {


	public function __construct() {
		$this->priority    = 1;
		$this->id          = 'blank';
		$this->title       = __( 'Blank Form', 'dialog-contact-form' );
		$this->description = __( 'The blank form allows you to create any type of form using our drag & drop builder.', 'dialog-contact-form' );
	}

	/**
	 * Form fields
	 *
	 * @return array
	 */
	protected function form_fields() {
		return array();
	}

	/**
	 * Form settings
	 *
	 * @return array
	 */
	protected function form_settings() {
		return array(
			'labelPosition' => 'both',
			'btnLabel'      => '',
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
			'store_submission' => array(),
			'success_message'  => array(
				'message' => Utils::get_option( 'mail_sent_ok' )
			),
			'redirect'         => array(
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
