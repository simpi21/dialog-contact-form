<?php

namespace DialogContactForm\Supports;

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
	 * Entry_List_Table constructor.
	 */
	public function __construct() {
		//Set parent defaults
		$args = array(
			'singular' => 'entry',
			'plural'   => 'entries',
			'ajax'     => false,
			'screen'   => null,
		);

		$this->date_format = get_option( 'date_format' );
		$this->time_format = get_option( 'time_format' );

		parent::__construct( $args );
	}

	/**
	 * Message to be displayed when there are no items
	 */
	function no_items() {
		_e( 'No entries found.', 'dialog-contact-from' );
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
		$title      = get_the_title( $item->form_id );
		$form_title = sprintf( '%s <b>(ID: %s)</b>', $title, $item->form_id );

		$view_url = add_query_arg( array(
			'post_type' => 'dialog-contact-form',
			'page'      => 'dcf-entries',
			'tab'       => 'view',
			'id'        => $item->id,
		), admin_url( 'edit.php' ) );

		//Build row actions
		$actions = array(
			'view'  => '<a href="' . $view_url . '">' . __( 'View', 'dialog-contact-from' ) . '</a>',
			'trash' => '<a href="' . $view_url . '">' . __( 'Trash', 'dialog-contact-from' ) . '</a>',
		);

		//Return the title contents
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
		$date        = new \DateTime( $item->created_at );
		$full_format = $this->date_format . ' ' . $this->time_format;

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
		$actions = array(
			'delete' => __( 'Delete', 'dialog-contact-from' )
		);

		return $actions;
	}

	/**
	 * Prepares the list of items for displaying.
	 */
	public function prepare_items() {

		// Build column headers
		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		// First, lets decide how many records per page to show
		$per_page = $this->get_items_per_page( 'dcf_entry_per_page', 50 );

		// What page the user is currently looking at
		$current_page = $this->get_pagenum();

		$args = array(
			'orderby'  => ! empty( $_REQUEST['orderby'] ) ? $_REQUEST['orderby'] : 'id',
			'order'    => ! empty( $_REQUEST['order'] ) ? $_REQUEST['order'] : 'desc',
			'offset'   => ( $current_page - 1 ) * $per_page,
			'per_page' => $per_page,
		);

		$this->items = $this->get_items( $args );

		// Total number of items
		$total_items = $this->count_items();

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
	 * Get items
	 *
	 * @param $args
	 *
	 * @return bool|mixed|object
	 */
	private function get_items( $args ) {
		$cache_key   = sprintf( 'all-%s', $this->_args["plural"] );
		$cache_group = sprintf( 'all-%s-group', $this->_args["plural"] );
		$items       = wp_cache_get( $cache_key, $cache_group );
		if ( false === $items ) {
			$movie = new Entry();
			$items = $this->_to_object( $movie->get( $args ) );
			wp_cache_set( $cache_key, $items, $cache_group );
		}

		return $items;
	}

	/**
	 * Count items
	 *
	 * @return int
	 */
	private function count_items() {
		global $wpdb;
		$table = $wpdb->prefix . "dcf_entries";

		return (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table" );
	}

	/**
	 * Convert date to object
	 *
	 * @param $data
	 *
	 * @return object
	 */
	private function _to_object( $data ) {
		if ( is_object( $data ) ) {
			return $data;
		}

		return json_decode( json_encode( $data ), false );
	}
}
