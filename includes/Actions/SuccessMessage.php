<?php

namespace DialogContactForm\Actions;

use DialogContactForm\Abstracts\Abstract_Action;

class SuccessMessage extends Abstract_Action {

	/**
	 * Email constructor.
	 */
	public function __construct() {
		$this->id    = 'success_message';
		$this->title = __( 'Success Message', 'dialog-contact-form' );
	}

	/**
	 * Process action
	 */
	public function process( $action_id, $form_id, $data ) {
		// TODO: Implement process() method.
	}
}