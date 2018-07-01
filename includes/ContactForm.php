<?php

namespace DialogContactForm;

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
}
