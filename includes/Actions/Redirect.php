<?php

namespace DialogContactForm\Actions;

use DialogContactForm\Abstracts\Abstract_Action;

class Redirect extends Abstract_Action {

	/**
	 * Redirect constructor.
	 */
	public function __construct() {
		$this->id       = 'redirect';
		$this->title    = __( 'Redirect', 'dialog-contact-form' );
		$this->settings = $this->settings();
	}

	/**
	 * Save action settings
	 *
	 * @param int $post_id
	 * @param \WP_Post $post
	 */
	public function save( $post_id, $post ) {

		if ( ! empty( $_POST['redirect'] ) ) {
			$sanitize_data = $this->sanitize_settings( $_POST['redirect'] );

			update_post_meta( $post_id, '_action_redirect', $sanitize_data );
		} else {
			delete_post_meta( $post_id, '_action_redirect' );
		}
	}

	/**
	 * Process action
	 */
	public function process( $action_id, $form_id, $data ) {
		// TODO: Implement process() method.
	}

	private function settings() {
		return array(
			'redirect_to' => array(
				'type'        => 'select',
				'id'          => 'redirect_to',
				'group'       => 'redirect',
				'meta_key'    => '_action_redirect',
				'label'       => __( 'Redirect To', 'dialog-contact-form' ),
				'description' => __( 'After successful submit, where the page will redirect to', 'dialog-contact-form' ),
				'default'     => 'same',
				'options'     => array(
					'same' => esc_html__( 'Same Page', 'dialog-contact-form' ),
					'page' => esc_html__( 'To a page', 'dialog-contact-form' ),
					'url'  => esc_html__( 'To a custom URL', 'dialog-contact-form' ),
				),
			),
			'page_id'     => array(
				'type'     => 'pages_list',
				'id'       => 'page_id',
				'group'    => 'redirect',
				'meta_key' => '_action_redirect',
				'label'    => __( 'Page', 'dialog-contact-form' ),
				'default'  => '',
				'sanitize' => 'intval',
			),
			'url'         => array(
				'id'          => 'url',
				'group'       => 'redirect',
				'meta_key'    => '_action_redirect',
				'input_class' => 'dcf-input-text dcf-field-title',
				'label'       => __( 'Custom URL', 'dialog-contact-form' ),
				'sanitize'    => 'esc_url_raw',
			),
		);
	}
}