<?php

namespace DialogContactForm\Entries;

use DialogContactForm\Supports\ContactForm;
use DialogContactForm\Supports\Utils;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Entry {

	/**
	 * WordPress database
	 *
	 * @var \wpdb
	 */
	private $db;

	/**
	 * Entry database table name
	 *
	 * @var string
	 */
	private $table_name;

	/**
	 * Entry meta table name
	 *
	 * @var string
	 */
	private $meta_table_name;

	/**
	 * @var array
	 */
	private $entry = array(
		'id'           => 0,
		'form_id'      => 0,
		'user_id'      => 0,
		'user_ip'      => '127.0.0.1',
		'user_agent'   => null,
		'referer'      => null,
		'status'       => null,
		'created_at'   => null,
		'field_values' => array(),
	);

	/**
	 * @var array
	 */
	private $field_values = array();

	/**
	 * @var ContactForm
	 */
	private $form;

	/**
	 * Entry constructor.
	 *
	 * @param array $entry
	 */
	public function __construct( $entry = array() ) {
		global $wpdb;

		/*
         * Assign Database Tables using the DB prefix
         */
		$this->db              = $wpdb;
		$this->table_name      = $wpdb->prefix . 'dcf_entries';
		$this->meta_table_name = $wpdb->prefix . 'dcf_entry_meta';

		if ( $entry ) {
			$this->entry = $entry;
		}
	}

	/**
	 * Does this entry have a given key?
	 *
	 * @param string $key The data key
	 *
	 * @return bool
	 */
	public function has( $key ) {
		return isset( $this->entry[ $key ] );
	}

	/**
	 * Get entry item for key
	 *
	 * @param string $key The data key
	 * @param mixed $default The default value to return if data key does not exist
	 *
	 * @return mixed The key's value, or the default value
	 */
	public function get( $key, $default = null ) {
		return $this->has( $key ) ? $this->entry[ $key ] : $default;
	}

	/**
	 * Get entry id
	 *
	 * @return int
	 */
	public function getId() {
		return $this->get( 'id' );
	}

	/**
	 * Get form id
	 *
	 * @return int
	 */
	public function getFormId() {
		return $this->get( 'form_id' );
	}

	/**
	 * Get form class related to entry
	 *
	 * @return ContactForm
	 */
	public function getForm() {
		if ( ! $this->form instanceof ContactForm ) {
			$this->form = new ContactForm( $this->getFormId() );
		}

		return $this->form;
	}

	/**
	 * Get user id
	 *
	 * @return int
	 */
	public function getUserId() {
		return $this->get( 'user_id' );
	}

	/**
	 * Get user IP address
	 *
	 * @return int
	 */
	public function getUserIp() {
		return $this->get( 'user_ip' );
	}

	/**
	 * Get user agent
	 *
	 * @return string
	 */
	public function getUserAgent() {
		return $this->get( 'user_agent' );
	}

	/**
	 * Get form referer
	 *
	 * @return string
	 */
	public function getReferer() {
		return $this->get( 'referer' );
	}

	/**
	 * Get status
	 *
	 * @return string
	 */
	public function getStatus() {
		return $this->get( 'status' );
	}

	/**
	 * Get entry creation date
	 *
	 * @return mixed
	 */
	public function getCreatedAt() {
		return $this->get( 'created_at' );
	}

	/**
	 * Get field values
	 *
	 * @param string $key
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public function getFieldValues( $key = null, $default = null ) {
		$field_values = $this->get( 'field_values' );

		if ( ! is_array( $field_values ) ) {
			return array();
		}

		if ( empty( $key ) ) {
			return $field_values;
		}

		return isset( $field_values[ $key ] ) ? $field_values[ $key ] : $default;
	}

	/**
	 * Get entry data as array
	 *
	 * @return array
	 */
	public function toArray() {
		return array(
			'id'           => $this->getId(),
			'form_id'      => $this->getFormId(),
			'user_id'      => $this->getUserId(),
			'user_ip'      => $this->getUserIp(),
			'user_agent'   => $this->getUserAgent(),
			'referer'      => $this->getReferer(),
			'status'       => $this->getStatus(),
			'created_at'   => $this->getCreatedAt(),
			'field_values' => $this->getFieldValues(),
		);
	}

	/**
	 * Find entries
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public function find( $args = array() ) {
		$orderby  = isset( $args['orderby'] ) ? $args['orderby'] : 'created_at';
		$order    = isset( $args['order'] ) ? $args['order'] : 'desc';
		$offset   = isset( $args['offset'] ) ? intval( $args['offset'] ) : 0;
		$per_page = isset( $args['per_page'] ) ? intval( $args['per_page'] ) : 50;

		$sql = "SELECT * FROM {$this->table_name}";
		$sql .= " ORDER BY {$orderby} {$order}";
		$sql .= " LIMIT $per_page OFFSET $offset";

		$items = $this->db->get_results( $sql, ARRAY_A );

		if ( ! $items ) {
			return array();
		}

		$ids     = Utils::array_column( $items, 'id' );
		$ids     = implode( ',', $ids );
		$sql     = "SELECT * FROM {$this->meta_table_name} WHERE entry_id IN($ids)";
		$entries = $this->db->get_results( $sql, ARRAY_A );

		$_meta = array();
		foreach ( $entries as $entry ) {
			if ( empty( $entry['meta_key'] ) ) {
				continue;
			}
			$_meta[ $entry['entry_id'] ][ $entry['meta_key'] ] = $this->unserialize( $entry['meta_value'] );
		}

		$data = array();
		foreach ( $items as $item ) {
			$data[] = $item + array( 'field_values' => $_meta[ $item['id'] ] );
		}

		return $data;
	}

	/**
	 * Get entry by entry ID
	 *
	 * @param int $entry_id
	 *
	 * @return array
	 */
	public function findById( $entry_id ) {
		$items = $this->db->get_row( $this->db->prepare(
			"SELECT * FROM {$this->table_name} WHERE id = %d", $entry_id ),
			ARRAY_A
		);

		$entries = $this->db->get_results( $this->db->prepare(
			"SELECT * FROM {$this->meta_table_name} WHERE entry_id = %d", $entry_id ),
			ARRAY_A
		);

		$_meta = array();
		foreach ( $entries as $entry ) {
			if ( empty( $entry['meta_key'] ) ) {
				continue;
			}
			$_meta[ $entry['meta_key'] ] = $this->unserialize( $entry['meta_value'] );
		}

		$_entries = $items + array( 'field_values' => $_meta );

		return $_entries;
	}

	/**
	 * Insert a row into a entries table with meta values.
	 *
	 * @param array $data Form data in $key => 'value' format
	 * array( $key1 => 'value 1', $key2 => 'value 2' )
	 *
	 * @return int last insert id
	 */
	public function insert( $data ) {
		$current_time = current_time( 'mysql' );
		$this->db->insert( $this->table_name, $this->getMetaInfo( $current_time ) );

		$insert_id = $this->db->insert_id;
		if ( $insert_id ) {

			$_data = array();
			foreach ( $data as $key => $value ) {
				$_data[] = array(
					'entry_id'   => $insert_id,
					'meta_key'   => $key,
					'meta_value' => $this->serialize( $value ),
				);
			}

			$this->insertMultipleRows( $this->meta_table_name, $_data );
		}

		return $insert_id;
	}

	/**
	 * @param array $data
	 * @param array $where
	 * @param string|array $format
	 * @param string|array $where_format
	 */
	public function update( $data, $where, $format = null, $where_format = null ) {
		$this->db->update( $this->table_name, $data, $where, $format, $where_format );
	}

	/**
	 * Delete entry
	 *
	 * @param int $entry_id
	 *
	 * @return bool
	 */
	public function delete( $entry_id = 0 ) {
		$result = $this->db->delete( $this->table_name, array( 'id' => $entry_id ), '%d' );

		if ( false === $result ) {
			return false;
		}
		$this->db->delete( $this->meta_table_name, array( 'entry_id' => $entry_id ), '%d' );

		return true;
	}

	/**
	 * Get form meta information
	 *
	 * @param null $current_time
	 *
	 * @return array
	 */
	private function getMetaInfo( $current_time = null ) {
		if ( ! $current_time ) {
			$current_time = current_time( 'mysql' );
		}

		return array(
			'form_id'    => Utils::get_form_id(),
			'user_id'    => get_current_user_id(),
			'user_ip'    => Utils::get_remote_ip(),
			'user_agent' => Utils::get_user_agent(),
			'referer'    => Utils::get_referer(),
			'status'     => 'unread',
			'created_at' => $current_time,
		);
	}

	/**
	 * Serialize data, if needed.
	 *
	 * @param string|array|object $data Data that might be serialized.
	 *
	 * @return mixed
	 */
	private function serialize( $data ) {
		if ( is_array( $data ) || is_object( $data ) ) {
			return serialize( $data );
		}

		return $data;
	}

	/**
	 * Unserialize value only if it was serialized.
	 *
	 * @param string $original Maybe unserialized original, if is needed.
	 *
	 * @return mixed Unserialized data can be any type.
	 */
	private function unserialize( $original ) {
		// don't attempt to unserialize data that wasn't serialized going in
		if ( is_serialized( $original ) ) {
			return @unserialize( $original );
		}

		return $original;
	}

	/**
	 *  A method for inserting multiple rows into the specified table
	 *  Updated to include the ability to Update existing rows by primary key
	 *
	 *  Usage Example for insert:
	 *
	 *  $insert_arrays = array();
	 *  foreach($assets as $asset) {
	 *  $time = current_time( 'mysql' );
	 *  $insert_arrays[] = array(
	 *  'type' => "multiple_row_insert",
	 *  'status' => 1,
	 *  'name'=>$asset,
	 *  'added_date' => $time,
	 *  'last_update' => $time);
	 *
	 *  }
	 *
	 *
	 *  $this->_insert_rows( $table_name, $data );
	 *
	 *  Usage Example for update:
	 *
	 *  $this->_insert_rows( $table_name, $data, true, "primary_column" );
	 *
	 *
	 * @param string $table_name
	 * @param array $data
	 * @param boolean $update
	 * @param string $primary_key
	 *
	 * @return false|int
	 */
	private function insertMultipleRows( $table_name, $data = array(), $update = false, $primary_key = null ) {
		global $wpdb;
		$table_name = esc_sql( $table_name );
		// Setup arrays for Actual Values, and Placeholders
		$values        = array();
		$place_holders = array();
		$query         = "";
		$query_columns = "";

		$query .= "INSERT INTO `{$table_name}` (";
		foreach ( $data as $count => $row_array ) {
			foreach ( $row_array as $key => $value ) {
				if ( $count == 0 ) {
					if ( $query_columns ) {
						$query_columns .= ", " . $key . "";
					} else {
						$query_columns .= "" . $key . "";
					}
				}

				$values[] = $value;

				$symbol = "%s";
				if ( is_numeric( $value ) ) {
					if ( is_float( $value ) ) {
						$symbol = "%f";
					} else {
						$symbol = "%d";
					}
				}
				if ( isset( $place_holders[ $count ] ) ) {
					$place_holders[ $count ] .= ", '$symbol'";
				} else {
					$place_holders[ $count ] = "( '$symbol'";
				}
			}
			// mind closing the GAP
			$place_holders[ $count ] .= ")";
		}

		$query .= " $query_columns ) VALUES ";

		$query .= implode( ', ', $place_holders );

		if ( $update ) {
			$update = " ON DUPLICATE KEY UPDATE $primary_key=VALUES( $primary_key ),";
			$cnt    = 0;
			foreach ( $data[0] as $key => $value ) {
				if ( $cnt == 0 ) {
					$update .= "$key=VALUES($key)";
					$cnt    = 1;
				} else {
					$update .= ", $key=VALUES($key)";
				}
			}
			$query .= $update;
		}

		$sql = $wpdb->prepare( $query, $values );
		if ( $wpdb->query( $sql ) ) {
			return true;
		} else {
			return false;
		}
	}
}
