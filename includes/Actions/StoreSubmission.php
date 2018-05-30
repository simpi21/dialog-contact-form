<?php

namespace DialogContactForm\Actions;

use DialogContactForm\Abstracts\Abstract_Action;
use DialogContactForm\Entries\Entry;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class StoreSubmission extends Abstract_Action {

	/**
	 * Redirect constructor.
	 */
	public function __construct() {
		$this->id    = 'store_submission';
		$this->title = __( 'Store Submission', 'dialog-contact-form' );
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
		$entry = new Entry();

		return $entry->insert( $data );
	}

	/**
	 * Get action description
	 *
	 * @return string
	 */
	public function get_description() {
		$html = '<p class="description">';
		$html .= esc_html__( 'No settings are available for this action.', 'dialog-contact-form' );
		$html .= '</p>';

		return $html;
	}
}
