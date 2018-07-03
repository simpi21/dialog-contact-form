<?php

namespace DialogContactForm\Collections;

use DialogContactForm\Abstracts\Template;
use DialogContactForm\Supports\Collection;

class Templates extends Collection {

	/**
	 * @var object
	 */
	protected static $instance;

	/**
	 * @return Templates
	 */
	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function __construct() {
		$this->set( 'blank', 'DialogContactForm\Templates\Blank' );
		$this->set( 'contact_us', 'DialogContactForm\Templates\ContactUs' );
		$this->set( 'event_registration', 'DialogContactForm\Templates\EventRegistration' );
		$this->set( 'collect_feedback', 'DialogContactForm\Templates\CollectFeedback' );
		$this->set( 'general_enquiry', 'DialogContactForm\Templates\GeneralEnquiry' );
		$this->set( 'data_erasure_request', 'DialogContactForm\Templates\DataErasureRequest' );
		$this->set( 'data_export_request', 'DialogContactForm\Templates\DataExportRequest' );
		$this->set( 'job_application', 'DialogContactForm\Templates\JobApplication' );
		$this->set( 'quote_request', 'DialogContactForm\Templates\QuoteRequest' );

		/**
		 * Give other plugin option to add their own template(s)
		 */
		do_action( 'dialog_contact_form/templates', $this );
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
	 * Get templates by priority
	 *
	 * @return array
	 */
	public function getTemplatesByPriority() {
		$tempCollections = $this->all();
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
		return $templateA->getPriority() - $templateB->getPriority();
	}
}
