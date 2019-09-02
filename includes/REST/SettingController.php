<?php

namespace DialogContactForm\REST;

use DialogContactForm\Admin\Settings;
use DialogContactForm\Supports\SettingHandler;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class SettingController extends ApiController {

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

			add_action( 'rest_api_init', array( self::$instance, 'register_routes' ) );
		}

		return self::$instance;
	}

	/**
	 * Registers the routes for the objects of the controller.
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/settings', [
			[
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => [ self::$instance, 'create_item' ],
				'args'     => [
					'options' => [
						'required'    => true,
						'default'     => [],
						'description' => __( 'Options to save.', 'dialog-contact-form' ),
					],
				],
			],
		] );
	}

	/**
	 * Retrieves a collection of items.
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_REST_Response Response object
	 */
	public function create_item( $request ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return $this->respondUnauthorized();
		}

		Settings::init();
		$settings = SettingHandler::init();

		$options       = $request->get_param( 'options' );
		$sanitize_data = $settings->sanitize_options( $options );
		$settings->update( $sanitize_data );

		return $this->respondOK();
	}
}
