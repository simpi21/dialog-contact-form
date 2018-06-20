<?php

namespace DialogContactForm;

use DialogContactForm\Abstracts\Abstract_Form_Template;
use DialogContactForm\Templates\Blank;
use DialogContactForm\Templates\ContactUs;
use DialogContactForm\Templates\EventRegistration;
use Traversable;

class TemplateManager implements \IteratorAggregate, \JsonSerializable, \Countable {

	/**
	 * @var object
	 */
	protected static $instance;

	/**
	 * @var array
	 */
	protected $templates = array();

	/**
	 * @return TemplateManager
	 */
	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function __construct() {
		$this->add_template( 'blank', new Blank() );
		$this->add_template( 'contact_us', new ContactUs() );
		$this->add_template( 'event_registration', new EventRegistration() );

		/**
		 * Give other plugin option to add their own template(s)
		 */
		do_action( 'dialog_contact_form/templates', $this );
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
	public function getTemplates() {
		$actions = $this->templates;

		// Sort by priority
		usort( $actions, array( $this, 'sortByPriority' ) );

		return $actions;
	}

	/**
	 * @param string $template_name
	 * @param  \DialogContactForm\Abstracts\Abstract_Form_Template $template
	 */
	public function add_template( $template_name, $template ) {
		if ( $template instanceof Abstract_Form_Template ) {
			$this->templates[ $template_name ] = $template;
		}
	}

	/**
	 * Retrieve an external iterator
	 * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
	 * @return Traversable An instance of an object implementing Iterator or Traversable
	 * @since 5.0.0
	 */
	public function getIterator() {
		return new \ArrayIterator( $this->getTemplates() );
	}

	/**
	 * Specify data which should be serialized to JSON
	 * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
	 * @return mixed data which can be serialized by json_encode,
	 * which is a value of any type other than a resource.
	 * @since 5.4.0
	 */
	public function jsonSerialize() {

		return array_map( function ( $template ) {
			if ( $template instanceof Abstract_Form_Template ) {
				return $template->toArray();
			}

			return $template;
		}, $this->getTemplates() );
	}

	/**
	 * Count elements of an object
	 * @link http://php.net/manual/en/countable.count.php
	 * @return int The custom count as an integer.
	 * @since 5.1.0
	 */
	public function count() {
		return count( $this->getTemplates() );
	}

	/**
	 * Sort action by priority
	 *
	 * @param \DialogContactForm\Abstracts\Abstract_Form_Template $templateA
	 * @param \DialogContactForm\Abstracts\Abstract_Form_Template $templateB
	 *
	 * @return mixed
	 */
	private function sortByPriority( $templateA, $templateB ) {
		return $templateA->get_priority() - $templateB->get_priority();
	}
}
