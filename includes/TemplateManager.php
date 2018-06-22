<?php

namespace DialogContactForm;

use DialogContactForm\Abstracts\Abstract_Form_Template;
use DialogContactForm\Templates\Blank;
use DialogContactForm\Templates\CollectFeedback;
use DialogContactForm\Templates\ContactUs;
use DialogContactForm\Templates\DataErasureRequest;
use DialogContactForm\Templates\DataExportRequest;
use DialogContactForm\Templates\EventRegistration;
use DialogContactForm\Templates\GeneralEnquiry;
use DialogContactForm\Templates\JobApplication;
use Traversable;

class TemplateManager implements \IteratorAggregate, \JsonSerializable, \Countable, \ArrayAccess {

	/**
	 * @var object
	 */
	protected static $instance;

	/**
	 * @var array
	 */
	protected $collections = array();

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
		$this->add_template( 'collect_feedback', new CollectFeedback() );
		$this->add_template( 'general_enquiry', new GeneralEnquiry() );
		$this->add_template( 'data_erasure_request', new DataErasureRequest() );
		$this->add_template( 'data_export_request', new DataExportRequest() );
		$this->add_template( 'job_application', new JobApplication() );

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
	public function getCollections() {
		$actions = $this->collections;

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
			$this->collections[ $template_name ] = $template;
		}
	}

	/**
	 * Retrieve an external iterator
	 * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
	 * @return Traversable An instance of an object implementing Iterator or Traversable
	 * @since 5.0.0
	 */
	public function getIterator() {
		return new \ArrayIterator( $this->getCollections() );
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
		}, $this->getCollections() );
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
	 * Whether a offset exists
	 * @link http://php.net/manual/en/arrayaccess.offsetexists.php
	 *
	 * @param mixed $offset An offset to check for.
	 *
	 * @return boolean true on success or false on failure.
	 * @since 5.0.0
	 */
	public function offsetExists( $offset ) {
		return isset( $this->collections[ $offset ] );
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
		if ( $this->offsetExists( $offset ) ) {
			return $this->collections[ $offset ];
		}

		return null;
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
		$this->add_template( $offset, $value );
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
		if ( $this->offsetExists( $offset ) ) {
			unset( $this->collections[ $offset ] );
		}
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
