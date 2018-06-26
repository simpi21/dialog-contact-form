<?php

namespace DialogContactForm\Actions;

use DialogContactForm\Abstracts\Action;
use DialogContactForm\Supports\Utils;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SuccessMessage extends Action {

	/**
	 * SuccessMessage constructor.
	 */
	public function __construct() {
		$this->priority   = 290;
		$this->id         = 'success_message';
		$this->title      = __( 'Success Message', 'dialog-contact-form' );
		$this->meta_group = 'success_message';
		$this->meta_key   = '_action_success_message';
		$this->settings   = $this->settings();
	}

	/**
	 * Process current action
	 *
	 * @param \DialogContactForm\Supports\Config $config Contact form configurations
	 * @param array $data User submitted sanitized data
	 *
	 * @return mixed
	 */
	public static function process( $config, $data ) {
		$message = get_post_meta( $config->getFormId(), '_action_success_message', true );
		if ( empty( $message['message'] ) ) {
			return false;
		}

		return esc_attr( $message['message'] );
	}

	/**
	 * Action settings
	 *
	 * @return array
	 */
	private function settings() {
		$default = Utils::get_option( 'mail_sent_ok' );

		return array(
			'message' => array(
				'type'        => 'textarea',
				'id'          => 'message',
				'group'       => $this->meta_group,
				'meta_key'    => $this->meta_key,
				'label'       => __( 'Message', 'dialog-contact-form' ),
				'description' => __( 'Enter success message. You can also use HTML markup.', 'dialog-contact-form' ),
				'sanitize'    => 'wp_kses_post',
				'rows'        => 5,
				'default'     => $default,
			),
		);
	}
}
