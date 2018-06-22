<?php

namespace DialogContactForm\Supports;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Collection implements ArrayAccess, Countable, IteratorAggregate, JsonSerializable {

	/**
	 * @var array
	 */
	protected $collections = array();

	/**
	 * Get collections
	 *
	 * @return array
	 */
	public function getCollections() {
		return $this->collections;
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
	 * Whether an offset exists
	 *
	 * @param mixed $key An offset to check for.
	 *
	 * @return bool
	 */
	public function has( $key ) {
		return isset( $this->collections[ $key ] );
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

		return $this->collections[ $key ];
	}

	/**
	 * Offset to set
	 *
	 * @param mixed $key The offset to assign the value to.
	 * @param mixed $value The value to set.
	 *
	 * @return void
	 */
	public function add( $key, $value ) {
		if ( is_null( $key ) ) {
			$this->collections[] = $value;
		} else {
			$this->collections[ $key ] = $value;
		}
	}

	/**
	 * Offset to unset
	 *
	 * @param mixed $key The offset to unset.
	 *
	 * @return void
	 */
	public function delete( $key ) {
		if ( $this->has( $key ) ) {
			unset( $this->collections[ $key ] );
		}
	}

	/**
	 * Whether a offset exists
	 * @link http://php.net/manual/en/arrayaccess.offsetexists.php
	 *
	 * @param mixed $offset An offset to check for.
	 *
	 * @return boolean true on success or false on failure.
	 * @since 5.0.0
	 */
	public function offsetExists( $offset ) {
		return $this->has( $offset );
	}

	/**
	 * Offset to retrieve
	 * @link http://php.net/manual/en/arrayaccess.offsetget.php
	 *
	 * @param mixed $offset The offset to retrieve.
	 *
	 * @return mixed Can return all value types.
	 * @since 5.0.0
	 */
	public function offsetGet( $offset ) {
		return $this->get( $offset );
	}

	/**
	 * Offset to set
	 * @link http://php.net/manual/en/arrayaccess.offsetset.php
	 *
	 * @param mixed $offset The offset to assign the value to.
	 * @param mixed $value The value to set.
	 *
	 * @return void
	 * @since 5.0.0
	 */
	public function offsetSet( $offset, $value ) {
		$this->add( $offset, $value );
	}

	/**
	 * Offset to unset
	 * @link http://php.net/manual/en/arrayaccess.offsetunset.php
	 *
	 * @param mixed $offset The offset to unset.
	 *
	 * @return void
	 * @since 5.0.0
	 */
	public function offsetUnset( $offset ) {
		$this->delete( $offset );
	}

	/**
	 * Count elements of an object
	 * @link http://php.net/manual/en/countable.count.php
	 * @return int The custom count as an integer.
	 * @since 5.1.0
	 */
	public function count() {
		return count( $this->getCollections() );
	}

	/**
	 * Retrieve an external iterator
	 * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
	 * @return Traversable An instance of an object implementing Iterator or Traversable
	 * @since 5.0.0
	 */
	public function getIterator() {
		return new ArrayIterator( $this->getCollections() );
	}

	/**
	 * Specify data which should be serialized to JSON
	 * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
	 * @return mixed data which can be serialized by json_encode,
	 * which is a value of any type other than a resource.
	 * @since 5.4.0
	 */
	public function jsonSerialize() {
		return $this->getCollections();
	}
}
