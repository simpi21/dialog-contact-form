<?php

namespace DialogContactForm\Actions;

use DialogContactForm\Abstracts\Abstract_Action;
use DialogContactForm\Supports\MailChimpHandler;

class MailChimp extends Abstract_Action {

	/**
	 * MailChimp constructor.
	 */
	public function __construct() {
		$this->id       = 'mail_chimp';
		$this->title    = __( 'MailChimp', 'dialog-contact-form' );
		$this->settings = $this->settings();
	}

	/**
	 * Save action settings
	 *
	 * @param int $post_id
	 * @param \WP_Post $post
	 */
	public function save( $post_id, $post ) {
		try {
			$api_key = get_dialog_contact_form_option( 'mail_chimp_api_key' );
			$handler = new MailchimpHandler( $api_key );
		} catch ( \Exception $exception ) {

		}
	}

	private function settings() {
		return array();
	}
}