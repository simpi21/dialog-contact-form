<?php

namespace DialogContactForm;

use DialogContactForm\Abstracts\Template;
use WP_Error;
use WP_REST_Request;
use WP_REST_Server;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RestApi {

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
	public function rest_api_init() {
		$templates = TemplateManager::init();

		register_rest_route( self::$namespace, '/forms', array(
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_contact_forms' ),
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
						'description' => __( 'Designates the ascending or descending order.', 'dialog-contact-form' ),
						'required'    => false,
						'default'     => 'DESC',
						'enum'        => array( 'ASC', 'DESC' ),
					),
					'orderby'  => array(
						'description' => __( 'Sort retrieved posts by parameter. One or more options can be passed.',
							'dialog-contact-form' ),
						'required'    => false,
						'default'     => 'date',
						'enum'        => array( 'ID', 'title', 'date', 'modified', 'rand' ),
					),
					'search'   => array(
						'description' => __( 'Show forms based on a keyword search.', 'dialog-contact-form' ),
						'type'        => 'string',
					),
				),
			),
			array(
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'create_contact_form' ),
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
				'callback' => array( $this, 'get_contact_form' ),
			),
			array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'update_contact_form' ),
			),
			array(
				'methods'  => WP_REST_Server::DELETABLE,
				'callback' => array( $this, 'delete_contact_form' ),
			),
		) );

		register_rest_route( self::$namespace, '/entries', array(
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_form_entries' ),
			)
		) );

		register_rest_route( self::$namespace, '/entries/(?P<id>\d+)', array(
			array(
				'methods'  => WP_REST_Server::READABLE,
				'callback' => array( $this, 'get_form_entry' ),
			),
			array(
				'methods'  => WP_REST_Server::EDITABLE,
				'callback' => array( $this, 'update_form_entry' ),
			),
			array(
				'methods'  => WP_REST_Server::DELETABLE,
				'callback' => array( $this, 'delete_form_entry' ),
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
	public function get_contact_forms( WP_REST_Request $request ) {
		if ( ! current_user_can( 'read_page' ) ) {
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

		$items = ContactForm::find( $args );

		$response = array();

		/** @var ContactForm $item */
		foreach ( $items as $item ) {
			$response[] = array(
				'id'    => $item->id(),
				'slug'  => $item->name(),
				'title' => $item->title(),
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
	public function create_contact_form( WP_REST_Request $request ) {

		if ( ! current_user_can( 'edit_page' ) ) {
			return new WP_Error( 'forbidden',
				__( "You are not allowed to create a contact form.", 'dialog-contact-form' ),
				array( 'status' => 403 ) );
		}

		$templateManager = TemplateManager::init();
		$template        = $request->get_param( 'template' );
		$className       = $templateManager->get( $template );
		$class           = new $className;

		if ( ! $class instanceof Template ) {
			return new WP_Error( 'template_not_found', __( "Form template is not available.", 'dialog-contact-form' ),
				array( 'status' => 400 ) );
		}

		$post_id = wp_insert_post( array(
			'post_title'     => $template->getTitle(),
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

		$item     = new ContactForm( $post_id );
		$response = array(
			'id'            => $item->id(),
			'slug'          => $item->name(),
			'title'         => $item->title(),
			'properties'    => array(),
			'config_errors' => array(),
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
	public function get_contact_form( WP_REST_Request $request ) {
		$id = $request->get_param( 'id' );

		$response = array();

		return rest_ensure_response( $response );
	}

	/**
	 * Update contact form
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return mixed|\WP_REST_Response
	 */
	public function update_contact_form( WP_REST_Request $request ) {
		$id = $request->get_param( 'id' );

		$response = array();

		return rest_ensure_response( $response );
	}

	/**
	 * Delete contact form
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return mixed|\WP_REST_Response
	 */
	public function delete_contact_form( WP_REST_Request $request ) {
		$id = $request->get_param( 'id' );

		$response = array();

		return rest_ensure_response( $response );
	}

	/**
	 * Get a collection of entries
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return array
	 */
	public function get_form_entries( WP_REST_Request $request ) {
		$id = $request->get_param( 'id' );

		$response = array();

		return rest_ensure_response( $response );
	}

	/**
	 * Get form entry
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return mixed|\WP_REST_Response
	 */
	public function get_form_entry( WP_REST_Request $request ) {
		$id = $request->get_param( 'id' );

		$response = array();

		return rest_ensure_response( $response );
	}

	/**
	 * Update form entry
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return mixed|\WP_REST_Response
	 */
	public function update_form_entry( WP_REST_Request $request ) {
		$id = $request->get_param( 'id' );

		$response = array();

		return rest_ensure_response( $response );
	}

	/**
	 * Delete form entry
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return mixed|\WP_REST_Response
	 */
	public function delete_form_entry( WP_REST_Request $request ) {
		$id = $request->get_param( 'id' );

		$response = array();

		return rest_ensure_response( $response );
	}
}
