<?php

namespace DialogContactForm\Models;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Form extends Model {

    const POST_TYPE = 'dialog-contact-form';

    /**
     * @var int
     */
    protected static $found_items = 0;

    /**
     * Form constructor.
     *
     * @param array $form
     */
    public function __construct( $form = array() ) {
        if ( $form ) {
            $this->collections = $form;
        }
    }

    /**
     * Retrieves a collection of items.
     *
     * @param array|int $args
     *
     * @return array|Form
     */
    public static function find( $args = array() ) {
        if ( is_numeric( $args ) ) {
            return self::first( $args );
        }

        $defaults = array(
            'post_status'    => 'any',
            'posts_per_page' => 100,
            'offset'         => 0,
            'orderby'        => 'ID',
            'order'          => 'DESC',
        );

        $args = wp_parse_args( $args, $defaults );

        $args['post_type'] = self::POST_TYPE;

        $q     = new \WP_Query();
        $posts = $q->query( $args );

        self::$found_items = $q->found_posts;

        $forms = array();

        foreach ( (array) $posts as $post ) {
            $forms[] = new self( $post );
        }

        return $forms;
    }

    /**
     * Retrieves one item from the collection.
     *
     * @param int $id
     *
     * @return bool|Form
     */
    public static function first( $id = 0 ) {
        $_post = get_post( intval( $id ) );
        if ( $_post instanceof \WP_Post && self::POST_TYPE == get_post_type( $_post ) ) {
            return new self( $_post );
        }

        return false;
    }

    public static function create( array $data ) {

    }

    public static function update( array $data ) {

    }

    /**
     * Trash or delete a form.
     *
     * @param int $id Post ID.
     * @param bool $force_delete Whether to bypass trash and force deletion.
     *
     * @return bool true on success, false on failure.
     */
    public static function delete( $id = 0, $force_delete = false ) {
        if ( wp_delete_post( $id, $force_delete ) ) {
            return true;
        }

        return false;
    }

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

        $query = $wpdb->prepare(
            "SELECT post_status, COUNT( * ) AS num_posts FROM {$wpdb->posts} WHERE post_type = %s",
            $type
        );
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

        $results = (array) $wpdb->get_results( $query, ARRAY_A );
        $counts  = array_fill_keys( get_post_stati(), 0 );

        foreach ( $results as $row ) {
            $counts[ $row['post_status'] ] = $row['num_posts'];
        }

        wp_cache_set( $cache_key, $counts, 'counts' );

        return $counts;
    }
}
