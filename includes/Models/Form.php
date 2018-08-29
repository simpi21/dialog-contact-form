<?php

namespace DialogContactForm\Models;

class Form extends Model {

	/**
	 * Count number of forms.
	 *
	 * @param string $perm Optional. 'readable' or empty. Default empty.
	 *
	 * @return array
	 */
	public static function totalCount( $perm = 'empty' ) {
		global $wpdb;
		$type = DIALOG_CONTACT_FORM_POST_TYPE;

		$cache_key = _count_posts_cache_key( $type, $perm );

		$counts = wp_cache_get( $cache_key, 'counts' );
		if ( false !== $counts ) {
			return $counts;
		}

		$query = "SELECT post_status, COUNT( * ) AS num_posts FROM {$wpdb->posts} WHERE post_type = %s";
		if ( 'readable' == $perm && is_user_logged_in() ) {
			$post_type_object = get_post_type_object( $type );
			if ( ! current_user_can( $post_type_object->cap->read_private_posts ) ) {
				$query .= $wpdb->prepare(
					" AND (post_status != 'private' OR ( post_author = %d AND post_status = 'private' ))",
					get_current_user_id()
				);
			}
		}
		$query .= ' GROUP BY post_status';

		$results = (array) $wpdb->get_results( $wpdb->prepare( $query, $type ), ARRAY_A );
		$counts  = array_fill_keys( get_post_stati(), 0 );

		foreach ( $results as $row ) {
			$counts[ $row['post_status'] ] = $row['num_posts'];
		}

		wp_cache_set( $cache_key, $counts, 'counts' );

		return $counts;
	}
}
