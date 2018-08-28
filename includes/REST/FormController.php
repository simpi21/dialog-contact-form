<?php

namespace DialogContactForm\REST;

use DialogContactForm\Abstracts\Template;
use DialogContactForm\Collections\Templates;
use DialogContactForm\Supports\ContactForm;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FormController extends Controller {

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
		$templates = Templates::init();

		register_rest_route( self::$namespace, '/forms', array(
			array(
				'methods'  => \WP_REST_Server::READABLE,
				'callback' => array( self::$instance, 'get_items' ),
				'args'     => self::$instance->get_get_items_args(),
			),
			array(
				'methods'  => \WP_REST_Server::CREATABLE,
				'callback' => array( self::$instance, 'create_item' ),
				'args'     => array(
					'template' => array(
						'required'    => false,
						'default'     => 'blank',
						'description' => __( 'Template unique id.', 'dialog-contact-form' ),
						'type'        => 'string',
						'enum'        => array_keys( $templates->all() ),
					),
				),
			),
		) );

		register_rest_route( self::$namespace, '/forms/(?P<id>\d+)', array(
			array(
				'methods'  => \WP_REST_Server::READABLE,
				'callback' => array( self::$instance, 'get_item' ),
			),
			array(
				'methods'  => \WP_REST_Server::EDITABLE,
				'callback' => array( self::$instance, 'update_item' ),
			),
			array(
				'methods'  => \WP_REST_Server::DELETABLE,
				'callback' => array( self::$instance, 'delete_item' ),
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
		if ( ! current_user_can( 'edit_posts' ) ) {
			return $this->respondForbidden( null,
				__( "You are not authorized to perform the action.", 'dialog-contact-form' ) );
		}

		$args = array();

		$per_page = $request->get_param( 'per_page' );

		if ( null !== $per_page ) {
			$args['posts_per_page'] = (int) $per_page;
		}

		$offset = $request->get_param( 'offset' );

		if ( null !== $offset ) {
			$args['offset'] = (int) $offset;
		}

		$order = $request->get_param( 'order' );

		if ( null !== $order ) {
			$args['order'] = (string) $order;
		}

		$orderby = $request->get_param( 'orderby' );

		if ( null !== $orderby ) {
			$args['orderby'] = (string) $orderby;
		}

		$search = $request->get_param( 'search' );

		if ( null !== $search ) {
			$args['s'] = (string) $search;
		}

		$forms = ContactForm::find( $args );

		$response = array();

		/** @var ContactForm $form */
		foreach ( $forms as $form ) {
			$response[] = array(
				'id'    => $form->getId(),
				'title' => $form->getTitle(),
			);
		}

		return $this->respondOK( $response );
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

		$form = new ContactForm( $id );

		if ( ! $form->getId() ) {
			return $this->respondNotFound( null,
				__( "The requested contact form was not found.", 'dialog-contact-form' ) );
		}

		$response = $form->toArray();

		return $this->respondOK( $response );
	}

	/**
	 * Creates one item from the collection.
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return \WP_REST_Response Response object
	 */
	public function create_item( $request ) {

		if ( ! current_user_can( 'publish_pages' ) ) {
			return $this->respondForbidden( null,
				__( "You are not authorized to perform the action.", 'dialog-contact-form' ) );
		}

		$templateManager = Templates::init();
		$template        = $request->get_param( 'template' );
		$template        = in_array( $template, array_keys( $templateManager->all() ) ) ? $template : '';
		$className       = $templateManager->get( $template );
		$class           = '';
		if ( ! empty( $className ) ) {
			$class = new $className;
		}

		if ( ! $class instanceof Template ) {
			return $this->respondUnprocessableEntity( null,
				__( "Form template is not available.", 'dialog-contact-form' ) );
		}

		$post_id = wp_insert_post( array(
			'post_title'     => $class->getTitle(),
			'post_status'    => 'publish',
			'post_type'      => DIALOG_CONTACT_FORM_POST_TYPE,
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		) );

		if ( is_wp_error( $post_id ) ) {
			$this->respondInternalServerError( null,
				__( "There was an error saving the contact form.", 'dialog-contact-form' ) );
		}

		$class->run( $post_id );

		$form     = new ContactForm( $post_id );
		$response = array(
			'id'    => $form->getId(),
			'title' => $form->getTitle(),
		);

		return $this->respondCreated( $response );
	}

	/**
	 * Updates one item from the collection.
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return \WP_REST_Response Response object
	 */
	public function update_item( $request ) {
		$id = $request->get_param( 'id' );

		if ( ! current_user_can( 'publish_pages', $id ) ) {
			return $this->respondForbidden( null,
				__( "You are not authorized to perform the action.", 'dialog-contact-form' ) );
		}

		$form = new ContactForm( $id );

		if ( ! $form->getId() ) {
			return $this->respondNotFound( null,
				__( "The requested contact form was not found.", 'dialog-contact-form' ) );
		}

		$form->update( $id, $request->get_params() );

		$response = $form->toArray();

		return $this->respondOK( $response );
	}

	/**
	 * Deletes one item from the collection.
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return \WP_REST_Response Response object
	 */
	public function delete_item( $request ) {
		$id = $request->get_param( 'id' );

		if ( ! current_user_can( 'publish_pages', $id ) ) {
			return $this->respondForbidden( null,
				__( "You are not authorized to perform the action.", 'dialog-contact-form' ) );
		}

		$item = new ContactForm( $id );

		if ( ! $item->getId() ) {
			return $this->respondNotFound( null,
				__( "The requested contact form was not found.", 'dialog-contact-form' ) );
		}

		if ( ! $item->delete( $id ) ) {
			return $this->respondInternalServerError( null,
				__( "There was an error deleting the contact form.", 'dialog-contact-form' ) );
		}

		$response = array( 'deleted' => true );

		return $this->respondOK( $response );
	}

	/**
	 * @return array
	 */
	private function get_get_items_args() {
		return array(
			'per_page' => array(
				'required'    => false,
				'default'     => 50,
				'description' => __( 'Number of form to show per page. Use -1 to show all forms',
					'dialog-contact-form' ),
				'type'        => 'integer',
			),
			'offset'   => array(
				'required'    => false,
				'default'     => 0,
				'description' => __( 'Number of form to displace or pass over. The \'offset\' parameter is ignored when \'per_page\'=> -1 (show all forms) is used.',
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
				'description' => __( 'Sort retrieved posts by parameter. One or more options can be passed.',
					'dialog-contact-form' ),
				'required'    => false,
				'default'     => 'date',
				'enum'        => array( 'ID', 'title', 'date', 'modified', 'rand' ),
				'type'        => 'string',
			),
			'search'   => array(
				'description' => __( 'Show forms based on a keyword search.', 'dialog-contact-form' ),
				'type'        => 'string',
			),
		);
	}
}
