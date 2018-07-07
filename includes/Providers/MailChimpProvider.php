<?php

namespace DialogContactForm\Providers;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MailChimpProvider {

	/**
	 * API base URL
	 *
	 * @var string
	 */
	private $api_base_url = '';

	/**
	 * MailChimp API Key
	 *
	 * @var string
	 */
	private $api_key = '';

	/**
	 * API request args
	 *
	 * @var array
	 */
	private $api_request_args = array();

	/**
	 * MailChimpHandler constructor.
	 *
	 * @param string $api_key
	 *
	 * @throws \Exception
	 */
	public function __construct( $api_key ) {
		if ( empty( $api_key ) ) {
			throw new \Exception( 'Invalid API key' );
		}

		// The API key is in format XXXXXXXXXXXXXXXXXXXX-us2 where us2 is the server sub domain for this account
		$key_parts = explode( '-', $api_key );
		if ( empty( $key_parts[1] ) || 0 !== strpos( $key_parts[1], 'us' ) ) {
			throw new \Exception( 'Invalid API key' );
		}

		$this->api_key          = $api_key;
		$this->api_base_url     = 'https://' . $key_parts[1] . '.api.mailchimp.com/3.0/';
		$this->api_request_args = array(
			'headers' => array(
				'Authorization' => 'Basic ' . base64_encode( 'user:' . $this->api_key ),
			),
		);
	}

	/**
	 * Build query
	 *
	 * @param $end_point
	 *
	 * @return array|mixed|object
	 * @throws \Exception
	 */
	private function query( $end_point ) {
		$response = wp_remote_get( $this->api_base_url . $end_point, $this->api_request_args );

		if ( is_wp_error( $response ) || 200 != (int) wp_remote_retrieve_response_code( $response ) ) {
			throw new \Exception( 'Mailchimp Error' );
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! is_array( $body ) ) {
			throw new \Exception( 'Mailchimp Error' );
		}

		return $body;
	}

	/**
	 * Send post request
	 *
	 * @param string $end_point
	 * @param mixed $data
	 * @param array $request_args
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function post( $end_point, $data, $request_args = array() ) {
		$this->api_request_args                            += $request_args;
		$this->api_request_args['headers']['Content-Type'] = 'application/json; charset=utf-8';
		$this->api_request_args['body']                    = wp_json_encode( $data );
		$response                                          = wp_remote_post( $this->api_base_url . $end_point, $this->api_request_args );

		if ( is_wp_error( $response ) ) {
			throw new \Exception( 'Mailchimp Error' );
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! is_array( $body ) ) {
			throw new \Exception( 'Mailchimp Error' );
		}

		return array(
			'code' => (int) wp_remote_retrieve_response_code( $response ),
			'body' => $body,
		);
	}

	/**
	 * Get lists
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function get_lists() {
		$results = $this->query( 'lists?count=999' );

		$lists = array(
			'' => 'Select...',
		);

		if ( ! empty( $results['lists'] ) ) {
			foreach ( $results['lists'] as $list ) {
				$lists[ $list['id'] ] = $list['name'];
			}
		}

		$return_array = array(
			'lists' => $lists,
		);

		return $return_array;
	}

	/**
	 * Get groups
	 *
	 * @param string $list_id
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function get_groups( $list_id ) {
		$results = $this->query( 'lists/' . $list_id . '/interest-categories?count=999' );
		$groups  = array();

		if ( ! empty( $results['categories'] ) ) {
			foreach ( $results['categories'] as $category ) {
				$interests_results = $this->query( 'lists/' . $list_id . '/interest-categories/' . $category['id'] . '/interests?count=999' );

				foreach ( $interests_results['interests'] as $interest ) {
					$groups[ $interest['id'] ] = $category['title'] . ' - ' . $interest['name'];
				}
			}
		}

		$return_array = array(
			'groups' => $groups,
		);

		return $return_array;
	}

	/**
	 * Get fields
	 *
	 * @param $list_id
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function get_fields( $list_id ) {
		$results = $this->query( 'lists/' . $list_id . '/merge-fields?count=999' );

		$fields = array(
			array(
				'remote_label'    => 'Email',
				'remote_type'     => 'email',
				'remote_id'       => 'email',
				'remote_required' => true,
			),
		);

		if ( ! empty( $results['merge_fields'] ) ) {
			foreach ( $results['merge_fields'] as $field ) {
				$fields[] = array(
					'remote_label'    => $field['name'],
					'remote_type'     => $this->normalize_type( $field['type'] ),
					'remote_id'       => $field['tag'],
					'remote_required' => $field['required'],
				);
			}
		}

		$return_array = array(
			'fields' => $fields,
		);

		return $return_array;
	}

	/**
	 * Get list details
	 *
	 * @param $list_id
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function get_list_details( $list_id ) {
		$groups = $this->get_groups( $list_id );
		$fields = $this->get_fields( $list_id );

		return array(
			'list_details' => $groups + $fields,
		);
	}

	/**
	 * Normalize type
	 *
	 * @param $type
	 *
	 * @return mixed
	 */
	private function normalize_type( $type ) {
		static $types = array(
			'text'     => 'text',
			'number'   => 'number',
			'address'  => 'text',
			'phone'    => 'text',
			'date'     => 'text',
			'url'      => 'url',
			'imageurl' => 'url',
			'radio'    => 'radio',
			'dropdown' => 'select',
			'birthday' => 'text',
			'zip'      => 'text',
		);

		return $types[ $type ];
	}
}
