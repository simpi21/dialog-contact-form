<?php

namespace DialogContactForm\Abstracts;

use DialogContactForm\Supports\Metabox;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class Action {

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
	 * Icon of the action
	 *
	 * @var string
	 */
	protected $icon;

	/**
	 * Timing of the action
	 *
	 * @var string
	 */
	protected $timing = 'normal';

	/**
	 * Priority of the action
	 *
	 * @var int
	 */
	protected $priority = 100;

	/**
	 * @var array
	 */
	protected $settings = array();

	/**
	 * Meta key name for holding current action settings
	 *
	 * @var string
	 */
	protected $meta_key;

	/**
	 * Meta group for sending metadata to save as action settings
	 *
	 * @var string
	 */
	protected $meta_group;

	/**
	 * Save action settings
	 *
	 * @param int $post_id
	 * @param \WP_Post $post
	 */
	public function save( $post_id, $post ) {
		if ( ! empty( $_POST[ $this->meta_group ] ) ) {
			$sanitize_data = $this->sanitize_settings( $_POST[ $this->meta_group ] );

			update_post_meta( $post_id, $this->meta_key, $sanitize_data );
		} else {
			delete_post_meta( $post_id, $this->meta_key );
		}
	}

	/**
	 * Process current action
	 *
	 * @param \DialogContactForm\Supports\Config $config Contact form configurations
	 * @param array $data User submitted sanitized data
	 *
	 * @return mixed
	 */
	public static function process( $config, $data ) {
		// TODO: Implement process() method.
	}

	/**
	 * Get settings as array
	 *
	 * @return array
	 */
	public function toArray() {
		return array(
			'id'       => $this->get_id(),
			'title'    => $this->get_title(),
			'settings' => $this->get_settings()
		);
	}

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
	public function get_priority() {
		return intval( $this->priority );
	}

	/**
	 * Returns the id of an action.
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Returns the title of an action.
	 *
	 * @return string
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * Returns the drawer section for an action.
	 *
	 * @return string
	 */
	public function get_section() {
		return $this->section;
	}

	/**
	 * Returns the url of a branded action's image.
	 *
	 * @return string
	 */
	public function get_icon() {
		return $this->icon;
	}

	/**
	 * Returns the settings for an action.
	 *
	 * @return array
	 */
	public function get_settings() {
		return $this->settings;
	}

	/**
	 * Get action description
	 *
	 * @return string
	 */
	public function get_description() {
		return '';
	}

	/**
	 * Get action meta key
	 *
	 * @return string
	 */
	public function get_meta_key() {
		return $this->meta_key;
	}

	/**
	 * Build metabox fields
	 */
	public function build_fields() {
		foreach ( $this->settings as $setting ) {
			$input_type = isset( $setting['type'] ) ? esc_attr( $setting['type'] ) : 'text';

			if ( method_exists( '\\DialogContactForm\\Supports\\Metabox', $input_type ) ) {
				Metabox::$input_type( $setting );
			}
		}
	}

	/**
	 * Sanitize action settings
	 *
	 * @param $data
	 *
	 * @return array
	 */
	protected function sanitize_settings( $data ) {
		$sanitize_data = array();
		foreach ( $this->get_settings() as $setting ) {
			$value = isset( $data[ $setting['id'] ] ) ? $data[ $setting['id'] ] : null;

			if ( is_array( $value ) ) {
				$sanitize_data[ $setting['id'] ] = self::sanitize_array( $value );
				continue;
			}

			if ( 'select' == $setting['type'] ) {
				$valid_options = array_keys( $setting['options'] );
				$value         = in_array( $value, $valid_options ) ? $value : $setting['default'];

				$sanitize_data[ $setting['id'] ] = $value;
				continue;
			}

			$sanitize_method = 'sanitize_text_field';
			if ( isset( $setting['sanitize'] ) && is_callable( $setting['sanitize'] ) ) {
				$sanitize_method = $setting['sanitize'];
			}
			$sanitize_data[ $setting['id'] ] = call_user_func( $sanitize_method, $value );
		}

		return $sanitize_data;
	}

	/**
	 * Sanitize meta value
	 *
	 * @param $input
	 *
	 * @return array|string
	 */
	protected static function sanitize_array( $input ) {

		if ( is_array( $input ) ) {
			// Initialize the new array that will hold the sanitize values
			$new_input = array();

			// Loop through the input and sanitize each of the values
			foreach ( $input as $key => $value ) {
				if ( is_array( $value ) ) {
					$new_input[ $key ] = self::sanitize_array( $value );
				} else {
					$new_input[ $key ] = sanitize_text_field( $value );
				}
			}

			return $new_input;
		}

		return sanitize_text_field( $input );
	}
}
