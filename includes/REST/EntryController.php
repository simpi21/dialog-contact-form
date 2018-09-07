<?php

namespace DialogContactForm\REST;

use DialogContactForm\Entries\Entry;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
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
				'methods'  => \WP_REST_Server::READABLE,
				'callback' => array( self::$instance, 'get_items' ),
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
				'methods'  => \WP_REST_Server::READABLE,
				'callback' => array( self::$instance, 'get_item' ),
			),
			array(
				'methods'  => \WP_REST_Server::DELETABLE,
				'callback' => array( self::$instance, 'delete_item' ),
			),
		) );

		register_rest_route( self::$namespace, '/entries/list', array(
			array(
				'methods'  => \WP_REST_Server::READABLE,
				'callback' => array( self::$instance, 'get_item_list' ),
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
		$form_id    = (int) $request->get_param( 'form_id' );
		$capability = current_user_can( 'edit_posts' );

		if ( $form_id ) {
			$capability = current_user_can( 'publish_pages', $form_id );
		}

		if ( ! $capability ) {
			return $this->respondForbidden( null,
				__( "You are not authorized to perform the action.", 'dialog-contact-form' ) );
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

		$entries = Entry::find( $args );

		if ( ! $entries ) {
			return $this->respondNotFound( null,
				__( "The requested contact form entry was not found.", 'dialog-contact-form' ) );
		}

		return $this->respondOK( $entries );
	}

	/**
	 * Retrieves one item from the collection.
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function get_item( $request ) {
		$id = $request->get_param( 'id' );

		if ( ! current_user_can( 'publish_pages', $id ) ) {
			return $this->respondForbidden( null,
				__( "You are not authorized to perform the action.", 'dialog-contact-form' ) );
		}

		$entries = Entry::findById( $id );

		if ( ! $entries ) {
			return $this->respondNotFound( null,
				__( "The requested form entry was not found.", 'dialog-contact-form' ) );
		}

		return $this->respondOK( $entries );
	}

	/**
	 * Deletes one item from the collection.
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return \WP_REST_Response Response object
	 */
	public function delete_item( $request ) {
		$id = (int) $request->get_param( 'id' );

		if ( ! current_user_can( 'publish_pages', $id ) ) {
			return $this->respondForbidden( null,
				__( "You are not authorized to perform the action.", 'dialog-contact-form' ) );
		}

		$result = Entry::delete( $id );

		if ( false === $result ) {
			return $this->respondInternalServerError( null,
				__( "There was an error deleting the form entry.", 'dialog-contact-form' ) );
		}

		return $this->respondOK( array( 'deleted' => true ) );
	}

	/**
	 * Deletes one item from the collection.
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return \WP_REST_Response Response object
	 */
	public function get_item_list( $request ) {
		$counts = \DialogContactForm\Models\Entry::statusCount();

		return $this->respondOK( [ 'items' => $counts ] );
	}
}
