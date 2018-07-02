<?php

namespace DialogContactForm\Actions;

use DialogContactForm\Abstracts\Action;
use DialogContactForm\Supports\Config;
use DialogContactForm\Supports\MailChimpHandler;
use DialogContactForm\Supports\Utils;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MailChimp extends Action {

	private $api_key;

	/**
	 * MailChimp constructor.
	 */
	public function __construct() {
		$this->priority   = 30;
		$this->id         = 'mailchimp';
		$this->title      = __( 'MailChimp', 'dialog-contact-form' );
		$this->meta_group = 'mailchimp';
		$this->meta_key   = '_action_mailchimp';
		$this->settings   = $this->settings();
	}

	/**
	 * Action settings
	 *
	 * @return array
	 */
	private function settings() {
		if ( empty( $this->api_key ) ) {
			$this->api_key = Utils::get_option( 'mailchimp_api_key' );
		}
		$meta = get_post_meta( get_the_ID(), '_action_mailchimp', true );

		$mailchimp_list   = array();
		$mailchimp_groups = array();

		try {
			$handler = new MailchimpHandler( $this->api_key );
			$lists   = $handler->getLists();
			$list    = $lists['lists'];
			if ( ! empty( $list ) ) {
				$mailchimp_list = $list;
			}

			if ( ! empty( $meta['list'] ) ) {
				$_groups          = $handler->getGroups( $meta['list'] );
				$groups           = $_groups['groups'];
				$mailchimp_groups = empty( $groups ) ? array() : $groups;
			}
		} catch ( \Exception $exception ) {
			// $exception->getMessage()
		}

		$config       = Config::init();
		$email_fields = array();
		$text_fields  = array(
			'' => __( '-- No Value --', 'dialog-contact-form' )
		);
		foreach ( $config->getFormFields() as $field ) {
			if ( empty( $field['field_type'] ) ) {
				continue;
			}
			if ( 'email' === $field['field_type'] ) {
				$email_fields[ $field['field_id'] ] = $field['field_title'];
			}
			if ( 'text' === $field['field_type'] ) {
				$text_fields[ $field['field_id'] ] = $field['field_title'];
			}
		}

		return array(
			'api_key_source' => array(
				'type'        => 'select',
				'id'          => 'api_key_source',
				'group'       => $this->meta_group,
				'meta_key'    => $this->meta_key,
				'label'       => __( 'API Key', 'dialog-contact-form' ),
				'description' => __( 'You are using MailChimp API Key set in the Integrations Settings. You can also set a different MailChimp API Key by choosing "Custom".',
					'dialog-contact-form' ),
				'default'     => 'default',
				'options'     => array(
					'default' => esc_html__( 'Default', 'dialog-contact-form' ),
					'custom'  => esc_html__( 'Custom', 'dialog-contact-form' ),
				),
			),
			'api_key'        => array(
				'id'          => 'api_key',
				'group'       => $this->meta_group,
				'meta_key'    => $this->meta_key,
				'group_class' => 'dcf-input-group col-mailchimp_api_key',
				'label'       => __( 'Custom API Key', 'dialog-contact-form' ),
				'description' => __( 'Use this field to set a custom API Key for the current form.',
					'dialog-contact-form' ),
				'sanitize'    => 'sanitize_text_field',
			),
			'list'           => array(
				'type'     => 'select',
				'id'       => 'list',
				'group'    => $this->meta_group,
				'meta_key' => $this->meta_key,
				'label'    => __( 'List', 'dialog-contact-form' ),
				'options'  => $mailchimp_list,
			),
			'groups'         => array(
				'type'     => 'select',
				'id'       => 'groups',
				'group'    => $this->meta_group,
				'meta_key' => $this->meta_key,
				'label'    => __( 'Groups', 'dialog-contact-form' ),
				'multiple' => true,
				'options'  => $mailchimp_groups,
			),
			'double_opt_in'  => array(
				'type'        => 'buttonset',
				'id'          => 'double_opt_in',
				'group'       => $this->meta_group,
				'meta_key'    => $this->meta_key,
				'label'       => __( 'Double Opt-In', 'dialog-contact-form' ),
				'description' => __( 'Set Double Opt-in to send a second verification email to visitor.',
					'dialog-contact-form' ),
				'default'     => 'off',
				'options'     => array(
					'on'  => __( 'Yes', 'dialog-contact-form' ),
					'off' => __( 'No', 'dialog-contact-form' ),
				),
			),
			'fields_map'     => array(
				'type'  => 'section',
				'label' => __( 'Field Mapping', 'dialog-contact-form' ),
			),
			'map_email'      => array(
				'type'     => 'select',
				'id'       => 'map_email',
				'group'    => $this->meta_group,
				'meta_key' => $this->meta_key,
				'label'    => __( 'Email Address', 'dialog-contact-form' ),
				'options'  => $email_fields,
			),
			'map_first_name' => array(
				'type'     => 'select',
				'id'       => 'map_first_name',
				'group'    => $this->meta_group,
				'meta_key' => $this->meta_key,
				'label'    => __( 'First Name', 'dialog-contact-form' ),
				'options'  => $text_fields,
			),
			'map_last_name'  => array(
				'type'     => 'select',
				'id'       => 'map_last_name',
				'group'    => $this->meta_group,
				'meta_key' => $this->meta_key,
				'label'    => __( 'Last Name', 'dialog-contact-form' ),
				'options'  => $text_fields,
			),
		);
	}

	/**
	 * Process current action
	 *
	 * @param \DialogContactForm\Supports\Config $config Contact form configurations
	 * @param array $data User submitted sanitized data
	 *
	 * @return boolean
	 */
	public static function process( $config, $data ) {
		$subscriber      = array();
		$action_settings = get_post_meta( $config->getFormId(), '_action_mailchimp', true );

		if ( empty( $action_settings['map_email'] ) ) {
			return false;
		}

		$subscriber['email_address'] = $data[ $action_settings['map_email'] ];

		if ( ! empty( $action_settings['map_first_name'] ) ) {
			$subscriber['merge_fields']['FNAME'] = $data[ $action_settings['map_first_name'] ];
		}

		if ( ! empty( $action_settings['map_last_name'] ) ) {
			$subscriber['merge_fields']['LNAME'] = $data[ $action_settings['map_last_name'] ];
		}

		if ( ! empty( $action_settings['groups'] ) ) {
			$subscriber['interests'] = array();

			foreach ( $action_settings['groups'] as $mailchimp_group ) {
				$subscriber['interests'][ $mailchimp_group ] = true;
			}
		}

		if ( 'default' === $action_settings['api_key_source'] ) {
			$api_key = Utils::get_option( 'mailchimp_api_key' );
		} else {
			$api_key = $action_settings['api_key'];
		}

		try {
			$handler = new MailchimpHandler( $api_key );

			$subscriber['status_if_new'] = 'yes' === $action_settings['double_opt_in'] ? 'pending' : 'subscribed';
			$subscriber['status']        = 'subscribed';

			$end_point = sprintf( 'lists/%s/members/%s', $action_settings['list'],
				md5( strtolower( $subscriber['email_address'] ) ) );

			$response = $handler->post( $end_point, $subscriber, array(
				'method' => 'PUT', // Add or Update
			) );

			if ( 200 !== $response['code'] ) {
				// Show server error message to admin user
				return false;
			}
		} catch ( \Exception $exception ) {
			// $exception->getMessage()
			return false;
		}

		return true;
	}
}
