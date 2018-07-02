<?php

namespace DialogContactForm\Abstracts;

use DialogContactForm\ActionManager;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class Template {

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
	 * Priority of the template
	 *
	 * @var int
	 */
	protected $priority = 100;

	/**
	 * Form fields
	 *
	 * @return array
	 */
	abstract protected function formFields();

	/**
	 * Form settings
	 *
	 * @return array
	 */
	abstract protected function formSettings();

	/**
	 * Form actions
	 *
	 * @return array
	 */
	abstract protected function formActions();

	/**
	 * Form validation messages
	 *
	 * @return array
	 */
	abstract protected function formValidationMessages();

	/**
	 * Generate form
	 *
	 * @param int $post_id Form ID
	 */
	public function run( $post_id ) {
		$form_actions      = $this->formActions();
		$form_actions_list = array_keys( $form_actions );

		update_post_meta( $post_id, '_contact_form_fields', $this->formFields() );
		update_post_meta( $post_id, '_contact_form_messages', $this->formValidationMessages() );
		update_post_meta( $post_id, '_contact_form_config', $this->formSettings() );
		update_post_meta( $post_id, '_contact_form_actions', $form_actions_list );

		$actions = ActionManager::init();
		foreach ( $actions as $action_id => $className ) {
			if ( ! in_array( $action_id, $form_actions_list ) ) {
				continue;
			}
			$action = new $className;
			if ( ! $action instanceof Action ) {
				continue;
			}

			$action_value = $form_actions[ $action->getId() ];
			if ( ! empty( $action_value ) ) {
				update_post_meta( $post_id, $action->getMetaKey(), $action_value );
			}
		}
	}

	/**
	 * Get Priority
	 *
	 * Returns the priority for an action.
	 *
	 * @return int
	 */
	public function getPriority() {
		return intval( $this->priority );
	}

	/**
	 * Get settings as array
	 *
	 * @return array
	 */
	public function toArray() {
		return array(
			'id'          => $this->getId(),
			'title'       => $this->getTitle(),
			'description' => $this->getDescription(),
		);
	}

	/**
	 * Returns the id of an template.
	 *
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Returns the title of an template.
	 *
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Returns the description of an template.
	 *
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}
}
