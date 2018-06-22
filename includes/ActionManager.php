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
	 * @var array
	 */
	protected $collections = array();

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
		$this->add_action( 'store_submission', new StoreSubmission() );
		$this->add_action( 'email_notification', new EmailNotification() );
		$this->add_action( 'mailchimp', new MailChimp() );
		$this->add_action( 'mailpoet', new Mailpoet() );
		$this->add_action( 'mailpoet3', new Mailpoet3() );
		$this->add_action( 'webhook', new Webhook() );
		$this->add_action( 'data_export_request', new DataExportRequest() );
		$this->add_action( 'data_erasure_request', new DataErasureRequest() );
		$this->add_action( 'success_message', new SuccessMessage() );
		$this->add_action( 'redirect', new Redirect() );

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
	 * @param string $action_name
	 * @param  \DialogContactForm\Abstracts\Action $action
	 */
	public function add_action( $action_name, $action ) {
		if ( $action instanceof Action ) {
			$this->collections[ $action_name ] = $action;
		}
	}

	/**
	 * Offset to set
	 * @link http://php.net/manual/en/arrayaccess.offsetset.php
	 *
	 * @param mixed $offset The offset to assign the value to.
	 * @param mixed $value The value to set.
	 */
	public function add( $offset, $value ) {
		$this->add_action( $offset, $value );
	}

	/**
	 * Specify data which should be serialized to JSON
	 * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
	 * @return mixed data which can be serialized by json_encode,
	 * which is a value of any type other than a resource.
	 * @since 5.4.0
	 */
	public function jsonSerialize() {
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
	 * @param \DialogContactForm\Abstracts\Action $actionA
	 * @param \DialogContactForm\Abstracts\Action $actionB
	 *
	 * @return mixed
	 */
	private function sortByPriority( $actionA, $actionB ) {
		return $actionA->get_priority() - $actionB->get_priority();
	}
}
