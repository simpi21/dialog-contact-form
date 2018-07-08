<?php

namespace DialogContactForm\Providers;

use DialogContactForm\Supports\RestClient;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class GetResponseProvider {

	/**
	 * @var RestClient
	 */
	public $rest_client = null;

	private $api_key = '';

	/**
	 * GetResponseProvider constructor.
	 *
	 * @param $api_key
	 *
	 * @throws \Exception
	 */
	public function __construct( $api_key ) {
		if ( empty( $api_key ) ) {
			throw new \Exception( 'Invalid API key' );
		}

		$this->init_rest_client( $api_key );

		if ( ! $this->is_valid_api_key() ) {
			throw new \Exception( 'Invalid API key' );
		}
	}

	/**
	 * @param $api_key
	 */
	private function init_rest_client( $api_key ) {
		$this->api_key     = $api_key;
		$this->rest_client = new RestClient( 'https://api.getresponse.com/v3/' );
		$this->rest_client->addHeaders( array(
			'X-Auth-Token' => 'api-key ' . $api_key,
			'Content-Type' => 'application/json',
		) );
	}

	/**
	 * validate api key
	 *
	 * @return bool
	 * @throws \Exception
	 */
	private function is_valid_api_key() {
		$lists = $this->get_lists();
		if ( ! empty( $lists ) ) {
			return true;
		}
		$this->api_key = '';

		return false;
	}

	/**
	 * get GetResponse lists associated with API key
	 * @return array
	 * @throws \Exception
	 */
	public function get_lists() {
		$results = $this->rest_client->get( 'campaigns' );

		$lists = array(
			'' => __( 'Select...', 'elementor-pro' ),
		);

		if ( ! empty( $results['body'] ) ) {
			foreach ( $results['body'] as $index => $list ) {
				if ( ! is_array( $list ) ) {
					continue;
				}
				$lists[ $list['campaignId'] ] = $list['name'];
			}
		}

		$return_array = array(
			'lists' => $lists,
		);

		return $return_array;
	}

	/**
	 * @return array
	 * @throws \Exception
	 */
	public function get_fields() {
		$results = $this->rest_client->get( 'custom-fields' );

		$fields = array(
			array(
				'remote_label'    => __( 'Email', 'elementor-pro' ),
				'remote_type'     => 'email',
				'remote_id'       => 'email',
				'remote_required' => true,
			),
			array(
				'remote_label'    => __( 'Name', 'elementor-pro' ),
				'remote_type'     => 'text',
				'remote_id'       => 'name',
				'remote_required' => false,
			),
		);

		if ( ! empty( $results['body'] ) ) {
			foreach ( $results['body'] as $field ) {
				$fields[] = array(
					'remote_label'    => $field['name'],
					'remote_type'     => $this->normalize_type( $field['type'] ),
					'remote_id'       => $field['customFieldId'],
					'remote_required' => false,
				);
			}
		}

		$return_array = array(
			'fields' => $fields,
		);

		return $return_array;
	}

	private function normalize_type( $type ) {
		static $types = array(
			'text'          => 'text',
			'number'        => 'number',
			'address'       => 'text',
			'phone'         => 'text',
			'date'          => 'text',
			'url'           => 'url',
			'imageurl'      => 'url',
			'radio'         => 'radio',
			'dropdown'      => 'select',
			'single_select' => 'select',
			'textarea'      => 'text',
			'birthday'      => 'text',
			'zip'           => 'text',
			'country'       => 'text',
			'gender'        => 'text',
		);

		return $types[ $type ];
	}

	/**
	 * create contact at GetResponse via api
	 *
	 * @param array $subscriber_data
	 *
	 * @return array|mixed
	 * @throws \Exception
	 */
	public function create_subscriber( $subscriber_data = array() ) {
		return $this->rest_client->request( 'POST', 'contacts', wp_json_encode( $subscriber_data ), 202 );
	}
}
