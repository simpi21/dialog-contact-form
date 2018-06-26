<?php

namespace DialogContactForm\Supports;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Mailer
 */
class Mailer {

	/**
	 * Array of email addresses to send message.
	 *
	 * @var array
	 */
	private $receiver;

	/**
	 * Email subject
	 *
	 * @var string
	 */
	private $subject = '';

	/**
	 * Email body
	 *
	 * @var string
	 */
	private $message = '';

	/**
	 * List of headers
	 *
	 * @var array
	 */
	private $headers = array();

	/**
	 * List of attachments file path
	 *
	 * @var array
	 */
	private $attachments = array();

	/**
	 * Send mail using WordPress wp_mail() function
	 *
	 * @return bool Whether the email contents were sent successfully.
	 */
	public function send() {
		return wp_mail(
			$this->getReceiver(),
			$this->getSubject(),
			$this->getMessage(),
			$this->getHeaders(),
			$this->getAttachments()
		);
	}

	/**
	 * Get mail receiver
	 *
	 * @return array
	 */
	public function getReceiver() {
		return array_filter( $this->receiver );
	}

	/**
	 * Set mail receiver
	 *
	 * @param string|array $receiver
	 *
	 * @return $this
	 */
	public function setReceiver( $receiver ) {
		if ( is_string( $receiver ) ) {
			$this->receiver[] = sanitize_email( $receiver );
		}

		if ( is_array( $receiver ) ) {
			foreach ( $receiver as $_receiver ) {
				if ( ! is_string( $_receiver ) ) {
					continue;
				}
				$this->receiver[] = sanitize_email( $_receiver );
			}
		}

		return $this;
	}

	/**
	 * Get mail subject
	 *
	 * @return mixed
	 */
	public function getSubject() {
		return $this->subject;
	}

	/**
	 * Set mail subject
	 *
	 * @param string $subject
	 *
	 * @return $this
	 */
	public function setSubject( $subject ) {
		if ( is_string( $subject ) ) {
			$this->subject = sanitize_text_field( $subject );
		}

		return $this;
	}

	/**
	 * Get mail body
	 *
	 * @return string
	 */
	public function getMessage() {
		return $this->message;
	}

	/**
	 * Set mail body
	 *
	 * @param string $message
	 *
	 * @return $this
	 */
	public function setMessage( $message ) {
		if ( is_string( $message ) ) {
			$this->message = $message;
		}

		return $this;
	}

	/**
	 * Get mail headers
	 *
	 * @return array
	 */
	public function getHeaders() {
		$headers  = array_unique( $this->headers );
		$_headers = array();
		foreach ( $headers as $header ) {
			$_headers[] = htmlspecialchars_decode( $header, ENT_QUOTES );
		}

		return $_headers;
	}

	/**
	 * Set mail headers
	 *
	 * @param array|string $headers
	 *
	 * @return $this
	 */
	public function setHeaders( $headers ) {
		if ( is_array( $headers ) ) {
			foreach ( $headers as $header ) {
				if ( ! is_string( $header ) ) {
					continue;
				}
				$this->headers[] = $this->encode_special_chars( $header );
			}
		}
		$this->headers[] = $this->encode_special_chars( $headers );

		return $this;
	}

	/**
	 * Get attachments
	 *
	 * @return array
	 */
	public function getAttachments() {
		return $this->attachments;
	}

	/**
	 * Set attachments
	 *
	 * @param array $attachments
	 *
	 * @return $this
	 */
	public function setAttachments( $attachments ) {
		$this->attachments = $attachments;

		return $this;
	}

	/**
	 * Set mail sender email address
	 *
	 * @param string $address
	 * @param string $name
	 *
	 * @return $this
	 */
	public function setFrom( $address, $name = '' ) {
		$email = sanitize_email( $address );
		if ( ! empty( $name ) ) {
			$name            = sanitize_text_field( $name );
			$this->headers[] = $this->encode_special_chars( "From: $name <$email>" );
		} else {
			$this->headers[] = "From: $email";
		}

		return $this;
	}

	/**
	 * Set mail reply to
	 *
	 * @param string $address
	 * @param string $name
	 *
	 * @return $this
	 */
	public function setReplyTo( $address, $name = '' ) {
		$email = sanitize_email( $address );
		if ( ! empty( $name ) ) {
			$name            = sanitize_text_field( $name );
			$this->headers[] = $this->encode_special_chars( "Reply-To: $name <$email>" );
		} else {
			$this->headers[] = "Reply-To: $email";
		}

		return $this;
	}

	/**
	 * Set mail content type html or text
	 *
	 * @param string $type
	 *
	 * @return $this
	 */
	public function setContentType( $type = 'html' ) {
		if ( 'html' == $type ) {
			$this->headers[] = 'Content-Type: text/html; charset=UTF-8';
		} else {
			$this->headers[] = 'Content-Type: text/plain; charset=UTF-8';
		}

		return $this;
	}

	/**
	 * Convert special characters to HTML entities
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	private function encode_special_chars( $string ) {
		return htmlspecialchars( $string, ENT_QUOTES, 'UTF-8' );
	}
}
