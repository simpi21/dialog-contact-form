<?php

namespace DialogContactForm;

use WP_REST_Server;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RestApi {

	/**
	 * REST API version
	 *
	 * @var string
	 */
	protected static $version = 'v1';

	/**
	 * REST API namespace
	 *
	 * @var string
	 */
	protected static $namespace = 'dialog-contact-form';

	/**
	 * The instance of the class
	 *
	 * @var self
	 */
	protected static $instance;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return self
	 */
	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Plugin constructor
	 */
	public function __construct() {
		self::$version   = '1';
		self::$namespace = 'dialog-contact-form/v' . self::$version;

		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
	}

	public function rest_api_init() {
		register_rest_route( self::$namespace, '/entries', array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_entries' ),
				'permission_callback' => array( $this, 'can_read_entries' ),
				'args'                => array(
					'page'     => array(
						'description'       => 'Current page of the collection.',
						'type'              => 'integer',
						'default'           => 1,
						'sanitize_callback' => 'absint',
					),
					'per_page' => array(
						'required'    => false,
						'default'     => 50,
						'description' => esc_html__( 'Maximum number of items to be returned in result set.',
							'dialog-contact-form' ),
						'type'        => 'integer',
					),
					'order'    => array(
						'required' => false,
						'default'  => 'DESC',
						'enum'     => array( 'ASC', 'DESC' ),
					),
					'order_by' => array(
						'required' => false,
						'default'  => 'id',
						'enum'     => array( 'id', 'form_id', 'user_id', 'referer', 'status', 'created_at' ),
					),
					'status'   => array(
						'required' => false,
						'default'  => 'all',
						'enum'     => array( 'all', 'unread', 'read', 'trash' ),
					),
				),
			)
		) );
	}

	/**
	 * Get a collection of entries
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return array
	 */
	public function get_entries( $request ) {
		return array(
			array( 'id' => 1 )
		);
	}

	/**
	 * Check current user can read entries
	 *
	 * @return bool
	 */
	public function can_read_entries() {
		return true;
	}
}