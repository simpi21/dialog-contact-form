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
			add_action( 'save_post', array( $this, 'save_meta' ), 10, 2 );
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

			if ( isset( $_POST['field']['field_id'] ) ) {
				$_field    = $_POST['field'];
				$_field_id = $_field['field_id'];
				$_data     = array();
				for ( $i = 0; $i < count( $_field_id ); $i ++ ) {
					$_data[] = array(
						'field_title'   => sanitize_text_field( $_field['field_title'][ $i ] ),
						'field_name'    => sanitize_text_field( $_field['field_id'][ $i ] ),
						'field_id'      => sanitize_text_field( $_field['field_id'][ $i ] ),
						'field_type'    => sanitize_text_field( $_field['field_type'][ $i ] ),
						'options'       => wp_strip_all_tags( $_field['options'][ $i ] ),
						'number_min'    => $this->positive_int( $_field['number_min'][ $i ] ),
						'number_max'    => $this->positive_int( $_field['number_max'][ $i ] ),
						'number_step'   => $this->positive_int( $_field['number_step'][ $i ] ),
						'field_value'   => sanitize_text_field( $_field['field_value'][ $i ] ),
						'field_class'   => sanitize_text_field( $_field['field_class'][ $i ] ),
						'field_width'   => sanitize_text_field( $_field['field_width'][ $i ] ),
						'validation'    => isset( $_field['validation'][ $i ] ) ? $_field['validation'][ $i ] : array(),
						'placeholder'   => sanitize_text_field( $_field['placeholder'][ $i ] ),
						'error_message' => sanitize_text_field( $_field['error_message'][ $i ] ),
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
			add_meta_box(
				"dialog-cf-fields",
				__( "Fields", 'dialog-contact-form' ),
				array( $this, 'meta_box_fields_cb' ),
				$this->post_type,
				"normal",
				"high"
			);
			add_meta_box(
				"dialog-cf-configuration",
				__( "Configuration", 'dialog-contact-form' ),
				array( $this, 'meta_box_config_cb' ),
				$this->post_type,
				"side",
				"high"
			);
			add_meta_box(
				"dialog-cf-mail-template",
				__( "Mail", 'dialog-contact-form' ),
				array( $this, 'meta_box_mail_template_cb' ),
				$this->post_type,
				"normal",
				"high"
			);
			add_meta_box(
				"dialog-cf-messages",
				__( "Validation Messages", 'dialog-contact-form' ),
				array( $this, 'meta_boxe_messages_cb' ),
				$this->post_type,
				"normal",
				"high"
			);

		}

		/**
		 * Metabox configuration callback
		 */
		public function meta_box_config_cb() {
			include_once DIALOG_CONTACT_FORM_TEMPLATES . '/admin/configuration.php';
		}

		/**
		 * Metabox message callback
		 */
		public function meta_boxe_messages_cb() {
			include_once DIALOG_CONTACT_FORM_TEMPLATES . '/admin/messages.php';
		}

		/**
		 * Metabox mail template callback
		 */
		public function meta_box_mail_template_cb() {
			include_once DIALOG_CONTACT_FORM_TEMPLATES . '/admin/mail-template.php';
		}

		/**
		 * Metabox fields callback
		 */
		public function meta_box_fields_cb() {
			include_once DIALOG_CONTACT_FORM_TEMPLATES . '/admin/fields.php';
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
