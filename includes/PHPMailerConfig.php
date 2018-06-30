<?php

namespace DialogContactForm;

use DialogContactForm\Supports\Utils;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PHPMailerConfig {
	/**
	 * Configure PHPMailer for sending email over SMTP
	 *
	 * @param \PHPMailer $mailer
	 */
	public static function config( &$mailer ) {
		if ( ! in_array( Utils::get_option( 'mailer' ), array( 'yes', 'on', '1', 1, true, 'true' ), true ) ) {
			return;
		}

		$host       = Utils::get_option( 'smpt_host' );
		$username   = Utils::get_option( 'smpt_username' );
		$password   = Utils::get_option( 'smpt_password' );
		$port       = Utils::get_option( 'smpt_port' );
		$encryption = Utils::get_option( 'encryption' );

		if ( empty( $host ) || empty( $username ) || empty( $password ) || empty( $port ) ) {
			return;
		}

		$mailer->isSMTP();
		$mailer->SMTPAuth = true;
		$mailer->Host     = $host;
		$mailer->Port     = $port;
		$mailer->Username = $username;
		$mailer->Password = $password;

		// Additional settings…
		if ( in_array( $encryption, array( 'ssl', 'tls' ) ) ) {
			$mailer->SMTPSecure = $encryption;
		}
	}
}
