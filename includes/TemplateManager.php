<?php

namespace DialogContactForm;

use DialogContactForm\Abstracts\Template;
use DialogContactForm\Supports\Collection;
use DialogContactForm\Templates\Blank;
use DialogContactForm\Templates\CollectFeedback;
use DialogContactForm\Templates\ContactUs;
use DialogContactForm\Templates\DataErasureRequest;
use DialogContactForm\Templates\DataExportRequest;
use DialogContactForm\Templates\EventRegistration;
use DialogContactForm\Templates\GeneralEnquiry;
use DialogContactForm\Templates\JobApplication;
use DialogContactForm\Templates\QuoteRequest;

class TemplateManager extends Collection {

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
		$this->add_template( 'quote_request', new QuoteRequest() );

		/**
		 * Give other plugin option to add their own template(s)
		 */
		do_action( 'dialog_contact_form/templates', $this );
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
	 * @param  \DialogContactForm\Abstracts\Template $template
	 */
	public function add_template( $template_name, $template ) {
		if ( $template instanceof Template ) {
			$this->collections[ $template_name ] = $template;
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
		$this->add_template( $offset, $value );
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
			if ( $template instanceof Template ) {
				return $template->toArray();
			}

			return $template;
		}, $this->getCollections() );
	}

	/**
	 * Sort action by priority
	 *
	 * @param \DialogContactForm\Abstracts\Template $templateA
	 * @param \DialogContactForm\Abstracts\Template $templateB
	 *
	 * @return mixed
	 */
	private function sortByPriority( $templateA, $templateB ) {
		return $templateA->get_priority() - $templateB->get_priority();
	}
}
