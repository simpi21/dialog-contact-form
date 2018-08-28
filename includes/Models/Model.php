<?php

namespace DialogContactForm\Models;

use DialogContactForm\Supports\Collection;

class Model extends Collection {

	/**
	 * MYSQL Database date format
	 *
	 * @var string
	 */
	private static $mysql_date_format = 'Y-m-d';

	/**
	 * MYSQL Database datetime format
	 *
	 * @var string
	 */
	private static $mysql_datetime_format = 'Y-m-d H:i:s';

	/**
	 * Format date string
	 *
	 * @param string|\DateTime $date
	 * @param string $type
	 *
	 * @return \DateTime|int|string
	 */
	public static function formatDate( $date, $type = 'raw' ) {
		if ( ! $date instanceof \DateTime ) {
			$date = new \DateTime( $date );

			$timezone = get_option( 'timezone_string' );
			if ( in_array( $timezone, \DateTimeZone::listIdentifiers() ) ) {
				$date->setTimezone( new \DateTimeZone( $timezone ) );
			}
		}

		if ( 'mysql' == $type ) {
			return $date->format( self::$mysql_datetime_format );
		}

		if ( 'timestamp' == $type ) {
			return $date->getTimestamp();
		}

		if ( 'view' == $type ) {
			$date_format = get_option( 'date_format' );

			return $date->format( $date_format );
		}

		if ( ! in_array( $type, [ 'raw', 'mysql', 'timestamp', 'view' ] ) ) {
			return $date->format( $type );
		}

		return $date;
	}

	/**
	 * Serialize data, if needed.
	 *
	 * @param string|array|object $data Data that might be serialized.
	 *
	 * @return mixed
	 */
	public static function serialize( $data ) {
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
	public static function unserialize( $original ) {
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
	public static function insertMultipleRows( $table_name, $data = array(), $update = false, $primary_key = null ) {
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
