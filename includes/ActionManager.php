<?php

namespace DialogContactForm;

use DialogContactForm\Abstracts\Abstract_Action;
use DialogContactForm\Actions\Email;
use Traversable;

class ActionManager implements
	\IteratorAggregate,
	\JsonSerializable,
	\Countable {

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
		$this->add_action( 'email', new Email() );
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
		return $this->actions;
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
		return new \ArrayIterator( $this->actions );
	}

	/**
	 * Count elements of an object
	 * @link http://php.net/manual/en/countable.count.php
	 * @return int The custom count as an integer.
	 * @since 5.1.0
	 */
	public function count() {
		return count( $this->actions );
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
		}, $this->actions );
	}
}