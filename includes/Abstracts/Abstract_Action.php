<?php

namespace DialogContactForm\Abstracts;

abstract class Abstract_Action {

	/**
	 * Unique slug for an action
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * Title for an action
	 *
	 * @var string
	 */
	protected $title;

	/**
	 * @var string
	 */
	protected $section = 'installed';

	/**
	 * @var string
	 */
	protected $icon;

	/**
	 * @var string
	 */
	protected $timing = 'normal';

	/**
	 * @var int
	 */
	protected $priority = '10';

	/**
	 * @var array
	 */
	protected $settings = array();

	/**
	 * Abstract_Action constructor.
	 */
	public function __construct() {

	}

	/**
	 * Save action data
	 */
	protected function save( $action_settings ) {

	}

	/**
	 * Process action
	 */
	abstract public function process( $action_id, $form_id, $data );

	/**
	 * Get Timing
	 *
	 * Returns the timing for an action.
	 *
	 * @return mixed
	 */
	public function get_timing() {
		$timing = array( 'early' => - 1, 'normal' => 0, 'late' => 1 );

		return intval( $timing[ $this->timing ] );
	}

	/**
	 * Get Priority
	 *
	 * Returns the priority for an action.
	 *
	 * @return int
	 */
	protected function get_priority() {
		return intval( $this->priority );
	}

	/**
	 * Returns the id of an action.
	 *
	 * @return string
	 */
	protected function get_id() {
		return $this->id;
	}

	/**
	 * Returns the title of an action.
	 *
	 * @return string
	 */
	protected function get_title() {
		return $this->title;
	}

	/**
	 * Returns the drawer section for an action.
	 *
	 * @return string
	 */
	protected function get_section() {
		return $this->section;
	}

	/**
	 * Returns the url of a branded action's image.
	 *
	 * @return string
	 */
	protected function get_icon() {
		return $this->icon;
	}

	/**
	 * Returns the settings for an action.
	 *
	 * @return array
	 */
	protected function get_settings() {
		return $this->settings;
	}
}