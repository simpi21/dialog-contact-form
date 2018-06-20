<?php

namespace DialogContactForm;

use DialogContactForm\Abstracts\Abstract_Form_Template;
use DialogContactForm\Supports\Metabox;

class AdminAjax {

	/**
	 * The instance of the class
	 *
	 * @var self
	 */
	private static $instance;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return self
	 */
	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();

			add_action( 'wp_ajax_dcf_field_settings', array( self::$instance, 'get_field_settings' ) );
			add_action( 'wp_ajax_dcf_new_form', array( self::$instance, 'add_new_form' ) );
		}

		return self::$instance;
	}

	/**
	 * Get field settings
	 */
	public static function get_field_settings() {
		if ( ! isset( $_POST['type'] ) ) {
			wp_send_json_error( __( 'Required fields are not set properly.', 'dialog-contact-form' ), 422 );
		}

		$_types     = Utils::field_types();
		$field_type = isset( $_POST['type'] ) && in_array( $_POST['type'], array_keys( $_types ) ) ? $_POST['type'] : null;
		$settings   = self::field_settings( $field_type );

		$supported  = array();
		$class_name = '\\DialogContactForm\\Fields\\' . ucfirst( $field_type );
		if ( class_exists( $class_name ) ) {
			/** @var \DialogContactForm\Abstracts\Abstract_Field $class */
			$class     = new $class_name;
			$supported = $class->getMetaboxFields();
		}

		ob_start();
		?>
        <div data-id="closed" class="dcf-toggle dcf-toggle--normal">
        <span class="dcf-toggle-title">
                <?php
                if ( ! empty( $_types[ $field_type ]['icon'] ) ) {
	                echo '<span class="dcf-toggle-title--icon"><i class="' . $_types[ $field_type ]['icon'] . '"></i></span>';
                }
                echo '<span class="dcf-toggle-title--label">' . esc_html__( 'Untitled', 'dialog-contact-form' ) . '</span>';
                ?>
            </span>
            <div class="dcf-toggle-inner">
                <div class="dcf-toggle-content">
                    <p>
                        <button class="button deleteField">
							<?php esc_html_e( 'Delete this field', 'dialog-contact-form' ); ?>
                        </button>
                    </p>
					<?php
					foreach ( $settings as $field_id => $setting ) {
						if ( ! in_array( $field_id, $supported ) ) {
							continue;
						}
						if ( method_exists( '\DialogContactForm\Supports\Metabox', $setting['type'] ) ) {
							Metabox::{$setting['type']}( $setting );
						}
					}
					?>
                </div>
            </div>
        </div>
		<?php
		$html = ob_get_contents();
		ob_end_clean();

		echo $html;
		die();
	}

	/**
	 * Form field settings
	 *
	 * @param string $type
	 * @param int $index
	 *
	 * @return array
	 */
	private static function field_settings( $type = 'text', $index = 100 ) {
		return array(
			'field_type'         => array(
				'type'        => 'hidden',
				'id'          => 'field_type',
				'group'       => 'field',
				'meta_key'    => '_contact_form_fields',
				'group_class' => 'dcf-input-group col-field_type',
				'position'    => $index,
				'default'     => $type,
			),
			'field_title'        => array(
				'type'        => 'text',
				'id'          => 'field_title',
				'group'       => 'field',
				'meta_key'    => '_contact_form_fields',
				'group_class' => 'dcf-input-group col-field_title',
				'position'    => $index,
				'input_class' => 'dcf-input-text dcf-field-title',
				'label'       => __( 'Label', 'dialog-contact-form' ),
				'description' => __( 'Enter the label for the field.', 'dialog-contact-form' ),
			),
			'field_id'           => array(
				'type'        => 'text',
				'id'          => 'field_id',
				'group'       => 'field',
				'meta_key'    => '_contact_form_fields',
				'group_class' => 'dcf-input-group col-field_id',
				'position'    => $index,
				'input_class' => 'dcf-input-text dcf-field-id',
				'label'       => __( 'Field ID', 'dialog-contact-form' ),
				'description' => __( 'Please make sure the ID is unique and not used elsewhere in this form. This field allows A-z 0-9 & underscore chars without spaces.',
					'dialog-contact-form' ),
			),
			'required_field'     => array(
				'type'        => 'buttonset',
				'id'          => 'required_field',
				'group'       => 'field',
				'group_class' => 'dcf-input-group col-required_field',
				'position'    => $index,
				'meta_key'    => '_contact_form_fields',
				'label'       => __( 'Required Field', 'dialog-contact-form' ),
				'description' => __( 'Check this option to mark the field required. A form will not submit unless all required fields are provided.',
					'dialog-contact-form' ),
				'default'     => 'off',
				'options'     => array(
					'off' => esc_html__( 'No', 'dialog-contact-form' ),
					'on'  => esc_html__( 'Yes', 'dialog-contact-form' ),
				),
			),
			'options'            => array(
				'type'        => 'textarea',
				'id'          => 'options',
				'group'       => 'field',
				'group_class' => 'dcf-input-group col-addOptions',
				'position'    => $index,
				'meta_key'    => '_contact_form_fields',
				'label'       => __( 'Add options', 'dialog-contact-form' ),
				'description' => __( 'One option per line.', 'dialog-contact-form' ),
				'rows'        => 8,
			),
			'number_options'     => array(
				'type'        => 'number_options',
				'group'       => 'field',
				'group_class' => 'dcf-input-group col-numberOption',
				'position'    => $index,
				'meta_key'    => '_contact_form_fields',
				'label'       => __( 'Number Option', 'dialog-contact-form' ),
				'description' => __( 'This fields is optional but you can set min value, max value and step. For allowing decimal values set step value (e.g. step="0.01" to allow decimals to two decimal places).',
					'dialog-contact-form' ),
			),
			'number_min'         => array(
				'type'        => 'number',
				'id'          => 'number_min',
				'group'       => 'field',
				'group_class' => 'dcf-input-group col-number_min',
				'position'    => $index,
				'meta_key'    => '_contact_form_fields',
				'label'       => __( 'Min Number', 'dialog-contact-form' ),
			),
			'number_max'         => array(
				'type'        => 'number',
				'id'          => 'number_max',
				'group'       => 'field',
				'group_class' => 'dcf-input-group col-number_max',
				'position'    => $index,
				'meta_key'    => '_contact_form_fields',
				'label'       => __( 'Max Number', 'dialog-contact-form' ),
			),
			'number_step'        => array(
				'type'        => 'number',
				'id'          => 'number_step',
				'group'       => 'field',
				'group_class' => 'dcf-input-group col-number_step',
				'position'    => $index,
				'meta_key'    => '_contact_form_fields',
				'label'       => __( 'Step Number', 'dialog-contact-form' ),
				'description' => __( 'For allowing decimal values set step value (e.g. "0.01" to allow decimals to two decimal places).',
					'dialog-contact-form' ),
			),
			'field_value'        => array(
				'type'        => 'text',
				'id'          => 'field_value',
				'group'       => 'field',
				'group_class' => 'dcf-input-group col-field_value',
				'position'    => $index,
				'meta_key'    => '_contact_form_fields',
				'label'       => __( 'Default Value', 'dialog-contact-form' ),
				'description' => __( 'Define field default value.', 'dialog-contact-form' ),
			),
			'field_class'        => array(
				'type'        => 'text',
				'id'          => 'field_class',
				'group'       => 'field',
				'group_class' => 'dcf-input-group col-field_class',
				'position'    => $index,
				'meta_key'    => '_contact_form_fields',
				'label'       => __( 'Field Class', 'dialog-contact-form' ),
				'description' => __( 'Insert additional class(es) (separated by blank space) for more personalization.',
					'dialog-contact-form' ),
			),
			'field_width'        => array(
				'type'        => 'select',
				'id'          => 'field_width',
				'group'       => 'field',
				'group_class' => 'dcf-input-group col-field_width',
				'position'    => $index,
				'meta_key'    => '_contact_form_fields',
				'label'       => __( 'Field Width', 'dialog-contact-form' ),
				'description' => __( 'Set field length.', 'dialog-contact-form' ),
				'default'     => 'is-12',
				'options'     => array(
					'is-12' => esc_html__( 'Full', 'dialog-contact-form' ),
					'is-9'  => esc_html__( 'Three Quarters', 'dialog-contact-form' ),
					'is-8'  => esc_html__( 'Two Thirds', 'dialog-contact-form' ),
					'is-6'  => esc_html__( 'Half', 'dialog-contact-form' ),
					'is-4'  => esc_html__( 'One Third', 'dialog-contact-form' ),
					'is-3'  => esc_html__( 'One Quarter', 'dialog-contact-form' ),
				),
			),
			'placeholder'        => array(
				'type'        => 'text',
				'id'          => 'placeholder',
				'group'       => 'field',
				'group_class' => 'dcf-input-group col-placeholder',
				'position'    => $index,
				'meta_key'    => '_contact_form_fields',
				'input_class' => 'dcf-input-text dcf-field-placeholder',
				'label'       => __( 'Placeholder Text', 'dialog-contact-form' ),
				'description' => __( 'Insert placeholder message.', 'dialog-contact-form' ),
			),
			'acceptance_text'    => array(
				'type'        => 'textarea',
				'id'          => 'acceptance_text',
				'group'       => 'field',
				'group_class' => 'dcf-input-group col-acceptance',
				'position'    => $index,
				'meta_key'    => '_contact_form_fields',
				'label'       => __( 'Acceptance Text', 'dialog-contact-form' ),
				'description' => __( 'Insert acceptance text. you can also use inline html markup.',
					'dialog-contact-form' ),
				'rows'        => 3,
			),
			'checked_by_default' => array(
				'type'        => 'buttonset',
				'id'          => 'checked_by_default',
				'group'       => 'field',
				'group_class' => 'dcf-input-group col-acceptance',
				'position'    => $index,
				'meta_key'    => '_contact_form_fields',
				'label'       => __( 'Checked by default', 'dialog-contact-form' ),
				'default'     => 'off',
				'options'     => array(
					'off' => esc_html__( 'No', 'dialog-contact-form' ),
					'on'  => esc_html__( 'Yes', 'dialog-contact-form' ),
				),
			),
			'min_date'           => array(
				'type'        => 'date',
				'id'          => 'min_date',
				'group'       => 'field',
				'group_class' => 'dcf-input-group col-min_date',
				'position'    => $index,
				'meta_key'    => '_contact_form_fields',
				'label'       => __( 'Min. Date', 'dialog-contact-form' ),
			),
			'max_date'           => array(
				'type'        => 'date',
				'id'          => 'max_date',
				'group'       => 'field',
				'group_class' => 'dcf-input-group col-max_date',
				'position'    => $index,
				'meta_key'    => '_contact_form_fields',
				'label'       => __( 'Max. Date', 'dialog-contact-form' ),
			),
			'native_html5'       => array(
				'type'        => 'buttonset',
				'id'          => 'native_html5',
				'group'       => 'field',
				'group_class' => 'dcf-input-group col-native_html5',
				'position'    => $index,
				'meta_key'    => '_contact_form_fields',
				'label'       => __( 'Native HTML5', 'dialog-contact-form' ),
				'default'     => 'on',
				'options'     => array(
					'off' => esc_html__( 'No', 'dialog-contact-form' ),
					'on'  => esc_html__( 'Yes', 'dialog-contact-form' ),
				),
			),
			'max_file_size'      => array(
				'type'        => 'file_size',
				'id'          => 'max_file_size',
				'group'       => 'field',
				'group_class' => 'dcf-input-group col-max_file_size',
				'position'    => $index,
				'meta_key'    => '_contact_form_fields',
				'label'       => __( 'Max. File Size', 'dialog-contact-form' ),
				'description' => __( 'If you need to increase max upload size please contact your hosting.',
					'dialog-contact-form' ),
				'default'     => '2',
			),
			'allowed_file_types' => array(
				'type'        => 'mime_type',
				'id'          => 'allowed_file_types',
				'group'       => 'field',
				'group_class' => 'dcf-input-group col-allowed_file_types',
				'position'    => $index,
				'meta_key'    => '_contact_form_fields',
				'label'       => __( 'Allowed File Types', 'dialog-contact-form' ),
				'description' => __( 'Choose file types.', 'dialog-contact-form' ),
				'multiple'    => true,
			),
			'multiple'           => array(
				'type'        => 'buttonset',
				'id'          => 'multiple',
				'group'       => 'field',
				'group_class' => 'dcf-input-group col-multiple',
				'position'    => $index,
				'meta_key'    => '_contact_form_fields',
				'label'       => __( 'Multiple', 'dialog-contact-form' ),
				'default'     => 'off',
				'options'     => array(
					'off' => esc_html__( 'No', 'dialog-contact-form' ),
					'on'  => esc_html__( 'Yes', 'dialog-contact-form' ),
				),
			),
			'rows'               => array(
				'type'        => 'number',
				'id'          => 'rows',
				'group'       => 'field',
				'group_class' => 'dcf-input-group col-rows',
				'position'    => $index,
				'meta_key'    => '_contact_form_fields',
				'label'       => __( 'Rows', 'dialog-contact-form' ),
			),
			'autocomplete'       => array(
				'type'        => 'select',
				'id'          => 'autocomplete',
				'group'       => 'field',
				'input_class' => 'dcf-input-text dcf-input-autocomplete',
				'group_class' => 'dcf-input-group col-autocomplete',
				'position'    => $index,
				'meta_key'    => '_contact_form_fields',
				'label'       => __( 'Autocomplete', 'dialog-contact-form' ),
				'options'     => Utils::autocomplete_values()
			),
			'html'               => array(
				'type'        => 'textarea',
				'id'          => 'html',
				'group'       => 'field',
				'input_class' => 'dcf-input-textarea dcf-input-html',
				'group_class' => 'dcf-input-group col-html',
				'position'    => $index,
				'meta_key'    => '_contact_form_fields',
				'label'       => __( 'HTML', 'dialog-contact-form' ),
				'rows'        => 5,
			),
			'validation'         => array(// @depreciated
				'type'        => 'checkbox',
				'id'          => 'validation',
				'group'       => 'field',
				'group_class' => 'dcf-input-group col-validation',
				'position'    => $index,
				'meta_key'    => '_contact_form_fields',
				'label'       => __( 'Validation', 'dialog-contact-form' ),
				'options'     => Utils::validation_rules(),
			),
			'error_message'      => array(// @depreciated
				'type'        => 'text',
				'id'          => 'error_message',
				'group'       => 'field',
				'group_class' => 'dcf-input-group col-error_message',
				'position'    => $index,
				'meta_key'    => '_contact_form_fields',
				'label'       => __( 'Error Message', 'dialog-contact-form' ),
				'description' => __( 'Insert the error message for validation. The length of message must be 10 characters or more. Leave blank for default message.',
					'dialog-contact-form' ),
			),
		);
	}

	/**
	 * Add new form from template
	 * @internal Abstract_Form_Template $template
	 */
	public static function add_new_form() {
		if ( ! isset( $_REQUEST['template'] ) ) {
			wp_die( __( 'Form template is not set properly.', 'dialog-contact-form' ) );
		}

		$templates = TemplateManager::init();
		$template  = $templates[ $_REQUEST['template'] ];
		if ( ! $template instanceof Abstract_Form_Template ) {
			wp_die( __( 'Form template is not available.', 'dialog-contact-form' ) );
		}

		$post_id = wp_insert_post( array(
			'post_title'     => $template->get_title(),
			'post_status'    => 'publish',
			'post_type'      => DIALOG_CONTACT_FORM_POST_TYPE,
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		) );

		if ( is_wp_error( $post_id ) ) {
			wp_die( __( 'Could not create form.', 'dialog-contact-form' ) );
		}

		$template->run( $post_id );

		$redirect_url = add_query_arg( array(
			'post'   => $post_id,
			'action' => 'edit',
		), admin_url( 'post.php' ) );

		wp_safe_redirect( $redirect_url );
		exit();
	}
}