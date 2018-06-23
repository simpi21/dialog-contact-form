<?php

namespace DialogContactForm;

use DialogContactForm\Abstracts\Action;
use DialogContactForm\Actions\DataErasureRequest;
use DialogContactForm\Actions\DataExportRequest;
use DialogContactForm\Actions\EmailNotification;
use DialogContactForm\Actions\MailChimp;
use DialogContactForm\Actions\Mailpoet;
use DialogContactForm\Actions\Mailpoet3;
use DialogContactForm\Actions\Redirect;
use DialogContactForm\Actions\StoreSubmission;
use DialogContactForm\Actions\SuccessMessage;
use DialogContactForm\Actions\Webhook;
use DialogContactForm\Supports\Collection;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ActionManager extends Collection {

	/**
	 * @var object
	 */
	protected static $instance;

	/**
	 * @return ActionManager
	 */
	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function __construct() {
		$this->add( 'store_submission', new StoreSubmission() );
		$this->add( 'email_notification', new EmailNotification() );
		$this->add( 'mailchimp', new MailChimp() );
		$this->add( 'mailpoet', new Mailpoet() );
		$this->add( 'mailpoet3', new Mailpoet3() );
		$this->add( 'webhook', new Webhook() );
		$this->add( 'data_export_request', new DataExportRequest() );
		$this->add( 'data_erasure_request', new DataErasureRequest() );
		$this->add( 'success_message', new SuccessMessage() );
		$this->add( 'redirect', new Redirect() );

		/**
		 * Give other plugin option to add their own action(s)
		 */
		do_action( 'dialog_contact_form/actions', $this );
	}

	/**
	 * @return array
	 */
	public function getCollections() {
		$actions = $this->collections;

		// Sort by priority
		usort( $actions, array( $this, 'sortByPriority' ) );

		return $actions;
	}

	/**
	 * Action to set
	 *
	 * @param string $action_name The action name to assign the value to.
	 * @param Action $action The action to set.
	 */
	public function add( $action_name, $action ) {
		if ( $action instanceof Action ) {
			$this->collections[ $action_name ] = $action;
		}
	}

	/**
	 * Get the array representation of the current element.
	 *
	 * @return array
	 */
	public function toArray() {
		return array_map( function ( $action ) {
			if ( $action instanceof Action ) {
				return $action->toArray();
			}

			return $action;
		}, $this->getCollections() );
	}

	/**
	 * Sort action by priority
	 *
	 * @param Action $actionA
	 * @param Action $actionB
	 *
	 * @return mixed
	 */
	private function sortByPriority( $actionA, $actionB ) {
		return $actionA->get_priority() - $actionB->get_priority();
	}
}
