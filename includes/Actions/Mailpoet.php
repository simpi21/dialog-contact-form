<?php

namespace DialogContactForm\Actions;

use DialogContactForm\Abstracts\Abstract_Action;
use DialogContactForm\Config;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Mailpoet extends Abstract_Action {

	/**
	 * MailChimp constructor.
	 */
	public function __construct() {
		$this->id         = 'mailpoet';
		$this->title      = __( 'MailPoet', 'dialog-contact-form' );
		$this->meta_group = 'mailpoet';
		$this->meta_key   = '_action_mailpoet';
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
		if ( ! class_exists( 'WYSIJA' ) ) {
			return false;
		}

		$action_settings = get_post_meta( $config->getFormId(), '_action_mailpoet', true );
		$mailpoet_lists  = isset( $action_settings['mailpoet_lists'] ) ? $action_settings['mailpoet_lists'] : array();

		$subscriber = array(
			'user'      => array(
				'email' => $data[ $action_settings['mailpoet_map_email'] ],
			),
			'user_list' => array( 'list_ids' => (array) $mailpoet_lists ),
		);

		if ( ! empty( $data[ $action_settings['mailpoet_map_first_name'] ] ) ) {
			$subscriber['user']['firstname'] = $data[ $action_settings['mailpoet_map_first_name'] ];
		}
		if ( ! empty( $data[ $action_settings['mailpoet_map_last_name'] ] ) ) {
			$subscriber['user']['lastname'] = $data[ $action_settings['mailpoet_map_last_name'] ];
		}

		try {
			/** @var \WYSIJA_help_user $helper_user */
			$helper_user = \WYSIJA::get( 'user', 'helper' );
			$helper_user->addSubscriber( $subscriber );
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
		if ( ! class_exists( 'WYSIJA' ) ) {
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

		/** @var \WYSIJA_model_list $model_list */
		$model_list     = \WYSIJA::get( 'list', 'model' );
		$mailpoet_lists = $model_list->get( [ 'name', 'list_id' ], array( 'is_enabled' => 1 ) );
		$options        = [];

		foreach ( $mailpoet_lists as $list ) {
			$options[ $list['list_id'] ] = $list['name'];
		}

		return array(
			'mailpoet_lists'          => array(
				'type'     => 'select',
				'id'       => 'mailpoet_lists',
				'group'    => $this->meta_group,
				'meta_key' => $this->meta_key,
				'label'    => __( 'List', 'dialog-contact-form' ),
				'options'  => $options,
				'multiple' => true,
			),
			'mailpoet_fields_map'     => array(
				'type'  => 'section',
				'label' => __( 'Field Mapping', 'dialog-contact-form' ),
			),
			'mailpoet_map_email'      => array(
				'type'     => 'select',
				'id'       => 'mailpoet_map_email',
				'group'    => $this->meta_group,
				'meta_key' => $this->meta_key,
				'label'    => __( 'Email Address', 'dialog-contact-form' ),
				'options'  => $email_fields,
			),
			'mailpoet_map_first_name' => array(
				'type'     => 'select',
				'id'       => 'mailpoet_map_first_name',
				'group'    => $this->meta_group,
				'meta_key' => $this->meta_key,
				'label'    => __( 'First Name', 'dialog-contact-form' ),
				'options'  => $text_fields,
			),
			'mailpoet_map_last_name'  => array(
				'type'     => 'select',
				'id'       => 'mailpoet_map_last_name',
				'group'    => $this->meta_group,
				'meta_key' => $this->meta_key,
				'label'    => __( 'Last Name', 'dialog-contact-form' ),
				'options'  => $text_fields,
			),
		);
	}
}
