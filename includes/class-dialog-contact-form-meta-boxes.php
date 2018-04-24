<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Dialog_Contact_Form_Meta_Boxes' ) ) {

	class Dialog_Contact_Form_Meta_Boxes {

		/**
		 * Dialog Contact Form post-type
		 *
		 * @var string
		 */
		private $post_type = DIALOG_CONTACT_FORM;

		/**
		 * @var object
		 */
		private static $instance;

		/**
		 * Ensures only one instance of this class is loaded or can be loaded.
		 */
		public static function init() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * DialogContactFormMetaBoxes constructor.
		 */
		public function __construct() {
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
			add_action( 'edit_form_after_title', array( $this, 'edit_form_after_title' ) );
			add_action( 'save_post', array( $this, 'save_meta' ), 10, 2 );
		}

		/**
		 * @param \WP_Post $post
		 *
		 * @return void
		 */
		public function edit_form_after_title( $post ) {
			if ( DIALOG_CONTACT_FORM_POST_TYPE !== $post->post_type ) {
				return;
			}
			?>
            <div class="dcf-tabs-wrapper">
                <div id="dcf-metabox-tabs" class="dcf-tabs">
                    <ul class="dcf-tabs-list">
                        <li class="dcf-tab-list--fields">
                            <a href="#dcf-tab-1">
								<?php esc_html_e( 'Fields', 'dialog-contact-form' ); ?>
                            </a>
                        </li>
                        <li class="dcf-tab-list--configuration">
                            <a href="#dcf-tab-2">
								<?php esc_html_e( 'Configuration', 'dialog-contact-form' ); ?>
                            </a>
                        </li>
                        <li class="dcf-tab-list--mail">
                            <a href="#dcf-tab-3">
								<?php esc_html_e( 'Mail', 'dialog-contact-form' ); ?>
                            </a>
                        </li>
                        <li class="dcf-tab-list--message">
                            <a href="#dcf-tab-4">
								<?php esc_html_e( 'Message', 'dialog-contact-form' ); ?>
                            </a>
                        </li>
                    </ul>
                    <div id="dcf-tab-1" class="dcf_options_panel">
						<?php include_once DIALOG_CONTACT_FORM_TEMPLATES . '/admin/fields.php'; ?>
                    </div>
                    <div id="dcf-tab-2" class="dcf_options_panel">
						<?php include_once DIALOG_CONTACT_FORM_TEMPLATES . '/admin/configuration.php'; ?>
                    </div>
                    <div id="dcf-tab-3" class="dcf_options_panel">
						<?php include_once DIALOG_CONTACT_FORM_TEMPLATES . '/admin/mail-template.php'; ?>
                    </div>
                    <div id="dcf-tab-4" class="dcf_options_panel">
						<?php include_once DIALOG_CONTACT_FORM_TEMPLATES . '/admin/messages.php'; ?>
                    </div>
                </div>
            </div>
			<?php

		}

		/**
		 * Save post metadata when a post is saved.
		 *
		 * @param int $post_id The post ID.
		 * @param WP_Post $post The post object.
		 */
		public function save_meta( $post_id, $post ) {
			// If this isn't a 'contact-form' post, don't update it.
			if ( $post->post_type != $this->post_type ) {
				return;
			}

			// - Update the post's metadata.
			if ( isset( $_POST['config'] ) ) {
				update_post_meta( $post_id, '_contact_form_config', self::sanitize_value( $_POST['config'] ) );
			}

			if ( isset( $_POST['messages'] ) ) {
				update_post_meta( $post_id, '_contact_form_messages', self::sanitize_value( $_POST['messages'] ) );
			}

			if ( isset( $_POST['mail'] ) ) {
				update_post_meta( $post_id, '_contact_form_mail', $_POST['mail'] );
			}

			if ( isset( $_POST['field'] ) && is_array( $_POST['field'] ) ) {
				$_data = array();
				foreach ( $_POST['field'] as $field ) {
					$_data[] = array(
						'field_title'   => isset( $field['field_title'] ) ? sanitize_text_field( $field['field_title'] ) : '',
						'field_name'    => isset( $field['field_id'] ) ? sanitize_text_field( $field['field_id'] ) : '',
						'field_id'      => isset( $field['field_id'] ) ? sanitize_text_field( $field['field_id'] ) : '',
						'field_type'    => isset( $field['field_type'] ) ? sanitize_text_field( $field['field_type'] ) : '',
						'options'       => isset( $field['options'] ) ? wp_strip_all_tags( $field['options'] ) : '',
						'number_min'    => isset( $field['number_min'] ) ? $this->positive_int( $field['number_min'] ) : '',
						'number_max'    => isset( $field['number_max'] ) ? $this->positive_int( $field['number_max'] ) : '',
						'number_step'   => isset( $field['number_step'] ) ? $this->positive_int( $field['number_step'] ) : '',
						'field_value'   => isset( $field['field_value'] ) ? sanitize_text_field( $field['field_value'] ) : '',
						'field_class'   => isset( $field['field_class'] ) ? sanitize_text_field( $field['field_class'] ) : '',
						'field_width'   => isset( $field['field_width'] ) ? sanitize_text_field( $field['field_width'] ) : '',
						'validation'    => isset( $field['validation'] ) ? self::sanitize_value( $field['validation'] ) : array(),
						'placeholder'   => isset( $field['placeholder'] ) ? sanitize_text_field( $field['placeholder'] ) : '',
						'error_message' => isset( $field['error_message'] ) ? sanitize_text_field( $field['error_message'] ) : '',
					);
				}

				update_post_meta( $post_id, '_contact_form_fields', $_data );
			}
		}

		/**
		 * Check if the value is a positive integer
		 *
		 * @param $value
		 *
		 * @return int|null
		 */
		private function positive_int( $value ) {
			$value = preg_replace( '/^[\pZ\pC]+|[\pZ\pC]+$/u', '', $value );
			if ( empty( $value ) ) {
				return null;
			}

			return $value;
		}

		/**
		 * Add carousel slider meta box
		 */
		public function add_meta_boxes() {
			add_meta_box(
				'dialog-cf-shortcode',
				__( 'Shortcode', 'dialog-contact-form' ),
				array( $this, 'meta_box_shortcode_cb' ),
				$this->post_type,
				'side',
				'high'
			);
		}

		/**
		 * Metabox shortcode callback
		 */
		public function meta_box_shortcode_cb() {
			global $post;
			$shortcode = sprintf( '[dialog_contact_form id=\'%s\']', $post->ID );
			?>
            <p><?php esc_html_e( 'Copy this shortcode and paste it into your post, page, or text widget content:
', 'dialog-contact-form' ); ?></p>
            <input
                    type="text"
                    onmousedown="this.clicked = 1;"
                    onfocus="if (!this.clicked) this.select(); else this.clicked = 2;"
                    onclick="if (this.clicked === 2) this.select(); this.clicked = 0;"
                    value="<?php echo $shortcode; ?>"
                    style="background-color: #f1f1f1;letter-spacing: 1px;width: 100%;padding: 5px 8px;"
            >
			<?php
		}


		/**
		 * Sanitize meta value
		 *
		 * @param $input
		 *
		 * @return array|string
		 */
		private static function sanitize_value( $input ) {
			// Initialize the new array that will hold the sanitize values
			$new_input = array();

			if ( is_array( $input ) ) {
				// Loop through the input and sanitize each of the values
				foreach ( $input as $key => $value ) {
					if ( is_array( $value ) ) {
						$new_input[ $key ] = self::sanitize_value( $value );
					} else {
						$new_input[ $key ] = sanitize_text_field( $value );
					}
				}
			} else {
				return sanitize_text_field( $input );
			}

			return $new_input;
		}
	}
}

Dialog_Contact_Form_Meta_Boxes::init();
