<?php

namespace DialogContactForm;

use DialogContactForm\Abstracts\Template;
use DialogContactForm\Supports\Collection;

class TemplateManager extends Collection {

	/**
	 * @var object
	 */
	protected static $instance;

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
		$this->add( 'blank', 'DialogContactForm\Templates\Blank' );
		$this->add( 'contact_us', 'DialogContactForm\Templates\ContactUs' );
		$this->add( 'event_registration', 'DialogContactForm\Templates\EventRegistration' );
		$this->add( 'collect_feedback', 'DialogContactForm\Templates\CollectFeedback' );
		$this->add( 'general_enquiry', 'DialogContactForm\Templates\GeneralEnquiry' );
		$this->add( 'data_erasure_request', 'DialogContactForm\Templates\DataErasureRequest' );
		$this->add( 'data_export_request', 'DialogContactForm\Templates\DataExportRequest' );
		$this->add( 'job_application', 'DialogContactForm\Templates\JobApplication' );
		$this->add( 'quote_request', 'DialogContactForm\Templates\QuoteRequest' );

		/**
		 * Give other plugin option to add their own template(s)
		 */
		do_action( 'dialog_contact_form/templates', $this );
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

		return '\\' . ltrim( $this->collections[ $key ], '\\' );
	}

	/**
	 * Get templates by priority
	 *
	 * @return array
	 */
	public function getTemplatesByPriority() {
		$tempCollections = $this->getCollections();
		$templates       = array();
		foreach ( $tempCollections as $id => $className ) {
			$templates[ $id ] = new $className();
		}

		// Sort by priority
		usort( $templates, array( $this, 'sortByPriority' ) );

		return $templates;
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
