<?php

namespace DialogContactForm\REST;

use DialogContactForm\Supports\SettingHandler;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class SettingController extends Controller {


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
		register_rest_route( self::$namespace, '/settings', array(
			array(
				'methods'  => \WP_REST_Server::READABLE,
				'callback' => array( self::$instance, 'get_items' ),
				'args'     => array(),
			),
		) );
	}

	/**
	 * Retrieves a collection of items.
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return \WP_REST_Response Response object
	 */
	public function get_items( $request ) {
		$settings = SettingHandler::init();

		$response = array(
			'panels'   => $settings->getPanels(),
			'sections' => $settings->getSections(),
			'fields'   => $settings->getFields(),
			'options'  => $settings->get_options(),
		);

		return $this->respondOK( $response );
	}
}
