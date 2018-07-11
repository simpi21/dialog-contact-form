<?php

namespace DialogContactForm\Supports;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RestClient {

	/**
	 * @var string
	 */
	private $api_base_url = '';

	/**
	 * @var string
	 */
	private $user_agent = 'Dialog Contact Form (WordPress plugin)';

	/**
	 * @var array
	 */
	public $request_cache = array();

	/**
	 * @var array
	 */
	private $headers = array();

	/**
	 * @var array
	 */
	private $request_args = array();

	/**
	 * RestClient constructor.
	 *
	 * @param $rest_base_url
	 */
	public function __construct( $rest_base_url ) {
		$this->api_base_url = $rest_base_url;
		//setup defaults
		$this->setRequestArg( 'timeout', 30 )
		     ->setRequestArg( 'sslverify', false )
		     ->addHeaders( 'User-Agent', $this->user_agent );

		return $this;
	}

	/**
	 * @param $key
	 * @param $value
	 *
	 * @return $this
	 */
	public function addHeaders( $key, $value = null ) {
		if ( ! is_array( $key ) ) {
			$this->headers[ $key ] = $value;

			return $this;
		}
		foreach ( $key as $header => $header_value ) {
			$this->headers[ $header ] = $header_value;
		}

		return $this;
	}

	/**
	 * @param string $name
	 * @param null $value
	 *
	 * @return $this
	 */
	public function setRequestArg( $name = '', $value = null ) {
		$this->request_args[ $name ] = $value;

		return $this;
	}

	/**
	 * @uses request
	 *
	 * @param string $endpoint
	 * @param null $data
	 *
	 * @return array|mixed
	 * @throws \Exception
	 */
	public function post( $endpoint = '', $data = null ) {
		$request_body = wp_json_encode( $data );

		return $this->request( 'POST', $endpoint, $request_body );
	}

	/**
	 * @uses request
	 *
	 * @param string $endpoint
	 * @param null $data
	 *
	 * @return array|mixed
	 * @throws \Exception
	 */
	public function get( $endpoint = '', $data = null ) {
		return $this->request( 'GET', $endpoint, $data );
	}

	/**
	 * @param string $method
	 * @param string $endpoint
	 * @param null $request_body
	 * @param int $valid_response_code
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function request( $method = 'GET', $endpoint = '', $request_body = null, $valid_response_code = 200 ) {
		$request_url      = $this->api_base_url . $endpoint;
		$base_args        = array(
			'method'  => $method,
			'headers' => $this->headers,
		);
		$api_request_args = array_merge( $base_args, $this->request_args );
		if ( null !== $request_body ) {
			if ( in_array( $method, array( 'POST', 'PUT' ) ) ) {
				$api_request_args['body'] = $request_body;
			} else {
				$request_url = add_query_arg( $request_body, $request_url );
			}
		}

		$cache_key = md5( $method . $endpoint . json_encode( $api_request_args ) );
		if ( isset( $this->request_cache[ $cache_key ] ) && isset( $this->request_cache[ $cache_key ]['parsed'] ) ) {
			$this->request_cache[ $cache_key ]['parsed'];
		}

		$response      = wp_remote_request( $request_url, $api_request_args );
		$response_code = (int) wp_remote_retrieve_response_code( $response );

		$this->request_cache[ $cache_key ]['raw'] = $response;

		if ( is_wp_error( $response ) || $valid_response_code !== $response_code ) {
			throw new \Exception( 'Rest Client Error: response code ' . $response_code );
		}

		$response_body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! is_array( $response_body ) ) {
			throw new \Exception( 'Rest Client Error: unexpected response type' );
		}

		$return = array(
			'code' => $response_code,
			'body' => $response_body,
		);

		$this->request_cache[ $cache_key ]['parsed'] = $return;

		return $return;
	}
}