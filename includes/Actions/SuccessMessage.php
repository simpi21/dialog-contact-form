<?php

namespace DialogContactForm\Actions;

use DialogContactForm\Abstracts\Abstract_Action;

class SuccessMessage extends Abstract_Action {

	/**
	 * SuccessMessage constructor.
	 */
	public function __construct() {
		$this->id       = 'success_message';
		$this->title    = __( 'Success Message', 'dialog-contact-form' );
		$this->settings = $this->settings();
	}

	/**
	 * Process action
	 *
	 * @param int $form_id
	 * @param array $data
	 */
	public function process( $form_id, $data ) {
		// TODO: Implement process() method.
	}

	private function settings() {
		$default = get_dialog_contact_form_option( 'mail_sent_ok' );

		return array(
			'message' => array(
				'type'        => 'textarea',
				'id'          => 'message',
				'group'       => 'success_message',
				'meta_key'    => '_action_success_message',
				'label'       => __( 'Message', 'dialog-contact-form' ),
				'description' => __( 'Enter success message. You can also use HTML markup.', 'dialog-contact-form' ),
				'sanitize'    => array( 'DialogContactForm\\Supports\\Sanitize', 'html' ),
				'rows'        => 5,
				'default'     => $default,
			),
		);
	}

	/**
	 * Save action settings
	 *
	 * @param int $post_id
	 * @param \WP_Post $post
	 */
	public function save( $post_id, $post ) {

		if ( ! empty( $_POST['success_message'] ) ) {
			$sanitize_data = $this->sanitize_settings( $_POST['success_message'] );

			update_post_meta( $post_id, '_action_success_message', $sanitize_data );
		} else {
			delete_post_meta( $post_id, '_action_success_message' );
		}
	}
}