<?php

namespace DialogContactForm\REST;

use DialogContactForm\Entries\Entry;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

defined( 'ABSPATH' ) || exit;

class EntryController extends ApiController {

	/**
	 * The instance of the class
	 *
	 * @var self
	 */
	private static $instance;

	/**
	 * Only one instance of the class can be loaded.
	 *
	 * @return self
	 */
	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self;

			add_action( 'rest_api_init', array( self::$instance, 'register_routes' ) );
		}

		return self::$instance;
	}

	/**
	 * Registers the routes for the objects of the controller.
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/entries', [
			[
				'methods'  => WP_REST_Server::READABLE,
				'callback' => [ $this, 'get_items' ],
				'args'     => $this->get_collection_params()
			],
		] );

		register_rest_route( $this->namespace, '/entries/batch', [
			[
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => [ $this, 'update_batch_items' ],
				'args'     => $this->get_batch_params()
			],
		] );

		register_rest_route( $this->namespace, '/entries/(?P<id>\d+)', [
			[
				'methods'  => WP_REST_Server::READABLE,
				'callback' => [ $this, 'get_item' ],
			],
			[
				'methods'  => WP_REST_Server::DELETABLE,
				'callback' => [ $this, 'delete_item' ],
			],
		] );
	}

	/**
	 * Retrieves a collection of items.
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_REST_Response Response object on success, or WP_Error object on failure.
	 */
	public function get_items( $request ) {

		if ( ! current_user_can( 'publish_pages' ) ) {
			return $this->respondForbidden();
		}

		$args = array();

		$form_id  = $request->get_param( 'form_id' );
		$page     = $request->get_param( 'page' );
		$per_page = $request->get_param( 'per_page' );
		$order    = $request->get_param( 'order' );
		$orderby  = $request->get_param( 'orderby' );

		if ( null !== $form_id ) {
			$args['form_id'] = (int) $form_id;
		}

		if ( null !== $per_page ) {
			$args['per_page'] = (int) $per_page;
		}

		if ( null !== $page ) {
			$args['page'] = (int) $page;
		}

		if ( null !== $order ) {
			$args['order'] = (string) $order;
		}

		if ( null !== $orderby ) {
			$args['orderby'] = (string) $orderby;
		}

		$entry = new Entry();
		$items = $entry->find( $args );

		return $this->respondOK( [ 'items' => $items ] );
	}

	/**
	 * Retrieves one item from the collection.
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_REST_Response Response object on success, or WP_Error object on failure.
	 */
	public function get_item( $request ) {
		if ( ! current_user_can( 'publish_pages' ) ) {
			return $this->respondForbidden();
		}

		$id      = $request->get_param( 'id' );
		$entry   = new Entry();
		$entries = $entry->findById( $id );

		if ( ! $entries ) {
			return $this->respondNotFound();
		}

		return $this->respondOK( $entries );
	}

	/**
	 * Deletes one item from the collection.
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_REST_Response Response object on success, or WP_Error object on failure.
	 */
	public function delete_item( $request ) {
		if ( ! current_user_can( 'publish_pages' ) ) {
			return $this->respondForbidden();
		}

		$id     = $request->get_param( 'id' );
		$entry  = new Entry();
		$result = $entry->delete( $id );

		if ( false === $result ) {
			return $this->respondInternalServerError( 'rest_cannot_delete',
				__( "There was an error deleting the form entry.", 'dialog-contact-form' ) );
		}

		return $this->respondOK();
	}

	/**
	 * Create/Update/Delete multiple items from the collection.
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_REST_Response Response object on success, or WP_Error object on failure.
	 */
	public function update_batch_items( $request ) {
		return $this->respondOK( [ 'batch' => true ] );
	}

	/**
	 * Retrieves the query params for the collections.
	 *
	 * @return array Query parameters for the collection.
	 */
	public function get_collection_params() {
		$params = parent::get_collection_params();

		$params = array_merge( $params, [
			'form_id' => [
				'description'       => __( 'Retrieve items only related to form ID.', 'dialog-contact-form' ),
				'required'          => false,
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'validate_callback' => 'rest_validate_request_arg',
			],
			'order'   => [
				'description' => __( 'Designates the ascending or descending order.', 'dialog-contact-form' ),
				'required'    => false,
				'default'     => 'DESC',
				'enum'        => [ 'ASC', 'DESC' ],
				'type'        => 'string',
			],
			'orderby' => [
				'description' => __( 'Sort retrieved entries by parameter.', 'dialog-contact-form' ),
				'required'    => false,
				'default'     => 'id',
				'enum'        => [ 'id', 'form_id', 'user_id', 'status', 'created_at' ],
				'type'        => 'string',
			],
		] );

		return $params;
	}

	/**
	 * Retrieves the query params for the batch operation.
	 *
	 * @return array Query parameters for the batch operation.
	 */
	public function get_batch_params() {
		return [
			'delete' => [
				'description' => __( 'List of items ids to delete.', 'dialog-contact-form' ),
				'required'    => false,
			],
		];
	}
}