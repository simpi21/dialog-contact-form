<?php

namespace DialogContactForm\Entries;

use DialogContactForm\Models\Model;
use DialogContactForm\Supports\ContactForm;
use DialogContactForm\Supports\Utils;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Entry extends Model {

	/**
	 * Entry database table name
	 *
	 * @var string
	 */
	private static $table_name = 'dcf_entries';

	/**
	 * Entry meta table name
	 *
	 * @var string
	 */
	private static $meta_table_name = 'dcf_entry_meta';

	/**
	 * @var array
	 */
	private static $entry = array(
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
	 * @var ContactForm
	 */
	private $form;

	/**
	 * Entry constructor.
	 *
	 * @param array $entry
	 */
	public function __construct( $entry = null ) {
		if ( ! is_null( $entry ) ) {
			$this->collections = $entry;
		}
	}

	/**
	 * Get entry id
	 *
	 * @return int
	 */
	public function getId() {
		return (int) $this->get( 'id' );
	}

	/**
	 * Get form id
	 *
	 * @return int
	 */
	public function getFormId() {
		return (int) $this->get( 'form_id' );
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
		return (int) $this->get( 'user_id' );
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
		return site_url( $this->get( 'referer' ) );
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
	 * @return \DateTime
	 */
	public function getCreatedAt() {
		return self::formatDate( $this->get( 'created_at' ) );
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
	 * @param $value
	 *
	 * @return string
	 */
	public function formatFieldValue( $value ) {
		if ( empty( $value ) ) {
			return '';
		}
		if ( is_string( $value ) ) {
			return wpautop( $value );
		}

		if ( is_numeric( $value ) ) {
			if ( is_float( $value ) ) {
				return floatval( $value );
			}

			return intval( $value );
		}

		if ( is_object( $value ) ) {
			$value = json_decode( json_encode( $value ), true );
		}

		if ( is_array( $value ) ) {
			foreach ( $value as $v_key => $v_value ) {
				if ( is_string( $v_value ) ) {
					return wpautop( $v_value );
				}

				if ( is_array( $v_value ) ) {
					if ( isset( $v_value['attachment_id'] ) && is_numeric( $v_value['attachment_id'] ) ) {
						$url  = wp_get_attachment_url( $v_value['attachment_id'] );
						$html = '<a href="' . $url . '" target="_blank">';
						$html .= wp_get_attachment_image( $v_value['attachment_id'] );
						$html .= '</a>';

						return $html;
					}

					return implode( '<br>', $v_value );
				}
			}
		}

		return '';
	}

	/**
	 * Get entry data as array
	 *
	 * @return array
	 */
	public function all() {
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
	public static function find( $args = array() ) {
		global $wpdb;
		$table_name      = $wpdb->prefix . self::$table_name;
		$meta_table_name = $wpdb->prefix . self::$meta_table_name;

		$orderby  = isset( $args['orderby'] ) ? $args['orderby'] : 'created_at';
		$order    = isset( $args['order'] ) ? $args['order'] : 'desc';
		$offset   = isset( $args['offset'] ) ? intval( $args['offset'] ) : 0;
		$per_page = isset( $args['per_page'] ) ? intval( $args['per_page'] ) : 50;

		$sql = "SELECT * FROM {$table_name}";
		$sql .= " ORDER BY {$orderby} {$order}";
		$sql .= " LIMIT $per_page OFFSET $offset";

		$items = $wpdb->get_results( $sql, ARRAY_A );

		if ( ! $items ) {
			return array();
		}

		$ids     = Utils::array_column( $items, 'id' );
		$ids     = implode( ',', $ids );
		$sql     = "SELECT * FROM {$meta_table_name} WHERE entry_id IN($ids)";
		$entries = $wpdb->get_results( $sql, ARRAY_A );

		$_meta = array();
		foreach ( $entries as $entry ) {
			if ( empty( $entry['meta_key'] ) ) {
				continue;
			}
			$_meta[ $entry['entry_id'] ][ $entry['meta_key'] ] = self::unserialize( $entry['meta_value'] );
		}

		$data = array();
		foreach ( $items as $item ) {
			$_data  = $item + array( 'field_values' => $_meta[ $item['id'] ] );
			$data[] = new self( $_data );
		}

		return $data;
	}

	/**
	 * Get entry by entry ID
	 *
	 * @param int $entry_id
	 *
	 * @return Entry|false
	 */
	public static function findById( $entry_id ) {
		global $wpdb;
		$table_name      = $wpdb->prefix . self::$table_name;
		$meta_table_name = $wpdb->prefix . self::$meta_table_name;

		$items = $wpdb->get_row( $wpdb->prepare(
			"SELECT * FROM {$table_name} WHERE id = %d", $entry_id ),
			ARRAY_A
		);

		$entries = $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM {$meta_table_name} WHERE entry_id = %d", $entry_id ),
			ARRAY_A
		);

		if ( ! $items ) {
			return false;
		}

		$_meta = array();
		foreach ( $entries as $entry ) {
			if ( empty( $entry['meta_key'] ) ) {
				continue;
			}
			$_meta[ $entry['meta_key'] ] = self::unserialize( $entry['meta_value'] );
		}

		$_entries = $items + array( 'field_values' => $_meta );

		return new self( $_entries );
	}

	/**
	 * Insert a row into a entries table with meta values.
	 *
	 * @param array $data Form data in $key => 'value' format
	 * array( $key1 => 'value 1', $key2 => 'value 2' )
	 *
	 * @return int last insert id
	 */
	public static function insert( $data ) {
		global $wpdb;
		$table_name      = $wpdb->prefix . self::$table_name;
		$meta_table_name = $wpdb->prefix . self::$meta_table_name;

		$current_time = current_time( 'mysql' );
		$wpdb->insert( $table_name, self::getMetaInfo( $current_time ) );

		$insert_id = $wpdb->insert_id;
		if ( $insert_id ) {

			$_data = array();
			foreach ( $data as $key => $value ) {
				$_data[] = array(
					'entry_id'   => $insert_id,
					'meta_key'   => $key,
					'meta_value' => self::serialize( $value ),
				);
			}

			self::insertMultipleRows( $meta_table_name, $_data );
		}

		return $insert_id;
	}

	/**
	 * @param array $data
	 * @param array $where
	 * @param string|array $format
	 * @param string|array $where_format
	 *
	 * @return bool
	 */
	public static function update( $data, $where, $format = null, $where_format = null ) {
		global $wpdb;
		$table_name = $wpdb->prefix . self::$table_name;

		$result = $wpdb->update( $table_name, $data, $where, $format, $where_format );

		return ( false !== $result );
	}

	/**
	 * Delete entry
	 *
	 * @param int $entry_id
	 *
	 * @return bool
	 */
	public static function delete( $entry_id = 0 ) {
		global $wpdb;
		$table_name      = $wpdb->prefix . self::$table_name;
		$meta_table_name = $wpdb->prefix . self::$meta_table_name;

		$result = $wpdb->delete( $table_name, array( 'id' => $entry_id ), '%d' );

		if ( false === $result ) {
			return false;
		}
		$wpdb->delete( $meta_table_name, array( 'entry_id' => $entry_id ), '%d' );

		return true;
	}

	/**
	 * Get form meta information
	 *
	 * @param null $current_time
	 *
	 * @return array
	 */
	private static function getMetaInfo( $current_time = null ) {
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
}
