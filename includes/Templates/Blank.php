<?php

namespace DialogContactForm\Templates;

use DialogContactForm\Abstracts\Abstract_Form_Template;

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
		return array();
	}

	/**
	 * Form actions
	 *
	 * @return array
	 */
	protected function form_actions() {
		return array();
	}

	/**
	 * Form validation messages
	 *
	 * @return array
	 */
	protected function form_validation_messages() {
		return array();
	}
}
