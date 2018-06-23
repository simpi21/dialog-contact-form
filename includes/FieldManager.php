<?php

namespace DialogContactForm;

use DialogContactForm\Supports\Collection;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FieldManager extends Collection {

	/**
	 * @var object
	 */
	protected static $instance;

	/**
	 * @return FieldManager
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
		$this->add( 'acceptance', '\DialogContactForm\Fields\Acceptance' );
		$this->add( 'checkbox', '\DialogContactForm\Fields\Checkbox' );
		$this->add( 'date', '\DialogContactForm\Fields\Date' );
		$this->add( 'email', '\DialogContactForm\Fields\Email' );
		$this->add( 'file', '\DialogContactForm\Fields\File' );
		$this->add( 'hidden', '\DialogContactForm\Fields\Hidden' );
		$this->add( 'html', '\DialogContactForm\Fields\Html' );
		$this->add( 'ip', '\DialogContactForm\Fields\Ip' );
		$this->add( 'number', '\DialogContactForm\Fields\Number' );
		$this->add( 'password', '\DialogContactForm\Fields\Password' );
		$this->add( 'radio', '\DialogContactForm\Fields\Radio' );
		$this->add( 'recaptcha', '\DialogContactForm\Fields\Recaptcha2' );
		$this->add( 'select', '\DialogContactForm\Fields\Select' );
		$this->add( 'text', '\DialogContactForm\Fields\Text' );
		$this->add( 'textarea', '\DialogContactForm\Fields\Textarea' );
		$this->add( 'time', '\DialogContactForm\Fields\Time' );
		$this->add( 'url', '\DialogContactForm\Fields\Url' );

		/**
		 * Give other plugin option to add their own field(s)
		 */
		do_action( 'dialog_contact_form/fields', $this );
	}
}
