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
		$this->priority = 10;
		$this->id       = 'store_submission';
		$this->title    = __( 'Store Submission', 'dialog-contact-form' );
	}

	/**
	 * Process current action
	 *
	 * @param \DialogContactForm\Config $config Contact form configurations
	 * @param array $data User submitted sanitized data
	 *
	 * @return mixed
	 */
	public static function process( $config, $data ) {
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
