<?php

namespace DialogContactForm\Entries;

/**
 * The WP_List_Table class isn't automatically available to plugins,
 * So we need to check if it's available and load it if necessary.
 */
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Entry_List_Table extends \WP_List_Table {

	/**
	 * @var string
	 */
	private $date_format;

	/**
	 * @var string
	 */
	private $time_format;

	/**
	 * Entry database table name
	 *
	 * @var string
	 */
	private $table_name;

	/**
	 * WordPress database
	 *
	 * @var \wpdb
	 */
	private $db;

	/**
	 * @var array
	 */
	private $entry_count = array();

	/**
	 * @var bool
	 */
	private $is_unread = false;

	/**
	 * @var bool
	 */
	private $is_read = false;

	/**
	 * @var bool
	 */
	private $is_trash = false;

	/**
	 * Entry_List_Table constructor.
	 */
	public function __construct() {
		global $wpdb;

		//Set parent defaults
		$args = array(
			'singular' => 'entry',
			'plural'   => 'entries',
			'ajax'     => false,
			'screen'   => null,
		);

		$this->date_format = get_option( 'date_format' );
		$this->time_format = get_option( 'time_format' );
		$this->db          = $wpdb;
		$this->table_name  = $wpdb->prefix . 'dcf_entries';
		$this->entry_count = $this->count_items();

		parent::__construct( $args );
	}

	/**
	 * Message to be displayed when there are no items
	 */
	function no_items() {
		$status = isset( $_REQUEST['post_status'] ) ? $_REQUEST['post_status'] : null;

		if ( 'trash' === $status ) {
			esc_html_e( 'No entries found in Trash.', 'dialog-contact-from' );
		} elseif ( 'read' === $status ) {
			esc_html_e( 'No read entries found.', 'dialog-contact-from' );
		} elseif ( 'unread' === $status ) {
			esc_html_e( 'No unread entries found.', 'dialog-contact-from' );
		} else {
			esc_html_e( 'No entries found.', 'dialog-contact-from' );
		}
	}

	/**
	 * Get a list of columns. The format is:
	 * 'internal-name' => 'Title'
	 *
	 * @return array
	 */
	public function get_columns() {
		$columns = array(
			'cb'         => '<input type="checkbox"/>',
			'title'      => __( 'Form', 'dialog-contact-from' ),
			'user_id'    => __( 'User ID', 'dialog-contact-from' ),
			'user_ip'    => __( 'User IP', 'dialog-contact-from' ),
			'user_agent' => __( 'User Agent', 'dialog-contact-from' ),
			'referer'    => __( 'Referer', 'dialog-contact-from' ),
			'created_at' => __( 'Date', 'dialog-contact-from' ),
		);

		return $columns;
	}

	/**
	 * Render a column when no other specific method exists for that column.
	 *
	 * @param object $item A singular item (one full row's worth of data)
	 * @param string $column_name The name/slug of the column to be processed
	 *
	 * @return string Text or HTML to be placed inside the column <td>
	 */
	public function column_default( $item, $column_name ) {
		if ( is_object( $item ) ) {
			return isset( $item->{$column_name} ) ? $item->{$column_name} : '';
		}
		if ( is_array( $item ) ) {
			return isset( $item[ $column_name ] ) ? $item[ $column_name ] : '';
		}

		return '';
	}

	/**
	 * Get title column
	 *
	 * @param object $item
	 *
	 * @return string
	 */
	public function column_title( $item ) {
		$actions = array();

		if ( ! $this->is_trash ) {
			$view_url        = add_query_arg( array(
				'post_type' => 'dialog-contact-form',
				'page'      => 'dcf-entries',
				'tab'       => 'view',
				'id'        => $item->id,
			), admin_url( 'edit.php' ) );
			$actions['view'] = '<a href="' . $view_url . '">' . __( 'View', 'dialog-contact-from' ) . '</a>';

			if ( current_user_can( 'delete_pages' ) ) {
				$trash_url        = add_query_arg( array(
					'post_type' => 'dialog-contact-form',
					'page'      => 'dcf-entries',
					'action'    => 'trash',
					'entry_id'  => $item->id,
				), admin_url( 'edit.php' ) );
				$trash_url        = wp_nonce_url( $trash_url, 'dcf_entries_list', '_dcf_nonce' );
				$actions['trash'] = '<a href="' . $trash_url . '">' . __( 'Trash', 'dialog-contact-from' ) . '</a>';
			}

		} else {
			if ( current_user_can( 'edit_pages' ) ) {
				$restore_url        = add_query_arg( array(
					'post_type' => 'dialog-contact-form',
					'page'      => 'dcf-entries',
					'action'    => 'untrash',
					'entry_id'  => $item->id,
				), admin_url( 'edit.php' ) );
				$restore_url        = wp_nonce_url( $restore_url, 'dcf_entries_list', '_dcf_nonce' );
				$actions['untrash'] = '<a href="' . $restore_url . '">' . __( 'Restore', 'dialog-contact-from' ) . '</a>';
			}
			if ( current_user_can( 'delete_pages' ) ) {
				$delete_url        = add_query_arg( array(
					'post_type' => 'dialog-contact-form',
					'page'      => 'dcf-entries',
					'action'    => 'delete',
					'entry_id'  => $item->id,
				), admin_url( 'edit.php' ) );
				$delete_url        = wp_nonce_url( $delete_url, 'dcf_entries_list', '_dcf_nonce' );
				$actions['delete'] = '<a href="' . $delete_url . '">' . __( 'Delete Permanently', 'dialog-contact-from' ) . '</a>';
			}
		}

		//Return the title contents
		$title      = get_the_title( $item->form_id );
		$form_title = sprintf( '%s <b>(ID: %s)</b>', $title, $item->form_id );

		if ( 'unread' === $item->status ) {
			$form_title = $form_title . ' - <span class="label-new">' . __( 'new', 'dialog-contact-form' ) . '</span>';
		}

		return sprintf( '%s %s', $form_title, $this->row_actions( $actions ) );
	}

	/**
	 * Date column
	 *
	 * @param $item
	 *
	 * @return string
	 */
	public function column_created_at( $item ) {
		$date = new \DateTime( $item->created_at );

		return '<abbr title="' . $date->format( 'r' ) . '">' . $date->format( $this->date_format ) . '</abbr>';
	}

	/**
	 * Generate checkbox column
	 *
	 * @param object $item
	 *
	 * @return string
	 */
	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			$this->_args['singular'] . '_id',
			$item->id
		);
	}

	/**
	 * Get an associative array ( option_name => option_title ) with the list
	 * of bulk actions available on this table.
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = array();

		if ( current_user_can( 'edit_pages' ) && $this->is_trash ) {
			$actions['untrash'] = __( 'Restore', 'dialog-contact-from' );
		}

		if ( current_user_can( 'delete_pages' ) ) {
			if ( $this->is_trash ) {
				$actions['delete'] = __( 'Delete Permanently', 'dialog-contact-from' );
			} else {
				$actions['trash'] = __( 'Move to Trash', 'dialog-contact-from' );
			}
		}

		return $actions;
	}

	/**
	 * Prepares the list of items for displaying.
	 */
	public function prepare_items() {

		$this->is_unread = isset( $_REQUEST['post_status'] ) && 'unread' === $_REQUEST['post_status'];
		$this->is_read   = isset( $_REQUEST['post_status'] ) && 'read' === $_REQUEST['post_status'];
		$this->is_trash  = isset( $_REQUEST['post_status'] ) && 'trash' === $_REQUEST['post_status'];

		// Build column headers
		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		// First, lets decide how many records per page to show
		$per_page = $this->get_items_per_page( 'entries_per_page', 50 );

		// What page the user is currently looking at
		$current_page = $this->get_pagenum();

		$args = array(
			'orderby'  => ! empty( $_REQUEST['orderby'] ) ? $_REQUEST['orderby'] : 'id',
			'order'    => ! empty( $_REQUEST['order'] ) ? $_REQUEST['order'] : 'desc',
			'offset'   => ( $current_page - 1 ) * $per_page,
			'per_page' => $per_page,
		);

		// Total number of items
		$total_items = $this->entry_count;

		if ( $this->is_unread ) {
			$this->items = $this->get_unread_items( $args );
			$total_items = $total_items['unread'];
		} else if ( $this->is_read ) {
			$this->items = $this->get_read_items( $args );
			$total_items = $total_items['read'];
		} else if ( $this->is_trash ) {
			$this->items = $this->get_trash_items( $args );
			$total_items = $total_items['trash'];
		} else {
			$this->items = $this->get_items( $args );
			$total_items = ( $total_items['unread'] + $total_items['read'] );
		}

		/**
		 * REQUIRED. We also have to register our pagination options & calculations.
		 */
		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil( $total_items / $per_page )
		) );
	}

	/**
	 * Get an associative array ( id => link ) with the list
	 * of views available on this table.
	 *
	 * @return array
	 */
	protected function get_views() {
		$all_url    = add_query_arg( array(
			'post_type' => 'dialog-contact-form',
			'page'      => 'dcf-entries',
		), admin_url( 'edit.php' ) );
		$unread_url = add_query_arg( array(
			'post_type'   => 'dialog-contact-form',
			'page'        => 'dcf-entries',
			'post_status' => 'unread',
		), admin_url( 'edit.php' ) );
		$read_url   = add_query_arg( array(
			'post_type'   => 'dialog-contact-form',
			'page'        => 'dcf-entries',
			'post_status' => 'read',
		), admin_url( 'edit.php' ) );

		$trash_url = add_query_arg( array(
			'post_type'   => 'dialog-contact-form',
			'page'        => 'dcf-entries',
			'post_status' => 'trash',
		), admin_url( 'edit.php' ) );

		$all_count    = ( $this->entry_count['unread'] + $this->entry_count['read'] );
		$all_label    = sprintf( __( 'All (%d)', 'dialog-contact-form' ), $all_count );
		$unread_label = sprintf( __( 'Unread (%d)', 'dialog-contact-form' ), $this->entry_count['unread'] );
		$read_label   = sprintf( __( 'Read (%d)', 'dialog-contact-form' ), $this->entry_count['read'] );
		$trash_label  = sprintf( __( 'Trash (%d)', 'dialog-contact-form' ), $this->entry_count['trash'] );
		$unread_class = $this->is_unread ? 'current' : '';
		$read_class   = $this->is_read ? 'current' : '';
		$trash_class  = $this->is_trash ? 'current' : '';
		$all_class    = ! ( $this->is_trash || $this->is_unread || $this->is_read ) ? 'current' : '';

		return array(
			'all'    => '<a class="' . $all_class . '" href="' . $all_url . '">' . $all_label . '</a>',
			'unread' => '<a class="' . $unread_class . '" href="' . $unread_url . '">' . $unread_label . '</a>',
			'read'   => '<a class="' . $read_class . '" href="' . $read_url . '">' . $read_label . '</a>',
			'trash'  => '<a class="' . $trash_class . '" href="' . $trash_url . '">' . $trash_label . '</a>',
		);
	}

	/**
	 * Get unread & read entries
	 *
	 * @param array $args
	 *
	 * @return object
	 */
	public function get_items( $args ) {
		$cache_key = sprintf( 'all_entries_%s', md5( json_encode( $args ) ) );
		$items     = wp_cache_get( $cache_key, 'dialog-contact-form' );
		if ( false === $items ) {
			$query = $this->db->prepare(
				"SELECT * FROM `{$this->table_name}` WHERE `status` != 'trash' ORDER BY %s %s LIMIT %d OFFSET %d",
				$args['orderby'], $args['order'], $args['per_page'], $args['offset']
			);
			$items = $this->db->get_results( $query, OBJECT );

			$items = $this->_to_object( $items );

			wp_cache_add( $cache_key, $items, 'dialog-contact-form' );
		}

		return $items;
	}

	/**
	 * Get publish entries
	 *
	 * @param array $args
	 *
	 * @return object
	 */
	private function get_unread_items( $args ) {
		$cache_key = sprintf( 'unread_entries_%s', md5( json_encode( $args ) ) );
		$items     = wp_cache_get( $cache_key, 'dialog-contact-form' );
		if ( false === $items ) {
			$query = $this->db->prepare(
				"SELECT * FROM `{$this->table_name}` WHERE `status` = 'unread' ORDER BY %s %s LIMIT %d OFFSET %d",
				$args['orderby'], $args['order'], $args['per_page'], $args['offset']
			);
			$items = $this->db->get_results( $query, OBJECT );

			$items = $this->_to_object( $items );

			wp_cache_add( $cache_key, $items, 'dialog-contact-form' );
		}

		return $items;
	}

	/**
	 * Get publish entries
	 *
	 * @param array $args
	 *
	 * @return object
	 */
	private function get_read_items( $args ) {
		$cache_key = sprintf( 'read_entries_%s', md5( json_encode( $args ) ) );
		$items     = wp_cache_get( $cache_key, 'dialog-contact-form' );
		if ( false === $items ) {
			$query = $this->db->prepare(
				"SELECT * FROM `{$this->table_name}` WHERE `status` = 'read' ORDER BY %s %s LIMIT %d OFFSET %d",
				$args['orderby'], $args['order'], $args['per_page'], $args['offset']
			);
			$items = $this->db->get_results( $query, OBJECT );

			$items = $this->_to_object( $items );

			wp_cache_add( $cache_key, $items, 'dialog-contact-form' );
		}

		return $items;
	}

	/**
	 * Get trash items
	 *
	 * @param array $args
	 *
	 * @return object
	 */
	private function get_trash_items( $args ) {
		$cache_key = sprintf( 'trash_entries_%s', md5( json_encode( $args ) ) );
		$items     = wp_cache_get( $cache_key, 'dialog-contact-form' );
		if ( false === $items ) {
			$query = $this->db->prepare(
				"SELECT * FROM `{$this->table_name}` WHERE `status` = 'trash' ORDER BY %s %s LIMIT %d OFFSET %d",
				$args['orderby'], $args['order'], $args['per_page'], $args['offset']
			);
			$items = $this->db->get_results( $query, OBJECT );

			$items = $this->_to_object( $items );

			wp_cache_add( $cache_key, $items, 'dialog-contact-form' );
		}

		return $items;
	}

	/**
	 * Count items
	 *
	 * @return array
	 */
	private function count_items() {
		$counts = wp_cache_get( 'entries_count', 'dialog-contact-form' );

		if ( false === $counts ) {
			global $wpdb;
			$table = $wpdb->prefix . "dcf_entries";

			$query   = "SELECT status, COUNT( * ) AS num_entries FROM {$table} GROUP BY status";
			$results = $wpdb->get_results( $query, ARRAY_A );
			$counts  = array(
				'unread' => 0,
				'read'   => 0,
				'trash'  => 0,
			);

			foreach ( $results as $row ) {
				$counts[ $row['status'] ] = $row['num_entries'];
			}

			wp_cache_set( 'entries_count', $counts, 'dialog-contact-form' );
		}

		return $counts;
	}

	/**
	 * Convert date to object
	 *
	 * @param $data
	 *
	 * @return object
	 */
	private function _to_object( $data ) {
		if ( is_array( $data ) ) {
			return json_decode( json_encode( $data ), false );
		}

		return $data;
	}
}
