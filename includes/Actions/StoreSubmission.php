<?php

namespace DialogContactForm\Actions;

use DialogContactForm\Abstracts\Abstract_Action;

class StoreSubmission extends Abstract_Action {

	/**
	 * Redirect constructor.
	 */
	public function __construct() {
		$this->id       = 'store_submission';
		$this->title    = __( 'Store Submission', 'dialog-contact-form' );
		$this->settings = $this->settings();
	}

	/**
	 * Save action settings
	 *
	 * @param int $post_id
	 * @param \WP_Post $post
	 */
	public function save( $post_id, $post ) {
		// TODO: Implement save() method.
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