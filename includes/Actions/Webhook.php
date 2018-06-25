<?php

namespace DialogContactForm\Actions;

use DialogContactForm\Abstracts\Action;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Webhook extends Action {

	/**
	 * Redirect constructor.
	 */
	public function __construct() {
		$this->priority   = 60;
		$this->id         = 'webhook';
		$this->title      = __( 'Webhook', 'dialog-contact-form' );
		$this->meta_group = 'webhook';
		$this->meta_key   = '_action_webhook';
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
		$form_id  = $config->getFormId();
		$settings = get_post_meta( $form_id, '_action_webhook', true );

		if ( empty( $settings['webhook_url'] ) ) {
			return;
		}

		if ( isset( $settings['webhook_advanced_data'] ) && 'on' === $settings['webhook_advanced_data'] ) {
			$body['form'] = array(
				'id'   => $form_id,
				'name' => get_the_title( $form_id ),
			);

			$body['fields'] = $data;
			$body['meta']   = $config->getMetaData();
		} else {
			$body              = $data;
			$body['form_id']   = $settings['id'];
			$body['form_name'] = get_the_title( $form_id );
		}

		$args = array(
			'body' => $body,
		);

		/**
		 * Forms webhook request arguments.
		 *
		 * Filters the request arguments delivered by the form webhook when executing
		 * an ajax request.
		 *
		 * @param array $args Webhook request arguments.
		 * @param int $record An instance of the form record.
		 */
		$args = apply_filters( 'dialog_contact_form/webhook/request_args', $args, $form_id );

		$response = wp_remote_post( $settings['webhook_url'], $args );

		/**
		 * Form webhook response.
		 *
		 * Fires when the webhook response is retrieved.
		 *
		 * @param \WP_Error|array $response The response or WP_Error on failure.
		 * @param int $record An instance of the form record.
		 */
		do_action( 'dialog_contact_form/webhook/response', $response, $form_id );
	}

	private function settings() {
		return array(
			'webhook_url'           => array(
				'id'          => 'webhook_url',
				'group'       => $this->meta_group,
				'meta_key'    => $this->meta_key,
				'label'       => __( 'Webhook URL', 'dialog-contact-form' ),
				'description' => __( 'Enter the integration URL (like Zapier) that will receive the form\'s submitted data.', 'dialog-contact-form' ),
				'placeholder' => __( 'https://your-webhook-url.com', 'dialog-contact-form' ),
				'sanitize'    => 'esc_url_raw',
			),
			'webhook_advanced_data' => array(
				'type'     => 'buttonset',
				'id'       => 'webhook_advanced_data',
				'group'    => $this->meta_group,
				'meta_key' => $this->meta_key,
				'label'    => __( 'Advanced Data', 'dialog-contact-form' ),
				'default'  => 'off',
				'options'  => array(
					'on'  => __( 'Yes', 'dialog-contact-form' ),
					'off' => __( 'No', 'dialog-contact-form' ),
				),
			),
		);
	}
}