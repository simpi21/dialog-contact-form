<?php

namespace DialogContactForm\Actions;

use DialogContactForm\Abstracts\Abstract_Action;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Redirect extends Abstract_Action {

	/**
	 * Redirect constructor.
	 */
	public function __construct() {
		$this->id         = 'redirect';
		$this->title      = __( 'Redirect', 'dialog-contact-form' );
		$this->meta_group = 'redirect';
		$this->meta_key   = '_action_redirect';
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
		$_redirect   = get_post_meta( $form_id, '_action_redirect', true );
		$redirect_to = ! empty( $_redirect['redirect_to'] ) ? $_redirect['redirect_to'] : 'same';
		$page_id     = ! empty( $_redirect['page_id'] ) ? intval( $_redirect['page_id'] ) : 0;
		$url         = ! empty( $_redirect['url'] ) ? esc_url( $_redirect['url'] ) : null;

		if ( 'same' == $redirect_to ) {
			return '';
		}
		if ( 'page' == $redirect_to && $page_id ) {
			return get_permalink( $page_id );
		}
		if ( 'url' == $redirect_to && $url ) {
			return $url;
		}


		return '';
	}

	private function settings() {
		return array(
			'redirect_to' => array(
				'type'        => 'select',
				'id'          => 'redirect_to',
				'group'       => $this->meta_group,
				'meta_key'    => $this->meta_key,
				'label'       => __( 'Redirect To', 'dialog-contact-form' ),
				'description' => __( 'After successful submit, where the page will redirect to',
					'dialog-contact-form' ),
				'default'     => 'same',
				'options'     => array(
					'same' => esc_html__( 'Same Page', 'dialog-contact-form' ),
					'page' => esc_html__( 'To a page', 'dialog-contact-form' ),
					'url'  => esc_html__( 'To a custom URL', 'dialog-contact-form' ),
				),
			),
			'page_id'     => array(
				'type'        => 'pages_list',
				'id'          => 'page_id',
				'group'       => $this->meta_group,
				'meta_key'    => $this->meta_key,
				'group_class' => 'dcf-input-group col-redirect-page_id',
				'label'       => __( 'Page', 'dialog-contact-form' ),
				'default'     => '',
				'sanitize'    => 'intval',
			),
			'url'         => array(
				'id'          => 'url',
				'group'       => $this->meta_group,
				'meta_key'    => $this->meta_key,
				'group_class' => 'dcf-input-group col-redirect-url',
				'input_class' => 'dcf-input-text dcf-field-title',
				'label'       => __( 'Custom URL', 'dialog-contact-form' ),
				'sanitize'    => 'esc_url_raw',
			),
		);
	}
}