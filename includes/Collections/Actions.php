<?php

namespace DialogContactForm\Collections;

use DialogContactForm\Abstracts\Action;
use DialogContactForm\Supports\Collection;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Actions extends Collection {

	/**
	 * @var object
	 */
	protected static $instance;

	/**
	 * @return Actions
	 */
	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function __construct() {
		$this->set( 'store_submission', 'DialogContactForm\Actions\StoreSubmission' );
		$this->set( 'email_notification', 'DialogContactForm\Actions\EmailNotification' );
		$this->set( 'mailchimp', 'DialogContactForm\Actions\MailChimp' );
		$this->set( 'mailpoet', 'DialogContactForm\Actions\Mailpoet' );
		$this->set( 'mailpoet3', 'DialogContactForm\Actions\Mailpoet3' );
		$this->set( 'webhook', 'DialogContactForm\Actions\Webhook' );
		$this->set( 'data_export_request', 'DialogContactForm\Actions\DataExportRequest' );
		$this->set( 'data_erasure_request', 'DialogContactForm\Actions\DataErasureRequest' );
		$this->set( 'success_message', 'DialogContactForm\Actions\SuccessMessage' );
		$this->set( 'redirect', 'DialogContactForm\Actions\Redirect' );

		/**
		 * Give other plugin option to add their own action(s)
		 */
		do_action( 'dialog_contact_form/actions', $this );
	}

	/**
	 * Get collection item for key
	 *
	 * @param string $key The data key
	 * @param mixed $default The default value to return if data key does not exist
	 *
	 * @return mixed The key's value, or the default value
	 */
	public function get( $key, $default = null ) {
		if ( ! $this->has( $key ) ) {
			return $default;
		}

		return '\\' . ltrim( $this->collections[ $key ], '\\' );
	}

	/**
	 * Get actions by priority
	 *
	 * @return array
	 */
	public function getActionsByPriority() {
		$tempCollections = $this->all();
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
		return $actionA->getPriority() - $actionB->getPriority();
	}
}
