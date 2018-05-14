<?php

namespace DialogContactForm\Actions;

use DialogContactForm\Abstracts\Abstract_Action;
use DialogContactForm\Entries\Entry;

class StoreSubmission extends Abstract_Action {

	/**
	 * Comment status
	 *
	 * @var string
	 */
	private static $comment_status = 'closed';

	/**
	 * Ping status
	 *
	 * @var string
	 */
	private static $ping_status = 'closed';

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
	 *
	 * @param int $form_id
	 * @param array $data
	 *
	 * @return bool
	 */
	public static function process( $form_id, $data ) {

		// Remove attachment data
		unset( $data['dcf_attachments'] );

		$entry = new Entry();

		return $entry->insert( $data );
	}

	private function settings() {
		return array();
	}
}