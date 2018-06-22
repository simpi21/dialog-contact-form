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
		$this->add( 'blank', new Blank() );
		$this->add( 'contact_us', new ContactUs() );
		$this->add( 'event_registration', new EventRegistration() );
		$this->add( 'collect_feedback', new CollectFeedback() );
		$this->add( 'general_enquiry', new GeneralEnquiry() );
		$this->add( 'data_erasure_request', new DataErasureRequest() );
		$this->add( 'data_export_request', new DataExportRequest() );
		$this->add( 'job_application', new JobApplication() );
		$this->add( 'quote_request', new QuoteRequest() );

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
	 * Template to set
	 *
	 * @param string $template_name The offset to assign the value to.
	 * @param Template $template The value to set.
	 */
	public function add( $template_name, $template ) {
		if ( $template instanceof Template ) {
			$this->collections[ $template_name ] = $template;
		}
	}

	/**
	 * Get the array representation of the current element.
	 *
	 * @return array
	 */
	public function toArray() {
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
	 * @param Template $templateA
	 * @param Template $templateB
	 *
	 * @return mixed
	 */
	private function sortByPriority( $templateA, $templateB ) {
		return $templateA->get_priority() - $templateB->get_priority();
	}
}
