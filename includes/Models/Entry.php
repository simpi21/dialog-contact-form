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
}
