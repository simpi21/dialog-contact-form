<?php

namespace DialogContactForm\Actions;

use DialogContactForm\Abstracts\Abstract_Action;
use DialogContactForm\Config;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Mailpoet3 extends Abstract_Action {

	/**
	 * MailChimp constructor.
	 * @throws \Exception
	 */
	public function __construct() {
		$this->id         = 'mailpoet3';
		$this->title      = __( 'MailPoet 3', 'dialog-contact-form' );
		$this->meta_group = 'mailpoet3';
		$this->meta_key   = '_action_mailpoet3';
		$this->settings   = $this->settings();
	}

	/**
	 * Process current action
	 *
	 * @param \DialogContactForm\Config $config Contact form configurations
	 * @param array $data User submitted sanitized data
	 *
	 * @return boolean
	 */
	public static function process( $config, $data ) {

		// If MailPoet is not available, then exit
		if ( ! class_exists( '\\MailPoet\\API\\API' ) ) {
			return false;
		}

		$action_settings = get_post_meta( $config->getFormId(), '_action_mailpoet3', true );
		$mailpoet3_lists = isset( $action_settings['mailpoet3_lists'] ) ? $action_settings['mailpoet3_lists'] : array();

		$subscriber          = array();
		$subscriber['email'] = $data[ $action_settings['mailpoet3_map_email'] ];
		if ( ! empty( $data[ $action_settings['mailpoet3_map_first_name'] ] ) ) {
			$subscriber['first_name'] = $data[ $action_settings['mailpoet3_map_first_name'] ];
		}
		if ( ! empty( $data[ $action_settings['mailpoet3_map_last_name'] ] ) ) {
			$subscriber['last_name'] = $data[ $action_settings['mailpoet3_map_last_name'] ];
		}

		if ( 'on' === $action_settings['mailpoet3_auto_confirm'] ) {
			$subscriber['status'] = \MailPoet\Models\Subscriber::STATUS_SUBSCRIBED;
		}

		try {
			\MailPoet\API\API::MP( 'v1' )->addSubscriber( $subscriber, $mailpoet3_lists );
		} catch ( \Exception $exception ) {
			// $exception->getMessage()
			return false;
		}

		return true;
	}

	/**
	 * Action settings
	 * @throws \Exception
	 */
	private function settings() {

		// If MailPoet is not available, then exit
		if ( ! class_exists( '\\MailPoet\\API\\API' ) ) {
			return array();
		}

		$config       = Config::init();
		$email_fields = array();
		$text_fields  = array(
			'' => __( '-- No Value --', 'dialog-contact-form' )
		);
		foreach ( $config->getFormFields() as $field ) {
			if ( 'email' === $field['field_type'] ) {
				$email_fields[ $field['field_id'] ] = $field['field_title'];
			}
			if ( 'text' === $field['field_type'] ) {
				$text_fields[ $field['field_id'] ] = $field['field_title'];
			}
		}

		$mailpoet3_lists = \MailPoet\API\API::MP( 'v1' )->getLists();
		$options         = [];

		foreach ( $mailpoet3_lists as $list ) {
			$options[ $list['id'] ] = $list['name'];
		}

		return array(
			'mailpoet3_lists'          => array(
				'type'     => 'select',
				'id'       => 'mailpoet3_lists',
				'group'    => $this->meta_group,
				'meta_key' => $this->meta_key,
				'label'    => __( 'List', 'dialog-contact-form' ),
				'options'  => $options,
				'multiple' => true,
			),
			'mailpoet3_auto_confirm'   => array(
				'type'     => 'buttonset',
				'id'       => 'mailpoet3_auto_confirm',
				'group'    => $this->meta_group,
				'meta_key' => $this->meta_key,
				'label'    => __( 'Auto Confirm', 'dialog-contact-form' ),
				'default'  => 'on',
				'options'  => array(
					'on'  => __( 'Yes', 'dialog-contact-form' ),
					'off' => __( 'No', 'dialog-contact-form' ),
				),
			),
			'mailpoet3_fields_map'     => array(
				'type'  => 'section',
				'label' => __( 'Field Mapping', 'dialog-contact-form' ),
			),
			'mailpoet3_map_email'      => array(
				'type'     => 'select',
				'id'       => 'mailpoet3_map_email',
				'group'    => $this->meta_group,
				'meta_key' => $this->meta_key,
				'label'    => __( 'Email Address', 'dialog-contact-form' ),
				'options'  => $email_fields,
			),
			'mailpoet3_map_first_name' => array(
				'type'     => 'select',
				'id'       => 'mailpoet3_map_first_name',
				'group'    => $this->meta_group,
				'meta_key' => $this->meta_key,
				'label'    => __( 'First Name', 'dialog-contact-form' ),
				'options'  => $text_fields,
			),
			'mailpoet3_map_last_name'  => array(
				'type'     => 'select',
				'id'       => 'mailpoet3_map_last_name',
				'group'    => $this->meta_group,
				'meta_key' => $this->meta_key,
				'label'    => __( 'Last Name', 'dialog-contact-form' ),
				'options'  => $text_fields,
			),
		);
	}

	/**
	 * Get action description
	 *
	 * @return string
	 */
	public function get_description() {
		// If MailPoet is not available, then exit
		if ( class_exists( '\\MailPoet\\API\\API' ) ) {
			return '';
		}

		$html = '<p class="description">';
		$html .= esc_html__( 'MailPoet (version 3) is not installed or activated.', 'dialog-contact-form' );
		$html .= '</p>';

		return $html;
	}
}
