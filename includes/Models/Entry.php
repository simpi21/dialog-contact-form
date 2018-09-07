<?php

namespace DialogContactForm\Models;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
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
	 * Count number of entries.
	 *
	 * @return array
	 */
	public static function totalCount() {
		global $wpdb;
		$table = $wpdb->prefix . self::$table_name;

		$query   = "SELECT form_id, COUNT( * ) AS num_entries";
		$query   .= " FROM {$table} WHERE status != 'trash' GROUP BY form_id";
		$results = $wpdb->get_results( $query, ARRAY_A );

		$counts = array();
		foreach ( $results as $row ) {
			$counts[ $row['form_id'] ] = intval( $row['num_entries'] );
		}

		return $counts;
	}

	/**
	 * Count entries by status and form id
	 *
	 * @return array
	 */
	public static function statusCount() {
		global $wpdb;
		$table = $wpdb->prefix . self::$table_name;

		$query   = "SELECT status, form_id, COUNT( * ) AS num_entries";
		$query   .= " FROM {$table} GROUP BY status, form_id";
		$results = $wpdb->get_results( $query, ARRAY_A );

		$counts = array();
		foreach ( $results as $row ) {
			$counts[ $row['form_id'] ][ $row['status'] ] = intval( $row['num_entries'] );
		}

		$status = [];
		foreach ( $counts as $form_id => $count ) {

			$default_count = array( 'unread' => 0, 'read' => 0, 'trash' => 0, );
			$count         = wp_parse_args( $count, $default_count );

			$status[ $form_id ] = [
				'form_title' => get_the_title( $form_id ),
				'form_id'    => $form_id,
				'status'     => $count,
			];
		}

		return $status;
	}
}
