<?php

namespace DialogContactForm\Actions;

use DialogContactForm\Abstracts\Abstract_Action;

class Email extends Abstract_Action {

	/**
	 * Email constructor.
	 */
	public function __construct() {
		parent::__construct();

		$this->id       = 'email';
		$this->title    = __( 'Email', 'dialog-contact-form' );
		$this->settings = array_merge( $this->settings, $this->settings() );
	}

	/**
	 * Process action
	 */
	public function process( $action_id, $form_id, $data ) {
		// TODO: Implement process() method.
	}

	private function settings() {
		return array();
	}
}