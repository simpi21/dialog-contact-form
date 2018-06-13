<?php

use DialogContactForm\Supports\Metabox;
use DialogContactForm\Utils;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div id="shaplaFieldList">
	<?php
	global $post;
	$_fields     = get_post_meta( $post->ID, '_contact_form_fields', true );
	$_fields     = is_array( $_fields ) ? $_fields : array();
	$field_types = Utils::field_types();

	if ( ! isset( $_GET['action'] ) && count( $_fields ) === 0 ) {
		$_fields = dcf_default_fields();
	}

	if ( count( $_fields ) < 1 ) {
		return;
	}

	foreach ( $_fields as $_field_number => $_field ) {
		$is_required_field = 'off';
		if ( is_array( $_field['validation'] ) && in_array( 'required', $_field['validation'] ) ) {
			$is_required_field = 'on';
		}
		if ( isset( $_field['required_field'] ) && in_array( $_field['required_field'], array( 'on', 'off' ) ) ) {
			$is_required_field = $_field['required_field'];
		}

		$supported  = array();
		$class_name = '\\DialogContactForm\\Fields\\' . ucfirst( $_field['field_type'] );
		if ( class_exists( $class_name ) ) {
			/** @var \DialogContactForm\Abstracts\Abstract_Field $class */
			$class     = new $class_name;
			$supported = $class->getMetaboxFields();
		}

		$_type = isset( $field_types[ $_field['field_type'] ] ) ? $field_types[ $_field['field_type'] ] : null;
		?>
        <div data-id="closed" class="dcf-toggle dcf-toggle--normal">
            <span class="dcf-toggle-title">
                <?php
                if ( ! empty( $_type['icon'] ) ) {
	                echo '<span class="dcf-toggle-title--icon"><i class="' . $_type['icon'] . '"></i></span>';
                }
                echo '<span class="dcf-toggle-title--label">' . esc_html( $_field['field_title'] ) . '</span>';
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
					$settings = array(
						'field_type'         => array(
							'type'        => 'hidden',
							'id'          => 'field_type',
							'group'       => 'field',
							'group_class' => 'dcf-input-group col-field_type',
							'position'    => $_field_number,
							'meta_key'    => '_contact_form_fields',
						),
						'field_title'        => array(
							'type'        => 'text',
							'id'          => 'field_title',
							'group'       => 'field',
							'meta_key'    => '_contact_form_fields',
							'group_class' => 'dcf-input-group col-field_title',
							'position'    => $_field_number,
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
							'position'    => $_field_number,
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
							'position'    => $_field_number,
							'meta_key'    => '_contact_form_fields',
							'label'       => __( 'Required Field', 'dialog-contact-form' ),
							'description' => __( 'Check this option to mark the field required. A form will not submit unless all required fields are provided.',
								'dialog-contact-form' ),
							'default'     => $is_required_field,
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
							'position'    => $_field_number,
							'meta_key'    => '_contact_form_fields',
							'label'       => __( 'Add options', 'dialog-contact-form' ),
							'description' => __( 'One option per line.', 'dialog-contact-form' ),
							'rows'        => 8,
						),
						'number_min'         => array(
							'type'        => 'number',
							'id'          => 'number_min',
							'group'       => 'field',
							'group_class' => 'dcf-input-group col-number_min',
							'position'    => $_field_number,
							'meta_key'    => '_contact_form_fields',
							'step'        => '0.01',
							'label'       => __( 'Minimum Value', 'dialog-contact-form' ),
							'description' => __( 'Specifies the minimum value allowed. (optional)', 'dialog-contact-form' ),
						),
						'number_max'         => array(
							'type'        => 'number',
							'id'          => 'number_max',
							'group'       => 'field',
							'group_class' => 'dcf-input-group col-number_max',
							'position'    => $_field_number,
							'meta_key'    => '_contact_form_fields',
							'step'        => '0.01',
							'label'       => __( 'Maximum Value', 'dialog-contact-form' ),
							'description' => __( 'Specifies the maximum value allowed. (optional)', 'dialog-contact-form' ),
						),
						'number_step'        => array(
							'type'        => 'number',
							'id'          => 'number_step',
							'group'       => 'field',
							'group_class' => 'dcf-input-group col-number_step',
							'position'    => $_field_number,
							'meta_key'    => '_contact_form_fields',
							'step'        => '0.01',
							'label'       => __( 'Step Number', 'dialog-contact-form' ),
							'description' => __( 'For allowing decimal values set step value (e.g. "0.01" to allow decimals to two decimal places).',
								'dialog-contact-form' ),
						),
						'field_value'        => array(
							'type'        => 'text',
							'id'          => 'field_value',
							'group'       => 'field',
							'group_class' => 'dcf-input-group col-field_value',
							'position'    => $_field_number,
							'meta_key'    => '_contact_form_fields',
							'label'       => __( 'Default Value', 'dialog-contact-form' ),
							'description' => __( 'Define field default value.', 'dialog-contact-form' ),
						),
						'field_class'        => array(
							'type'        => 'text',
							'id'          => 'field_class',
							'group'       => 'field',
							'group_class' => 'dcf-input-group col-field_class',
							'position'    => $_field_number,
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
							'position'    => $_field_number,
							'meta_key'    => '_contact_form_fields',
							'label'       => __( 'Field Width', 'dialog-contact-form' ),
							'description' => __( 'Set field length.', 'dialog-contact-form' ),
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
							'position'    => $_field_number,
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
							'position'    => $_field_number,
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
							'position'    => $_field_number,
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
							'position'    => $_field_number,
							'meta_key'    => '_contact_form_fields',
							'label'       => __( 'Min. Date', 'dialog-contact-form' ),
						),
						'max_date'           => array(
							'type'        => 'date',
							'id'          => 'max_date',
							'group'       => 'field',
							'group_class' => 'dcf-input-group col-max_date',
							'position'    => $_field_number,
							'meta_key'    => '_contact_form_fields',
							'label'       => __( 'Max. Date', 'dialog-contact-form' ),
						),
						'native_html5'       => array(
							'type'        => 'buttonset',
							'id'          => 'native_html5',
							'group'       => 'field',
							'group_class' => 'dcf-input-group col-native_html5',
							'position'    => $_field_number,
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
							'position'    => $_field_number,
							'meta_key'    => '_contact_form_fields',
							'label'       => __( 'Max. File Size', 'dialog-contact-form' ),
							'description' => __( 'If you need to increase max upload size please contact your hosting.',
								'dialog-contact-form' ),
						),
						'allowed_file_types' => array(
							'type'        => 'mime_type',
							'id'          => 'allowed_file_types',
							'group'       => 'field',
							'group_class' => 'dcf-input-group col-allowed_file_types',
							'position'    => $_field_number,
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
							'position'    => $_field_number,
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
							'position'    => $_field_number,
							'meta_key'    => '_contact_form_fields',
							'label'       => __( 'Rows', 'dialog-contact-form' ),
						),
						'autocomplete'       => array(
							'type'        => 'select',
							'id'          => 'autocomplete',
							'group'       => 'field',
							'group_class' => 'dcf-input-group col-autocomplete',
							'position'    => $_field_number,
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
							'position'    => $_field_number,
							'meta_key'    => '_contact_form_fields',
							'label'       => __( 'HTML', 'dialog-contact-form' ),
							'rows'        => 5,
						),
						// @depreciated
						'validation'         => array(
							'type'        => 'checkbox',
							'id'          => 'validation',
							'group'       => 'field',
							'group_class' => 'dcf-input-group col-validation',
							'position'    => $_field_number,
							'meta_key'    => '_contact_form_fields',
							'label'       => __( 'Validation', 'dialog-contact-form' ),
							'options'     => Utils::validation_rules(),
						),
						// @depreciated
						'error_message'      => array(
							'type'        => 'text',
							'id'          => 'error_message',
							'group'       => 'field',
							'group_class' => 'dcf-input-group col-error_message',
							'position'    => $_field_number,
							'meta_key'    => '_contact_form_fields',
							'label'       => __( 'Error Message', 'dialog-contact-form' ),
							'description' => __( 'Insert the error message for validation. The length of message must be 10 characters or more. Leave blank for default message.',
								'dialog-contact-form' ),
						),
					);

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
	<?php } ?>
</div>
