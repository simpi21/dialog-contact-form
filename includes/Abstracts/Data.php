<?php

namespace DialogContactForm\Abstracts;

use ArrayAccess;
use JsonSerializable;

defined( 'ABSPATH' ) || exit;

class Data implements ArrayAccess, JsonSerializable {

	/**
	 * Class data
	 *
	 * @var array
	 */
	protected $data = [];

	/**
	 * Get data array
	 *
	 * @return array
	 */
	public function toArray() {
		return $this->data;
	}

	/**
	 * Does this data have a given key?
	 *
	 * @param string $key The data key
	 *
	 * @return bool
	 */
	public function has( $key ) {
		return isset( $this->data[ $key ] );
	}

	/**
	 * Get data item for a key
	 *
	 * @param string $key The data key
	 * @param mixed $default The default value to return if data key does not exist
	 *
	 * @return mixed The key's value, or the default value
	 */
	public function get( $key, $default = null ) {
		return $this->has( $key ) ? $this->data[ $key ] : $default;
	}

	/**
	 * Set data item
	 *
	 * @param string $key The data key
	 * @param mixed $value The data value
	 */
	public function set( $key, $value ) {
		$this->data[ $key ] = $value;
	}

	/**
	 * Remove item from data
	 *
	 * @param string $key The data key
	 */
	public function remove( $key ) {
		if ( $this->has( $key ) ) {
			unset( $this->data[ $key ] );
		}
	}

	/**
	 * Whether a offset exists
	 * @link https://php.net/manual/en/arrayaccess.offsetexists.php
	 *
	 * @param mixed $offset An offset to check for.
	 *
	 * @return boolean true on success or false on failure.
	 */
	public function offsetExists( $offset ) {
		return $this->has( $offset );
	}

	/**
	 * Offset to retrieve
	 * @link https://php.net/manual/en/arrayaccess.offsetget.php
	 *
	 * @param mixed $offset The offset to retrieve.
	 *
	 * @return mixed Can return all value types.
	 */
	public function offsetGet( $offset ) {
		return $this->get( $offset );
	}

	/**
	 * Offset to set
	 * @link https://php.net/manual/en/arrayaccess.offsetset.php
	 *
	 * @param mixed $offset The offset to assign the value to.
	 * @param mixed $value The value to set.
	 *
	 * @return void
	 */
	public function offsetSet( $offset, $value ) {
		$this->set( $offset, $value );
	}

	/**
	 * Offset to unset
	 * @link https://php.net/manual/en/arrayaccess.offsetunset.php
	 *
	 * @param mixed $offset The offset to unset.
	 *
	 * @return void
	 */
	public function offsetUnset( $offset ) {
		$this->remove( $offset );
	}

	/**
	 * Specify data which should be serialized to JSON
	 * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
	 *
	 * @return mixed data which can be serialized by <b>json_encode</b>,
	 * which is a value of any type other than a resource.
	 */
	public function jsonSerialize() {
		return $this->toArray();
	}
}
