<?php

namespace DialogContactForm\Collections;

use DialogContactForm\Abstracts\Field;
use DialogContactForm\Fields\Acceptance;
use DialogContactForm\Fields\Checkbox;
use DialogContactForm\Fields\Date;
use DialogContactForm\Fields\Divider;
use DialogContactForm\Fields\Email;
use DialogContactForm\Fields\File;
use DialogContactForm\Fields\Hidden;
use DialogContactForm\Fields\Html;
use DialogContactForm\Fields\Number;
use DialogContactForm\Fields\Password;
use DialogContactForm\Fields\Radio;
use DialogContactForm\Fields\Select;
use DialogContactForm\Fields\Text;
use DialogContactForm\Fields\Textarea;
use DialogContactForm\Fields\Time;
use DialogContactForm\Fields\Url;
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
		$this->set( 'acceptance', Acceptance::class );
		$this->set( 'checkbox', Checkbox::class );
		$this->set( 'date', Date::class );
		$this->set( 'email', Email::class );
		$this->set( 'file', File::class );
		$this->set( 'hidden', Hidden::class );
		$this->set( 'number', Number::class );
		$this->set( 'password', Password::class );
		$this->set( 'radio', Radio::class );
		$this->set( 'select', Select::class );
		$this->set( 'text', Text::class );
		$this->set( 'textarea', Textarea::class );
		$this->set( 'time', Time::class );
		$this->set( 'url', Url::class );
		$this->set( 'html', Html::class );
		$this->set( 'divider', Divider::class );

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
	 * @return array|Field[]
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
