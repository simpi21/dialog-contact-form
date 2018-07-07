<?php

namespace DialogContactForm\Providers;

use DialogContactForm\Supports\RestClient;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ReCaptchaProvider {
	/**
	 * @var RestClient
	 */
	public $rest_client = null;

	private $api_key = '';
}