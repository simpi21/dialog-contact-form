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
	 * @param $form_id
	 *
	 * @return array
	 */
	protected static function get_data_table_columns( $form_id ) {
		$fields       = (array) get_post_meta( $form_id, '_contact_form_fields', true );
		$action_store = get_post_meta( $form_id, '_action_store_submission', true );
		$columns_keys = isset( $action_store['data_table_fields'] ) ? $action_store['data_table_fields'] : [];
		$entryColumns = Entry::get_columns_label();
		$columns      = [];
		foreach ( $columns_keys as $index => $key ) {
			if ( isset( $entryColumns[ $key ] ) ) {
				$columns[ $index ] = [ 'key' => $key, 'label' => $entryColumns[ $key ] ];
			} else {
				foreach ( $fields as $field ) {
					if ( $field['field_id'] == $key ) {
						$columns[ $index ] = [ 'key' => $key, 'label' => $field['field_title'] ];
					}
				}
			}
		}

		return $columns;
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
		register_rest_route( $this->namespace, '/forms/(?P<form_id>\d+)/entries', [
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

		register_rest_route( $this->namespace, '/entries/status', [
			[
				'methods'  => WP_REST_Server::READABLE,
				'callback' => [ $this, 'get_forms_status' ],
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

		$form_id = $request->get_param( 'form_id' );

		$page = $request->get_param( 'page' );
		$page = is_numeric( $page ) && intval( $page ) > 0 ? $page : 1;

		$per_page = $request->get_param( 'per_page' );
		$per_page = is_numeric( $per_page ) && intval( $per_page ) > 0 ? $per_page : 1;

		$order   = $request->get_param( 'order' );
		$orderby = $request->get_param( 'orderby' );

		$status = $request->get_param( 'status' );
		$status = in_array( $status, [ 'all', 'read', 'unread', 'trash' ] ) ? $status : 'all';

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

		if ( null !== $status ) {
			$args['status'] = (string) $status;
		}

		$columns      = self::get_data_table_columns( $form_id );
		$columns_keys = wp_list_pluck( $columns, 'key' );

		$_items = ( new Entry )->find( $args );
		$items  = [];
		foreach ( $_items as $index => $item ) {
			$items[ $index ] = [ 'id' => $item->getId() ];
			$data            = $item->toArray();
			$field_values    = $data['field_values'];
			foreach ( $columns_keys as $columns_key ) {
				if ( isset( $data[ $columns_key ] ) ) {
					$items[ $index ][ $columns_key ] = $data[ $columns_key ];
				}
				if ( isset( $field_values[ $columns_key ] ) ) {
					$items[ $index ][ $columns_key ] = $field_values[ $columns_key ];
				}
			}
		}

		$counts     = Entry::get_form_entries_counts( $form_id );
		$pagination = $this->getPaginationMetadata( [
			'totalCount'  => $counts[ $status ],
			'limit'       => $per_page,
			'currentPage' => $page,
		] );

		return $this->respondOK( [
			'items'      => $items,
			'counts'     => $counts,
			'pagination' => $pagination,
			'metaData'   => [
				'columns'          => $columns,
				'primaryColumn'    => $columns[0]['key'],
				'actions'          => [
					[ 'key' => 'view', 'label' => __( 'View', 'dialog-contact-form' ) ],
					[ 'key' => 'trash', 'label' => __( 'Trash', 'dialog-contact-form' ) ],
				],
				'trashActions'     => [
					[ 'key' => 'restore', 'label' => __( 'Restore', 'dialog-contact-form' ) ],
					[ 'key' => 'delete', 'label' => __( 'Delete Permanently', 'dialog-contact-form' ) ],
				],
				'bulkActions'      => [
					[ 'key' => 'trash', 'label' => __( 'Move to Trash', 'dialog-contact-form' ) ],
				],
				'trashBulkActions' => [
					[ 'key' => 'restore', 'label' => __( 'Restore', 'dialog-contact-form' ) ],
					[ 'key' => 'delete', 'label' => __( 'Delete Permanently', 'dialog-contact-form' ) ],
				],
				'statuses'         => [
					[ 'key' => 'all', 'label' => __( 'All', 'dialog-contact-form' ), 'count' => $counts['all'] ],
					[ 'key' => 'read', 'label' => __( 'Read', 'dialog-contact-form' ), 'count' => $counts['read'] ],
					[ 'key'   => 'unread',
					  'label' => __( 'Unread', 'dialog-contact-form' ),
					  'count' => $counts['unread']
					],
					[ 'key' => 'trash', 'label' => __( 'Trash', 'dialog-contact-form' ), 'count' => $counts['trash'] ],
				],
			]
		] );
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
		$trash_items   = $request->get_param( 'trash' );
		$restore_items = $request->get_param( 'restore' );
		$delete_items  = $request->get_param( 'delete' );
		$ids           = [];
		$entry         = new Entry;

		if ( ! empty( $trash_items ) ) {
			$ids = is_string( $trash_items ) ? explode( ',', $trash_items ) : $trash_items;
			$ids = count( $ids ) ? array_map( 'intval', $ids ) : [];
			foreach ( $ids as $id ) {
				$entry->update( [ 'status' => 'trash' ], [ 'id' => $id ], '%s', '%d' );
			}
		}

		if ( ! empty( $restore_items ) ) {
			$ids = is_string( $restore_items ) ? explode( ',', $restore_items ) : $restore_items;
			$ids = count( $ids ) ? array_map( 'intval', $ids ) : [];
			foreach ( $ids as $id ) {
				$entry->update( [ 'status' => 'read' ], [ 'id' => $id ], '%s', '%d' );
			}
		}

		if ( ! empty( $delete_items ) ) {
			$ids = is_string( $delete_items ) ? explode( ',', $delete_items ) : $delete_items;
			$ids = count( $ids ) ? array_map( 'intval', $ids ) : [];
			foreach ( $ids as $id ) {
				$entry->delete( $id );
			}
		}

		return $this->respondOK( $ids );
	}

	/**
	 * Get entries status group by form id
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_REST_Response Response object on success, or WP_Error object on failure.
	 */
	public function get_forms_status( $request ) {
		global $wpdb;

		$forms = get_posts( array(
			'posts_per_page' => - 1,
			'orderby'        => 'ID',
			'order'          => 'DESC',
			'post_type'      => 'dialog-contact-form',
			'post_status'    => 'publish',
		) );

		$table   = $wpdb->prefix . "dcf_entries";
		$query   = "SELECT status, form_id, COUNT( * ) AS num_entries FROM {$table} GROUP BY status, form_id";
		$results = $wpdb->get_results( $query, ARRAY_A );

		$counts = array();
		foreach ( $results as $row ) {
			$counts[ $row['form_id'] ][ $row['status'] ] = intval( $row['num_entries'] );
		}

		$default_count = array( 'unread' => 0, 'read' => 0, 'trash' => 0, );

		$response = [];
		foreach ( $forms as $form ) {
			$_count       = isset( $counts[ $form->ID ] ) ? $counts[ $form->ID ] : array();
			$count        = wp_parse_args( $_count, $default_count );
			$count['all'] = ( $count['unread'] + $count['read'] );

			$response[] = [
				'form_id'    => $form->ID,
				'form_title' => $form->post_title,
				'counts'     => $count,
			];
		}

		return $this->respondOK( $response );
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