<?php

namespace DialogContactForm\Abstracts;

// Exit if accessed directly
use DialogContactForm\ActionManager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class Abstract_Form_Template {

	/**
	 * Form unique id
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * Form title
	 *
	 * @var string
	 */
	protected $title;

	/**
	 * Form description
	 *
	 * @var string
	 */
	protected $description;

	/**
	 * Form fields
	 *
	 * @return array
	 */
	abstract protected function form_fields();

	/**
	 * Form settings
	 *
	 * @return array
	 */
	abstract protected function form_settings();

	/**
	 * Form actions
	 *
	 * @return array
	 */
	abstract protected function form_actions();

	/**
	 * Form validation messages
	 *
	 * @return array
	 */
	abstract protected function form_validation_messages();

	/**
	 * Generate form
	 *
	 * @param int $post_id Form ID
	 */
	public function run( $post_id ) {
		$form_actions      = $this->form_actions();
		$form_actions_list = array_keys( $form_actions );

		update_post_meta( $post_id, '_contact_form_fields', $this->form_fields() );
		update_post_meta( $post_id, '_contact_form_messages', $this->form_validation_messages() );
		update_post_meta( $post_id, '_contact_form_config', $this->form_settings() );
		update_post_meta( $post_id, '_contact_form_actions', array(
			'after_submit_actions' => $form_actions_list,
		) );

		$actions = ActionManager::init();
		/** @var \DialogContactForm\Abstracts\Abstract_Action $action */
		foreach ( $actions as $action ) {
			if ( ! in_array( $action->get_id(), $form_actions_list ) ) {
				continue;
			}
			$action_value = $form_actions[ $action->get_id() ];
			if ( ! empty( $action_value ) ) {
				update_post_meta( $post_id, $action->get_meta_key(), $action_value );
			}
		}
	}
}
