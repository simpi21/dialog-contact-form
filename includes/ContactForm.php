<?php

namespace DialogContactForm;

use DialogContactForm\Abstracts\Action;
use DialogContactForm\Collections\Actions;
use DialogContactForm\Supports\Config;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ContactForm {

	const POST_TYPE = 'dialog-contact-form';

	/**
	 * @var int
	 */
	private static $found_items = 0;

	/**
	 * @var int
	 */
	private $id;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $title;

	/**
	 * @var Config
	 */
	private $settings;

	/**
	 * ContactForm constructor.
	 *
	 * @param int|\WP_Post|null $post Optional. Post ID or post object.
	 */
	public function __construct( $post = null ) {
		$post = get_post( $post );

		if ( $post && self::POST_TYPE == get_post_type( $post ) ) {
			$this->id       = $post->ID;
			$this->name     = $post->post_name;
			$this->title    = $post->post_title;
			$this->settings = Config::init( $post->ID );
		}
	}

	/**
	 * @param $data
	 *
	 * @return array
	 */
	private static function sanitize_field( $data ) {
		$_data = array(
			'field_title'        => isset( $data['field_title'] ) ? sanitize_text_field( $data['field_title'] ) : '',
			'field_name'         => isset( $data['field_id'] ) ? sanitize_text_field( $data['field_id'] ) : '',
			'field_id'           => isset( $data['field_id'] ) ? sanitize_text_field( $data['field_id'] ) : '',
			'field_type'         => isset( $data['field_type'] ) ? sanitize_text_field( $data['field_type'] ) : '',
			'options'            => isset( $data['options'] ) ? wp_strip_all_tags( $data['options'] ) : '',
			'number_min'         => isset( $data['number_min'] ) ? sanitize_text_field( $data['number_min'] ) : '',
			'number_max'         => isset( $data['number_max'] ) ? sanitize_text_field( $data['number_max'] ) : '',
			'number_step'        => isset( $data['number_step'] ) ? sanitize_text_field( $data['number_step'] ) : '',
			'field_value'        => isset( $data['field_value'] ) ? sanitize_text_field( $data['field_value'] ) : '',
			'required_field'     => isset( $data['required_field'] ) ? sanitize_text_field( $data['required_field'] ) : '',
			'field_class'        => isset( $data['field_class'] ) ? sanitize_text_field( $data['field_class'] ) : '',
			'field_width'        => isset( $data['field_width'] ) ? sanitize_text_field( $data['field_width'] ) : '',
			'placeholder'        => isset( $data['placeholder'] ) ? sanitize_text_field( $data['placeholder'] ) : '',
			'autocomplete'       => isset( $data['autocomplete'] ) ? sanitize_text_field( $data['autocomplete'] ) : '',
			// Acceptance Field
			'acceptance_text'    => isset( $data['acceptance_text'] ) ? wp_kses_post( $data['acceptance_text'] ) : '',
			'checked_by_default' => isset( $data['checked_by_default'] ) ? sanitize_text_field( $data['checked_by_default'] ) : '',
			// Date Field
			'min_date'           => isset( $data['min_date'] ) ? sanitize_text_field( $data['min_date'] ) : '',
			'max_date'           => isset( $data['max_date'] ) ? sanitize_text_field( $data['max_date'] ) : '',
			// Date & Time Field
			'native_html5'       => isset( $data['native_html5'] ) ? sanitize_text_field( $data['native_html5'] ) : '',
			// File Field
			'max_file_size'      => isset( $data['max_file_size'] ) ? absint( $data['max_file_size'] ) : '',
			'allowed_file_types' => isset( $data['allowed_file_types'] ) ? self::sanitize_value( $data['allowed_file_types'] ) : array(),
			'rows'               => isset( $data['rows'] ) ? intval( $data['rows'] ) : '',
			// File & Email
			'multiple'           => isset( $data['multiple'] ) ? sanitize_text_field( $data['multiple'] ) : '',
			// HTML
			'html'               => isset( $data['html'] ) ? wp_kses_post( $data['html'] ) : '',
			// Depreciated
			'validation'         => isset( $data['validation'] ) ? self::sanitize_value( $data['validation'] ) : array(),
			'error_message'      => isset( $data['error_message'] ) ? sanitize_text_field( $data['error_message'] ) : '',
		);

		return array_filter( $_data );
	}

	/**
	 * @param array $args
	 *
	 * @return array
	 */
	public static function find( $args = array() ) {
		$defaults = array(
			'post_status'    => 'any',
			'posts_per_page' => - 1,
			'offset'         => 0,
			'orderby'        => 'ID',
			'order'          => 'ASC',
		);

		$args = wp_parse_args( $args, $defaults );

		$args['post_type'] = self::POST_TYPE;

		$q     = new \WP_Query();
		$posts = $q->query( $args );

		self::$found_items = $q->found_posts;

		$objs = array();

		foreach ( (array) $posts as $post ) {
			$objs[] = new self( $post );
		}

		return $objs;
	}

	/**
	 * Update current form
	 *
	 * @param array $data
	 */
	public function update( array $data ) {

		// Update form settings
		if ( isset( $data['config'] ) && is_array( $data['config'] ) ) {
			update_post_meta( $this->id(), '_contact_form_config', self::sanitize_value( $data['config'] ) );
		}

		// Update form validation messages
		if ( isset( $data['messages'] ) && is_array( $data['messages'] ) ) {
			update_post_meta( $this->id(), '_contact_form_messages', self::sanitize_value( $data['messages'] ) );
		}

		// Update form fields settings
		if ( isset( $data['field'] ) && is_array( $data['field'] ) ) {
			$_data = array();
			foreach ( $data['field'] as $field ) {
				$_data[] = self::sanitize_field( $field );
			}

			update_post_meta( $this->id(), '_contact_form_fields', $_data );
		} else {
			delete_post_meta( $this->id(), '_contact_form_fields' );
		}

		// Update form actions
		if ( isset( $data['actions'] ) && is_array( $data['actions'] ) ) {

			$actions       = array();
			$actionManager = Actions::init();

			foreach ( $data['actions'] as $action_id => $action_data ) {
				$className = $actionManager->get( $action_id );
				$action    = new $className;
				if ( ! $action instanceof Action ) {
					continue;
				}

				$actions[] = $action_id;

				$action->save( $this->id(), $action_data );
			}

			update_post_meta( $this->id(), '_contact_form_actions', self::sanitize_value( $actions ) );
		}
	}

	/**
	 * Delete current form
	 *
	 * @return bool
	 */
	public function delete() {
		if ( wp_delete_post( $this->id, true ) ) {
			$this->id = 0;

			return true;
		}

		return false;
	}

	/**
	 * @return int
	 */
	public static function count() {
		return self::$found_items;
	}

	/**
	 * @return int
	 */
	public function id() {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function name() {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function title() {
		return $this->title;
	}

	/**
	 * @return Config
	 */
	public function settings() {
		return $this->settings;
	}

	/**
	 * Sanitize meta value
	 *
	 * @param $input
	 *
	 * @return array|string
	 */
	private static function sanitize_value( $input ) {
		// Initialize the new array that will hold the sanitize values
		$new_input = array();

		if ( is_array( $input ) ) {
			// Loop through the input and sanitize each of the values
			foreach ( $input as $key => $value ) {
				if ( is_array( $value ) ) {
					$new_input[ $key ] = self::sanitize_value( $value );
				} else {
					$new_input[ $key ] = sanitize_text_field( $value );
				}
			}
		} else {
			return sanitize_text_field( $input );
		}

		return $new_input;
	}
}
