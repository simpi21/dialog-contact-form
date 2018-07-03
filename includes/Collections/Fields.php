<?php

namespace DialogContactForm\Collections;

use DialogContactForm\Abstracts\Field;
use DialogContactForm\Supports\Collection;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Fields extends Collection {

	/**
	 * @var object
	 */
	protected static $instance;

	/**
	 * @return Fields
	 */
	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * FieldManager constructor.
	 */
	public function __construct() {
		$this->set( 'acceptance', 'DialogContactForm\Fields\Acceptance' );
		$this->set( 'checkbox', 'DialogContactForm\Fields\Checkbox' );
		$this->set( 'date', 'DialogContactForm\Fields\Date' );
		$this->set( 'email', 'DialogContactForm\Fields\Email' );
		$this->set( 'file', 'DialogContactForm\Fields\File' );
		$this->set( 'hidden', 'DialogContactForm\Fields\Hidden' );
		$this->set( 'number', 'DialogContactForm\Fields\Number' );
		$this->set( 'password', 'DialogContactForm\Fields\Password' );
		$this->set( 'radio', 'DialogContactForm\Fields\Radio' );
		$this->set( 'select', 'DialogContactForm\Fields\Select' );
		$this->set( 'text', 'DialogContactForm\Fields\Text' );
		$this->set( 'textarea', 'DialogContactForm\Fields\Textarea' );
		$this->set( 'time', 'DialogContactForm\Fields\Time' );
		$this->set( 'url', 'DialogContactForm\Fields\Url' );
		$this->set( 'html', 'DialogContactForm\Fields\Html' );
		$this->set( 'divider', 'DialogContactForm\Fields\Divider' );

		/**
		 * Give other plugin option to add their own field(s)
		 */
		do_action( 'dialog_contact_form/fields', $this );
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
	 * Get fields by priority
	 *
	 * @return array
	 */
	public function getFieldsByPriority() {
		$tempCollections = $this->all();
		$fields          = array();
		foreach ( $tempCollections as $id => $className ) {
			$fields[ $id ] = new $className();
		}

		// Sort by priority
		usort( $fields, array( $this, 'sortByPriority' ) );

		return $fields;
	}

	/**
	 * Sort action by priority
	 *
	 * @param Field $fieldA
	 * @param Field $fieldB
	 *
	 * @return mixed
	 */
	private function sortByPriority( $fieldA, $fieldB ) {
		return $fieldA->getPriority() - $fieldB->getPriority();
	}
}
