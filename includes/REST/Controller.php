<?php

namespace DialogContactForm\REST;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Controller {

	/**
	 * The namespace of this controller's route.
	 *
	 * @var string
	 */
	protected static $namespace = 'dialog-contact-form/v1';

	/**
	 * The base of this controller's route.
	 *
	 * @var string
	 */
	protected static $rest_base = 'dialog-contact-form/v1';

	/**
	 * HTTP Status Code
	 * @var int
	 */
	protected $statusCode = 200;

	/**
	 * MYSQL date format
	 *
	 * @var string
	 */
	protected static $mysql_date_format = 'Y-m-d';

	/**
	 * Get status code
	 *
	 * @return integer
	 */
	public function getStatusCode() {
		return $this->statusCode;
	}

	/**
	 * @param integer $statusCode
	 *
	 * @return self
	 */
	public function setStatusCode( $statusCode ) {
		$this->statusCode = $statusCode;

		return $this;
	}

	/**
	 * Respond.
	 *
	 * @param mixed $data Response data. Default null.
	 * @param int $status Optional. HTTP status code. Default 200.
	 * @param array $headers Optional. HTTP header map. Default empty array.
	 *
	 * @return \WP_REST_Response
	 */
	public function respond( $data = null, $status = 200, $headers = array() ) {
		return new \WP_REST_Response( $data, $status, $headers );
	}

	/**
	 * Response error message
	 *
	 * @param string $message
	 * @param mixed $data
	 *
	 * @return \WP_REST_Response
	 */
	public function respondWithError( $data = null, $message = null ) {
		$code = $this->getStatusCode();
		if ( empty( $message ) ) {
			$message = $this->getStatusDescription( $code );
		}
		$response = [ 'success' => false, 'message' => $message, 'code' => $code ];

		if ( ! empty( $data ) ) {
			$response['data'] = $data;
		}

		return $this->respond( $response, $code );
	}

	/**
	 * Response success message
	 *
	 * @param string $message
	 * @param mixed $data
	 *
	 * @return \WP_REST_Response
	 */
	public function respondWithSuccess( $data = null, $message = null ) {
		$code = $this->getStatusCode();
		if ( empty( $message ) ) {
			$message = $this->getStatusDescription( $code );
		}
		$response = [ 'success' => true, 'message' => $message, 'code' => $code ];

		if ( ! empty( $data ) ) {
			$response['data'] = $data;
		}

		return $this->respond( $response, $code );
	}

	/**
	 * 200 (OK)
	 * The request has succeeded.
	 *
	 * Use cases:
	 * --> update/retrieve data
	 * --> bulk creation
	 * --> bulk update
	 *
	 * @param mixed $data
	 * @param string $message
	 *
	 * @return \WP_REST_Response
	 */
	public function respondOK( $data = null, $message = null ) {
		return $this->setStatusCode( 200 )->respondWithSuccess( $data, $message );
	}

	/**
	 * 201 (Created)
	 * The request has succeeded and a new resource has been created as a result of it.
	 * This is typically the response sent after a POST request, or after some PUT requests.
	 *
	 * @param mixed $data
	 * @param string $message
	 *
	 * @return \WP_REST_Response
	 */
	public function respondCreated( $data = null, $message = null ) {
		return $this->setStatusCode( 201 )->respondWithSuccess( $data, $message );
	}

	/**
	 * 202 (Accepted)
	 * The request has been received but not yet acted upon.
	 * The response should include the Location header with a link towards the location where
	 * the final response can be polled & later obtained.
	 *
	 * Use cases:
	 * --> asynchronous tasks (e.g., report generation)
	 * --> batch processing
	 * --> delete data that is NOT immediate
	 *
	 * @param mixed $data
	 * @param string $message
	 *
	 * @return \WP_REST_Response
	 */
	public function respondAccepted( $data = null, $message = null ) {
		return $this->setStatusCode( 202 )->respondWithSuccess( $data, $message );
	}

	/**
	 * 400 (Bad request)
	 * Server could not understand the request due to invalid syntax.
	 *
	 * Use cases:
	 * --> invalid/incomplete request
	 * --> return multiple client errors at once
	 *
	 * @param mixed $data
	 * @param string $message
	 *
	 * @return \WP_REST_Response
	 */
	public function respondBadRequest( $data = null, $message = null ) {
		return $this->setStatusCode( 400 )->respondWithError( $data, $message );
	}

	/**
	 * 401 (Unauthorized)
	 * The request requires user authentication.
	 *
	 * @param mixed $data
	 * @param string $message
	 *
	 * @return \WP_REST_Response
	 */
	public function respondUnauthorized( $data = null, $message = null ) {
		return $this->setStatusCode( 401 )->respondWithError( $data, $message );
	}

	/**
	 * 403 (Forbidden)
	 * The client is authenticated but not authorized to perform the action.
	 *
	 * @param mixed $data
	 * @param string $message
	 *
	 * @return \WP_REST_Response
	 */
	public function respondForbidden( $data = null, $message = null ) {
		return $this->setStatusCode( 403 )->respondWithError( $data, $message );
	}

	/**
	 * 404 (Not Found)
	 * The server can not find requested resource. In an API, this can also mean that the endpoint is valid but
	 * the resource itself does not exist. Servers may also send this response instead of 403 to hide
	 * the existence of a resource from an unauthorized client.
	 *
	 * @param mixed $data
	 * @param string $message
	 *
	 * @return \WP_REST_Response
	 */
	public function respondNotFound( $data = null, $message = null ) {
		return $this->setStatusCode( 404 )->respondWithError( $data, $message );
	}

	/**
	 * 422 (Unprocessable Entity)
	 * The request was well-formed but was unable to be followed due to semantic errors.
	 *
	 * @param mixed $data
	 * @param string $message
	 *
	 * @return \WP_REST_Response
	 */
	public function respondUnprocessableEntity( $data = null, $message = null ) {
		return $this->setStatusCode( 422 )->respondWithError( $data, $message );
	}

	/**
	 * 500 (Internal Server Error)
	 * The server has encountered a situation it doesn't know how to handle.
	 *
	 * @param mixed $data
	 * @param string $message
	 *
	 * @return \WP_REST_Response
	 */
	public function respondInternalServerError( $data = null, $message = null ) {
		return $this->setStatusCode( 500 )->respondWithError( $data, $message );
	}


	/**
	 * Retrieve the description for the HTTP status.
	 *
	 * @param int $code HTTP status code.
	 *
	 * @return string Empty string if not found, or description if found.
	 */
	protected function getStatusDescription( $code ) {
		return get_status_header_desc( $code );
	}

	/**
	 * @param $date
	 * @param string $type
	 *
	 * @return \DateTime|int|string
	 */
	protected static function formatDate( $date, $type = 'raw' ) {
		if ( ! $date instanceof \DateTime ) {
			$date = new \DateTime( $date );

			$timezone = get_option( 'timezone_string' );
			if ( in_array( $timezone, \DateTimeZone::listIdentifiers() ) ) {
				$date->setTimezone( new \DateTimeZone( $timezone ) );
			}
		}

		if ( 'mysql' == $type ) {
			return $date->format( self::$mysql_date_format );
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
}
