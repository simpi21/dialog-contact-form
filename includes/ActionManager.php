<?php

namespace DialogContactForm;

use DialogContactForm\Abstracts\Abstract_Action;
use DialogContactForm\Actions\EmailNotification;
use DialogContactForm\Actions\MailChimp;
use DialogContactForm\Actions\Mailpoet;
use DialogContactForm\Actions\Mailpoet3;
use DialogContactForm\Actions\Redirect;
use DialogContactForm\Actions\StoreSubmission;
use DialogContactForm\Actions\SuccessMessage;
use DialogContactForm\Actions\Webhook;
use Traversable;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ActionManager implements \IteratorAggregate, \JsonSerializable, \Countable {

	/**
	 * @var object
	 */
	protected static $instance;

	/**
	 * @var array
	 */
	protected $actions = array();

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
		$this->add_action( 'success_message', new SuccessMessage() );
		$this->add_action( 'redirect', new Redirect() );

		/**
		 * Give other plugin option to add their own action(s)
		 */
		do_action( 'dialog_contact_form/actions', $this );
	}

	/**
	 * Get the string representation of the current element.
	 *
	 * @return string
	 */
	public function __toString() {
		return json_encode( $this->jsonSerialize() );
	}

	/**
	 * @return array
	 */
	public function getActions() {
		$actions = $this->actions;

		// Sort by priority
		usort( $actions, array( $this, 'sortByPriority' ) );

		return $actions;
	}

	/**
	 * @param string $action_name
	 * @param  \DialogContactForm\Abstracts\Abstract_Action $action
	 */
	public function add_action( $action_name, $action ) {
		if ( $action instanceof Abstract_Action ) {
			$this->actions[ $action_name ] = $action;
		}
	}

	/**
	 * Retrieve an external iterator
	 * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
	 * @return Traversable An instance of an object implementing Iterator or Traversable
	 * @since 5.0.0
	 */
	public function getIterator() {
		return new \ArrayIterator( $this->getActions() );
	}

	/**
	 * Count elements of an object
	 * @link http://php.net/manual/en/countable.count.php
	 * @return int The custom count as an integer.
	 * @since 5.1.0
	 */
	public function count() {
		return count( $this->getActions() );
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
			if ( $action instanceof Abstract_Action ) {
				return $action->toArray();
			}

			return $action;
		}, $this->getActions() );
	}

	/**
	 * Sort action by priority
	 *
	 * @param \DialogContactForm\Abstracts\Abstract_Action $actionA
	 * @param \DialogContactForm\Abstracts\Abstract_Action $actionB
	 *
	 * @return mixed
	 */
	private function sortByPriority( $actionA, $actionB ) {
		return $actionA->get_priority() - $actionB->get_priority();
	}
}