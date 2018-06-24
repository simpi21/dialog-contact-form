<?php

namespace DialogContactForm;

use DialogContactForm\Abstracts\Action;
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
		$this->add( 'store_submission', 'DialogContactForm\Actions\StoreSubmission' );
		$this->add( 'email_notification', 'DialogContactForm\Actions\EmailNotification' );
		$this->add( 'mailchimp', 'DialogContactForm\Actions\MailChimp' );
		$this->add( 'mailpoet', 'DialogContactForm\Actions\Mailpoet' );
		$this->add( 'mailpoet3', 'DialogContactForm\Actions\Mailpoet3' );
		$this->add( 'webhook', 'DialogContactForm\Actions\Webhook' );
		$this->add( 'data_export_request', 'DialogContactForm\Actions\DataExportRequest' );
		$this->add( 'data_erasure_request', 'DialogContactForm\Actions\DataErasureRequest' );
		$this->add( 'success_message', 'DialogContactForm\Actions\SuccessMessage' );
		$this->add( 'redirect', 'DialogContactForm\Actions\Redirect' );

		/**
		 * Give other plugin option to add their own action(s)
		 */
		do_action( 'dialog_contact_form/actions', $this );
	}

	/**
	 * Offset to retrieve
	 *
	 * @param mixed $key The offset to retrieve.
	 *
	 * @return mixed Can return all value types.
	 */
	public function get( $key ) {
		if ( ! $this->has( $key ) ) {
			return null;
		}

		return '\\' . ltrim( $this->collections[ $key ], '\\' );
	}

	/**
	 * Get actions by priority
	 *
	 * @return array
	 */
	public function getActionsByPriority() {
		$tempCollections = $this->getCollections();
		$actions         = array();
		foreach ( $tempCollections as $id => $className ) {
			$actions[ $id ] = new $className();
		}

		// Sort by priority
		usort( $actions, array( $this, 'sortByPriority' ) );

		return $actions;
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
