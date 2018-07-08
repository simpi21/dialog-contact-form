<?php

namespace DialogContactForm\REST;

use DialogContactForm\Abstracts\Template;
use DialogContactForm\Collections\Templates;
use DialogContactForm\Entries\Entry;
use DialogContactForm\Supports\ContactForm;
use WP_Error;
use WP_REST_Request;
use WP_REST_Server;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Controller {

	/**
	 * REST API namespace
	 *
	 * @var string
	 */
	protected static $namespace;

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

			self::$namespace = 'dialog-contact-form/v1';

			add_action( 'rest_api_init', array( self::$instance, 'rest_api_init' ) );
		}

		return self::$instance;
	}

	/**
	 * Fires when preparing to serve an API request.
	 */
	public static function rest_api_init() {
		$templates = Templates::init();

		register_rest_route( self::$namespace, '/forms', array(
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( self::$instance, 'get_contact_forms' ),
				'args'     => array(
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
				),
			),
			array(
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array( self::$instance, 'create_contact_form' ),
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
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( self::$instance, 'get_contact_form' ),
			),
			array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => array( self::$instance, 'update_contact_form' ),
			),
			array(
				'methods'  => WP_REST_Server::DELETABLE,
				'callback' => array( self::$instance, 'delete_contact_form' ),
			),
		) );

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
	 * Get contact forms
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return mixed|WP_Error|\WP_REST_Response
	 */
	public static function get_contact_forms( WP_REST_Request $request ) {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return new WP_Error( 'forbidden',
				__( "You are not allowed to access contact forms.", 'dialog-contact-form' ),
				array( 'status' => 403 ) );
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

		return rest_ensure_response( $response );
	}

	/**
	 * Create new contact form
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return mixed|WP_Error|\WP_REST_Response
	 */
	public static function create_contact_form( WP_REST_Request $request ) {

		if ( ! current_user_can( 'publish_pages' ) ) {
			return new WP_Error( 'forbidden',
				__( "You are not allowed to create a contact form.", 'dialog-contact-form' ),
				array( 'status' => 403 ) );
		}

		$templateManager = Templates::init();
		$template        = $request->get_param( 'template' );
		$template        = in_array( $template, array_keys( $templateManager->all() ) ) ? $template : 'blank';
		$className       = $templateManager->get( $template );
		$class           = new $className;

		if ( ! $class instanceof Template ) {
			return new WP_Error( 'template_not_found', __( "Form template is not available.", 'dialog-contact-form' ),
				array( 'status' => 400 ) );
		}

		$post_id = wp_insert_post( array(
			'post_title'     => $class->getTitle(),
			'post_status'    => 'publish',
			'post_type'      => DIALOG_CONTACT_FORM_POST_TYPE,
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		) );

		if ( is_wp_error( $post_id ) ) {
			return new WP_Error( 'cannot_save',
				__( "There was an error saving the contact form.", 'contact-form-7' ),
				array( 'status' => 500 ) );
		}

		$class->run( $post_id );

		$form     = new ContactForm( $post_id );
		$response = array(
			'id'    => $form->getId(),
			'title' => $form->getTitle(),
		);

		return rest_ensure_response( $response );
	}

	/**
	 * Get contact form
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return mixed|\WP_REST_Response
	 */
	public static function get_contact_form( WP_REST_Request $request ) {

		$id = $request->get_param( 'id' );

		if ( ! current_user_can( 'publish_pages', $id ) ) {
			return new WP_Error( 'forbidden',
				__( "You are not allowed to access the requested contact form.", 'dialog-contact-form' ),
				array( 'status' => 403 ) );
		}

		$form = new ContactForm( $id );

		if ( ! $form->getId() ) {
			return new WP_Error( 'not_found', __( "The requested contact form was not found.", 'dialog-contact-form' ),
				array( 'status' => 404 ) );
		}

		$response = $form->toArray();

		return rest_ensure_response( $response );
	}

	/**
	 * Update contact form
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return mixed|\WP_REST_Response
	 */
	public static function update_contact_form( WP_REST_Request $request ) {
		$id = $request->get_param( 'id' );

		if ( ! current_user_can( 'publish_pages', $id ) ) {
			return new WP_Error( 'forbidden',
				__( "You are not allowed to access the requested contact form.", 'dialog-contact-form' ),
				array( 'status' => 403 ) );
		}

		$form = new ContactForm( $id );

		if ( ! $form->getId() ) {
			return new WP_Error( 'not_found', __( "The requested contact form was not found.", 'dialog-contact-form' ),
				array( 'status' => 404 ) );
		}

		$form->update( $id, $request->get_params() );

		$response = $form->toArray();

		return rest_ensure_response( $response );
	}

	/**
	 * Delete contact form
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return mixed|\WP_REST_Response
	 */
	public static function delete_contact_form( WP_REST_Request $request ) {
		$id = $request->get_param( 'id' );

		if ( ! current_user_can( 'publish_pages', $id ) ) {
			return new WP_Error( 'forbidden',
				__( "You are not allowed to access the requested contact form.", 'dialog-contact-form' ),
				array( 'status' => 403 ) );
		}

		$item = new ContactForm( $id );

		if ( ! $item->getId() ) {
			return new WP_Error( 'not_found', __( "The requested contact form was not found.", 'dialog-contact-form' ),
				array( 'status' => 404 ) );
		}

		if ( ! $item->delete( $id ) ) {
			return new WP_Error( 'cannot_delete',
				__( "There was an error deleting the contact form.", 'dialog-contact-form' ),
				array( 'status' => 500 ) );
		}

		$response = array( 'deleted' => true );

		return rest_ensure_response( $response );
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
		$entries = $entry->get( $id );

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
