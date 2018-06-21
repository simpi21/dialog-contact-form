<?php

namespace DialogContactForm\Actions;

use DialogContactForm\Abstracts\Abstract_Action;
use DialogContactForm\Config;

class DataErasureRequest extends Abstract_Action {

	/**
	 * DataErasureRequest constructor.
	 */
	public function __construct() {
		$this->priority   = 12;
		$this->id         = 'data_erasure_request';
		$this->title      = __( 'Data Erasure Request', 'dialog-contact-form' );
		$this->meta_group = 'data_erasure_request';
		$this->meta_key   = '_action_data_erasure_request';
		$this->settings   = array_merge( $this->settings, $this->settings() );
	}

	/**
	 * Process current action
	 *
	 * @param \DialogContactForm\Config $config Contact form configurations
	 * @param array $data User submitted sanitized data
	 *
	 * @return mixed
	 */
	public static function process( $config, $data ) {
		$settings   = get_post_meta( $config->getFormId(), '_action_data_erasure_request', true );
		$user_email = isset( $data[ $settings['user_email'] ] ) ? $data[ $settings['user_email'] ] : null;

		if ( ! is_email( $user_email ) ) {
			return false;
		}

		// create request for user
		$request_id = wp_create_user_request( $user_email, 'remove_personal_data' );

		/**
		 * Basically ignore if we get a user error as it will be one of two things.
		 *
		 * 1) The email in question is already in the erase data request queue
		 * 2) The email does not belong to an actual user.
		 */
		if ( ! $request_id instanceof \WP_Error ) {
			wp_send_user_request( $request_id );
		}

		return $data;
	}

	/**
	 * Get action description
	 *
	 * @return string
	 */
	public function get_description() {
		$html = '<p class="description">';
		$html .= esc_html__( 'This action adds users to WordPress\' personal data delete tool, allowing admins to comply with the GDPR and other privacy regulations from the site\'s front end.', 'dialog-contact-form' );
		$html .= '</p>';

		return $html;
	}

	/**
	 * Action settings
	 *
	 * @return array
	 */
	private function settings() {
		$config       = Config::init();
		$email_fields = array();

		foreach ( $config->getFormFields() as $field ) {
			if ( 'email' === $field['field_type'] ) {
				$email_fields[ $field['field_id'] ] = $field['field_title'];
			}
		}

		return array(
			'field_mapping' => array(
				'type'  => 'section',
				'label' => __( 'Field Mapping', 'dialog-contact-form' ),
			),
			'user_email'    => array(
				'type'        => 'select',
				'id'          => 'user_email',
				'group'       => $this->meta_group,
				'meta_key'    => $this->meta_key,
				'label'       => __( 'Email Address *', 'dialog-contact-form' ),
				'description' => __( 'The user email address. This is required field.', 'dialog-contact-form' ),
				'options'     => $email_fields,
			),
		);
	}

}