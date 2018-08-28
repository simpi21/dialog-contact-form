<?php

namespace DialogContactForm\REST;

use DialogContactForm\Entries\Entry;
use WP_Error;
use WP_REST_Request;
use WP_REST_Server;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EntryController extends Controller {

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
	public static function register_routes() {
		register_rest_route( self::$namespace, '/entries', array(
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( self::$instance, 'get_form_entries' ),
				'args'     => array(
					'per_page' => array(
						'required'    => false,
						'default'     => 50,
						'description' => __( 'Number of entry to show per page. Use -1 to show all entries',
							'dialog-contact-form' ),
						'type'        => 'integer',
					),
					'offset'   => array(
						'required'    => false,
						'default'     => 0,
						'description' => __( 'Number of entry to displace or pass over. The \'offset\' parameter is ignored when \'per_page\'=> -1 (show all entries) is used.',
							'dialog-contact-form' ),
						'type'        => 'integer',
					),
					'order'    => array(
						'description' => __( 'Designates the ascending or descending order.',
							'dialog-contact-form' ),
						'required'    => false,
						'default'     => 'DESC',
						'enum'        => array( 'ASC', 'DESC' ),
						'type'        => 'string',
					),
					'orderby'  => array(
						'description' => __( 'Sort retrieved entries by parameter. One or more options can be passed.',
							'dialog-contact-form' ),
						'required'    => false,
						'default'     => 'created_at',
						'enum'        => array( 'id', 'form_id', 'user_id', 'status', 'created_at' ),
						'type'        => 'string',
					),
				),
			)
		) );

		register_rest_route( self::$namespace, '/entries/(?P<id>\d+)', array(
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( self::$instance, 'get_form_entry' ),
			),
			array(
				'methods'  => WP_REST_Server::DELETABLE,
				'callback' => array( self::$instance, 'delete_form_entry' ),
			),
		) );
	}

	/**
	 * Get a collection of entries
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return mixed|\WP_REST_Response
	 */
	public static function get_form_entries( WP_REST_Request $request ) {
		$form_id    = (int) $request->get_param( 'form_id' );
		$capability = current_user_can( 'edit_posts' );

		if ( $form_id ) {
			$capability = current_user_can( 'publish_pages', $form_id );
		}

		if ( ! $capability ) {
			return new WP_Error( 'forbidden',
				__( "You are not allowed to access contact forms entries.", 'dialog-contact-form' ),
				array( 'status' => 403 ) );
		}

		$args = array();

		$per_page = $request->get_param( 'per_page' );
		$offset   = $request->get_param( 'offset' );
		$order    = $request->get_param( 'order' );
		$orderby  = $request->get_param( 'orderby' );

		if ( null !== $per_page ) {
			$args['per_page'] = (int) $per_page;
		}

		if ( null !== $offset ) {
			$args['offset'] = (int) $offset;
		}

		if ( null !== $order ) {
			$args['order'] = (string) $order;
		}

		if ( null !== $orderby ) {
			$args['orderby'] = (string) $orderby;
		}

		$entry   = new Entry();
		$entries = $entry->find( $args );

		if ( ! $entries ) {
			return new WP_Error( 'not_found',
				__( "The requested contact form entry was not found.", 'dialog-contact-form' ),
				array( 'status' => 404 ) );
		}

		return rest_ensure_response( $entries );
	}

	/**
	 * Get form entry
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return mixed|\WP_REST_Response
	 */
	public static function get_form_entry( WP_REST_Request $request ) {
		$id = $request->get_param( 'id' );

		if ( ! current_user_can( 'publish_pages', $id ) ) {
			return new WP_Error( 'forbidden',
				__( "You are not allowed to access the requested contact form entry.", 'dialog-contact-form' ),
				array( 'status' => 403 ) );
		}

		$entry   = new Entry();
		$entries = $entry->findById( $id );

		if ( ! $entries ) {
			return new WP_Error( 'not_found', __( "The requested form entry was not found.", 'dialog-contact-form' ),
				array( 'status' => 404 ) );
		}

		return rest_ensure_response( $entries );
	}

	/**
	 * Delete form entry
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return mixed|\WP_REST_Response
	 */
	public static function delete_form_entry( WP_REST_Request $request ) {
		$id = (int) $request->get_param( 'id' );

		if ( ! current_user_can( 'publish_pages', $id ) ) {
			return new WP_Error( 'forbidden',
				__( "You are not allowed to access the requested form entry.", 'dialog-contact-form' ),
				array( 'status' => 403 ) );
		}

		$entry  = new Entry();
		$result = $entry->delete( $id );

		if ( false === $result ) {
			return new WP_Error( 'cannot_delete',
				__( "There was an error deleting the form entry.", 'dialog-contact-form' ),
				array( 'status' => 500 ) );
		}

		return rest_ensure_response( array( 'deleted' => true ) );
	}
}
