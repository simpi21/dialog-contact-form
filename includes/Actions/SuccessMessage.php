<?php

namespace DialogContactForm\Actions;

use DialogContactForm\Abstracts\Abstract_Action;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SuccessMessage extends Abstract_Action {

	/**
	 * SuccessMessage constructor.
	 */
	public function __construct() {
		$this->id         = 'success_message';
		$this->title      = __( 'Success Message', 'dialog-contact-form' );
		$this->meta_group = 'success_message';
		$this->meta_key   = '_action_success_message';
		$this->settings   = $this->settings();
	}

	/**
	 * Process action
	 *
	 * @param int $form_id
	 * @param array $data
	 *
	 * @return string
	 */
	public static function process( $form_id, $data ) {
		$message = get_post_meta( $form_id, '_action_success_message', true );
		if ( empty( $message['message'] ) ) {
			return false;
		}

		return esc_attr( $message['message'] );
	}

	private function settings() {
		$default = get_dialog_contact_form_option( 'mail_sent_ok' );

		return array(
			'message' => array(
				'type'        => 'textarea',
				'id'          => 'message',
				'group'       => $this->meta_group,
				'meta_key'    => $this->meta_key,
				'label'       => __( 'Message', 'dialog-contact-form' ),
				'description' => __( 'Enter success message. You can also use HTML markup.', 'dialog-contact-form' ),
				'sanitize'    => array( 'DialogContactForm\\Supports\\Sanitize', 'html' ),
				'rows'        => 5,
				'default'     => $default,
			),
		);
	}
}