<?php

namespace DialogContactForm\Entries;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Entry implements \JsonSerializable {

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
	private $table_name = 'dcf_entries';

	/**
	 * Entry meta table name
	 *
	 * @var string
	 */
	private $meta_table_name = 'dcf_entry_meta';

	private $entries = array();

	/**
	 * Entry constructor.
	 *
	 * @param array $entries
	 */
	public function __construct( $entries = array() ) {
		global $wpdb;

		/*
         * Assign Database Tables using the DB prefix
         */
		$this->db              = $wpdb;
		$this->table_name      = $wpdb->prefix . $this->table_name;
		$this->meta_table_name = $wpdb->prefix . $this->meta_table_name;

		if ( $entries && is_object( $entries ) ) {
			$this->entries = $entries;
		}
	}

	/**
	 * Get the string representation of the current element.
	 *
	 * @return string
	 */
	public function __toString() {
		return json_encode( $this->entries );
	}

	/**
	 * Get entries
	 *
	 * @param $args
	 *
	 * @return null|object
	 */
	public function get_entries( $args ) {
		$orderby  = isset( $args['orderby'] ) ? $args['orderby'] : 'id';
		$order    = isset( $args['order'] ) ? $args['order'] : 'desc';
		$offset   = isset( $args['offset'] ) ? intval( $args['offset'] ) : 0;
		$per_page = isset( $args['per_page'] ) ? intval( $args['per_page'] ) : 50;

		$items = $this->db->get_results( "
                SELECT * FROM $this->table_name
                ORDER BY $orderby $order
                LIMIT $per_page
                OFFSET $offset
            ", OBJECT );

		return $items;
	}

	/**
	 * Get entry by entry ID
	 *
	 * @param int $entry_id
	 *
	 * @return array
	 */
	public function get( $entry_id ) {
		$items = $this->db->get_row( $this->db->prepare(
			"SELECT * FROM $this->table_name WHERE id = %d", $entry_id ),
			ARRAY_A
		);

		$entries = $this->db->get_results( $this->db->prepare(
			"SELECT * FROM $this->meta_table_name WHERE entry_id = %d", $entry_id ),
			ARRAY_A
		);

		$_entries = array(
			'meta_data' => $items,
		);
		foreach ( $entries as $entry ) {
			$_entries[ $entry['meta_key'] ] = $this->unserialize( $entry['meta_value'] );
		}

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
		$this->db->insert( $this->table_name, $this->get_meta_info( $current_time ) );

		$insert_id = $this->db->insert_id;
		if ( $insert_id ) {

			// If attachment field is empty, do not record it
			if ( empty( $data['dcf_attachments'] ) ) {
				unset( $data['dcf_attachments'] );
			}

			$_data = array();

			foreach ( $data as $key => $value ) {
				$_data[] = array(
					'entry_id'   => $insert_id,
					'meta_key'   => $key,
					'meta_value' => $this->serialize( $value ),
				);
			}

			$this->_insert_rows( $this->meta_table_name, $_data );
		}

		return $insert_id;
	}

	public function update( $data, $where, $format = null, $where_format = null ) {
		$this->db->update( $this->table_name, $data, $where, $format, $where_format );
	}

	public function delete( $where, $where_format = null ) {
		$this->db->delete( $this->table_name, $where, $where_format );
	}

	/**
	 * Get form meta information
	 *
	 * @param null $current_time
	 *
	 * @return array
	 */
	private function get_meta_info( $current_time = null ) {
		if ( ! $current_time ) {
			$current_time = current_time( 'mysql' );
		}

		return array(
			'form_id'    => $this->get_form_id(),
			'user_id'    => get_current_user_id(),
			'user_ip'    => $this->get_remote_ip(),
			'user_agent' => $this->get_user_agent(),
			'referer'    => $this->get_referer(),
			'status'     => 'unread',
			'created_at' => $current_time,
		);
	}

	/**
	 * Get user IP address
	 *
	 * @return string
	 */
	private function get_remote_ip() {
		$server_ip_keys = array(
			'HTTP_CLIENT_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_FORWARDED',
			'HTTP_X_CLUSTER_CLIENT_IP',
			'HTTP_FORWARDED_FOR',
			'HTTP_FORWARDED',
			'REMOTE_ADDR',
		);

		foreach ( $server_ip_keys as $key ) {
			if ( isset( $_SERVER[ $key ] ) && filter_var( $_SERVER[ $key ], FILTER_VALIDATE_IP ) ) {
				return $_SERVER[ $key ];
			}
		}

		// Fallback local ip.
		return '127.0.0.1';
	}

	/**
	 * Get user browser name
	 *
	 * @return string
	 */
	private function get_user_agent() {
		if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
			return substr( $_SERVER['HTTP_USER_AGENT'], 0, 254 );
		}

		return '';
	}

	/**
	 * Get the form id
	 *
	 * @return int
	 */
	private function get_form_id() {
		if ( isset( $_POST['_dcf_id'] ) && is_numeric( $_POST['_dcf_id'] ) ) {
			return intval( $_POST['_dcf_id'] );
		}

		return 0;
	}

	/**
	 * Get form referer
	 *
	 * @return string
	 */
	private function get_referer() {
		if ( isset( $_POST['_dcf_referer'] ) && is_string( $_POST['_dcf_referer'] ) ) {
			return sanitize_text_field( $_POST['_dcf_referer'] );
		}

		return '';
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
	private function _insert_rows( $table_name, $data = array(), $update = false, $primary_key = null ) {
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
	 * Specify data which should be serialized to JSON
	 * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
	 * @return mixed data which can be serialized by <b>json_encode</b>,
	 * which is a value of any type other than a resource.
	 * @since 5.4.0
	 */
	public function jsonSerialize() {
		return $this->entries;
	}
}