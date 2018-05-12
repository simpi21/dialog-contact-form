<?php

namespace DialogContactForm\Actions;

use DialogContactForm\Abstracts\Abstract_Action;

class StoreSubmission extends Abstract_Action {

	/**
	 * Comment status
	 *
	 * @var string
	 */
	private static $comment_status = 'closed';

	/**
	 * Ping status
	 *
	 * @var string
	 */
	private static $ping_status = 'closed';

	/**
	 * Redirect constructor.
	 */
	public function __construct() {
		$this->id       = 'store_submission';
		$this->title    = __( 'Store Submission', 'dialog-contact-form' );
		$this->settings = $this->settings();
	}

	/**
	 * Save action settings
	 *
	 * @param int $post_id
	 * @param \WP_Post $post
	 */
	public function save( $post_id, $post ) {
		// TODO: Implement save() method.
	}

	/**
	 * Process action
	 *
	 * @param int $form_id
	 * @param array $data
	 *
	 * @return bool
	 */
	public static function process( $form_id, $data ) {
		/** @var \wpdb $wpdb */
		global $wpdb;

		$current_time     = current_time( 'mysql' );
		$current_time_gmt = get_gmt_from_date( $current_time );

		$wpdb->insert( $wpdb->posts, array(
			'post_author'           => get_current_user_id(),
			'post_date'             => $current_time,
			'post_date_gmt'         => $current_time_gmt,
			'post_content'          => maybe_serialize( $data ),
			'post_title'            => 'Form #' . $form_id,
			'post_excerpt'          => '',
			'post_status'           => 'publish',
			'comment_status'        => self::$comment_status,
			'ping_status'           => self::$ping_status,
			'post_password'         => '',
			'post_name'             => '',
			'to_ping'               => '',
			'pinged'                => '',
			'post_modified'         => $current_time,
			'post_modified_gmt'     => $current_time_gmt,
			'post_content_filtered' => '',
			'post_parent'           => 0,
			'guid'                  => '',
			'menu_order'            => 0,
			'post_type'             => 'dcf_entry',
			'post_mime_type'        => '',
			'comment_count'         => 0,
		) );

		$record_id = $wpdb->insert_id;

		return $record_id;
	}

	private function settings() {
		return array();
	}
}