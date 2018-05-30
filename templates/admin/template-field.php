<?php

use DialogContactForm\Supports\Metabox;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$_field_number = 100;
?>

<template style="display: none" id="shaplaFieldTemplate">
    <div data-id="closed" class="dcf-toggle dcf-toggle--normal">
            <span class="dcf-toggle-title">
                <?php esc_html_e( 'Untitled', 'dialog-contact-form' ); ?>
            </span>
        <div class="dcf-toggle-inner">
            <div class="dcf-toggle-content">
                <p>
                    <button class="button deleteField">
						<?php esc_html_e( 'Delete this field', 'dialog-contact-form' ); ?>
                    </button>
                </p>
				<?php
				Metabox::select( array(
					'id'          => 'field_type',
					'group'       => 'field',
					'group_class' => 'dcf-input-group col-field_type',
					'position'    => $_field_number,
					'meta_key'    => '_contact_form_fields',
					'label'       => __( 'Field Type', 'dialog-contact-form' ),
					'description' => __( 'Select the type for this field.', 'dialog-contact-form' ),
					'input_class' => 'select2 dcf-field-type dcf-input-text',
					'options'     => dcf_available_field_types(),
				) );
				Metabox::text( array(
					'id'          => 'field_title',
					'group'       => 'field',
					'group_class' => 'dcf-input-group col-field_title',
					'position'    => $_field_number,
					'meta_key'    => '_contact_form_fields',
					'input_class' => 'dcf-input-text dcf-field-title',
					'label'       => __( 'Label', 'dialog-contact-form' ),
					'description' => __( 'Enter the label for the field.', 'dialog-contact-form' ),
				) );
				Metabox::text( array(
					'id'          => 'field_id',
					'group'       => 'field',
					'group_class' => 'dcf-input-group col-field_id',
					'position'    => $_field_number,
					'meta_key'    => '_contact_form_fields',
					'input_class' => 'dcf-input-text dcf-field-id',
					'label'       => __( 'Field ID', 'dialog-contact-form' ),
					'description' => __( 'Please make sure the ID is unique and not used elsewhere in this form. This field allows A-z 0-9 & underscore chars without spaces.',
						'dialog-contact-form' ),
				) );
				Metabox::buttonset( array(
					'id'          => 'required_field',
					'group'       => 'field',
					'group_class' => 'dcf-input-group col-required_field',
					'position'    => $_field_number,
					'meta_key'    => '_contact_form_fields',
					'label'       => __( 'Required Field', 'dialog-contact-form' ),
					'description' => __( 'Check this option to mark the field required. A form will not submit unless all required fields are provided.',
						'dialog-contact-form' ),
					'default'     => 'off',
					'options'     => array(
						'off' => esc_html__( 'No', 'dialog-contact-form' ),
						'on'  => esc_html__( 'Yes', 'dialog-contact-form' ),
					),
				) );
				Metabox::textarea( array(
					'id'          => 'options',
					'group'       => 'field',
					'group_class' => 'dcf-input-group col-addOptions',
					'position'    => $_field_number,
					'meta_key'    => '_contact_form_fields',
					'label'       => __( 'Add options', 'dialog-contact-form' ),
					'description' => __( 'One option per line.', 'dialog-contact-form' ),
					'rows'        => 8,
				) );
				Metabox::number_options( array(
					'group'       => 'field',
					'group_class' => 'dcf-input-group col-numberOption',
					'position'    => $_field_number,
					'meta_key'    => '_contact_form_fields',
					'label'       => __( 'Number Option', 'dialog-contact-form' ),
					'description' => __( 'This fields is optional but you can set min value, max value and step. For allowing decimal values set step value (e.g. step="0.01" to allow decimals to two decimal places).',
						'dialog-contact-form' ),
				) );
				Metabox::text( array(
					'id'          => 'field_value',
					'group'       => 'field',
					'group_class' => 'dcf-input-group col-field_value',
					'position'    => $_field_number,
					'meta_key'    => '_contact_form_fields',
					'label'       => __( 'Default Value', 'dialog-contact-form' ),
					'description' => __( 'Define field default value.', 'dialog-contact-form' ),
				) );
				Metabox::text( array(
					'id'          => 'field_class',
					'group'       => 'field',
					'group_class' => 'dcf-input-group col-field_class',
					'position'    => $_field_number,
					'meta_key'    => '_contact_form_fields',
					'label'       => __( 'Field Class', 'dialog-contact-form' ),
					'description' => __( 'Insert additional class(es) (separated by blank space) for more personalization.',
						'dialog-contact-form' ),
				) );
				Metabox::select( array(
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
				) );
				Metabox::checkbox( array(
					'id'          => 'validation',
					'group'       => 'field',
					'group_class' => 'dcf-input-group col-validation',
					'position'    => $_field_number,
					'meta_key'    => '_contact_form_fields',
					'label'       => __( 'Validation', 'dialog-contact-form' ),
					'options'     => dcf_validation_rules(),
				) );
				Metabox::text( array(
					'id'          => 'placeholder',
					'group'       => 'field',
					'group_class' => 'dcf-input-group col-placeholder',
					'position'    => $_field_number,
					'meta_key'    => '_contact_form_fields',
					'input_class' => 'dcf-input-text dcf-field-placeholder',
					'label'       => __( 'Placeholder Text', 'dialog-contact-form' ),
					'description' => __( 'Insert placeholder message.', 'dialog-contact-form' ),
				) );
				Metabox::text( array(
					'id'          => 'error_message',
					'group'       => 'field',
					'group_class' => 'dcf-input-group col-error_message',
					'position'    => $_field_number,
					'meta_key'    => '_contact_form_fields',
					'label'       => __( 'Error Message', 'dialog-contact-form' ),
					'description' => __( 'Insert the error message for validation. The length of message must be 10 characters or more. Leave blank for default message.',
						'dialog-contact-form' ),
				) );
				// Acceptance Field
				Metabox::textarea( array(
					'id'          => 'acceptance_text',
					'group'       => 'field',
					'group_class' => 'dcf-input-group col-acceptance',
					'position'    => $_field_number,
					'meta_key'    => '_contact_form_fields',
					'label'       => __( 'Acceptance Text', 'dialog-contact-form' ),
					'description' => __( 'Insert acceptance text. you can also use inline html markup.',
						'dialog-contact-form' ),
					'default'     => '',
				) );
				Metabox::buttonset( array(
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
				) );
				// Date
				Metabox::date( array(
					'id'          => 'min_date',
					'group'       => 'field',
					'group_class' => 'dcf-input-group col-min_date',
					'position'    => $_field_number,
					'meta_key'    => '_contact_form_fields',
					'label'       => __( 'Min. Date', 'dialog-contact-form' ),
				) );
				Metabox::date( array(
					'id'          => 'max_date',
					'group'       => 'field',
					'group_class' => 'dcf-input-group col-max_date',
					'position'    => $_field_number,
					'meta_key'    => '_contact_form_fields',
					'label'       => __( 'Max. Date', 'dialog-contact-form' ),
				) );
				Metabox::buttonset( array(
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
				) );
				// File Field
				Metabox::file_size( array(
					'id'          => 'max_file_size',
					'group'       => 'field',
					'group_class' => 'dcf-input-group col-max_file_size',
					'position'    => $_field_number,
					'meta_key'    => '_contact_form_fields',
					'label'       => __( 'Max. File Size', 'dialog-contact-form' ),
					'description' => __( 'If you need to increase max upload size please contact your hosting.',
						'dialog-contact-form' ),
					'default'     => '2',
				) );
				Metabox::mime_type( array(
					'id'          => 'allowed_file_types',
					'group'       => 'field',
					'group_class' => 'dcf-input-group col-allowed_file_types',
					'position'    => $_field_number,
					'meta_key'    => '_contact_form_fields',
					'label'       => __( 'Allowed File Types', 'dialog-contact-form' ),
					'description' => __( 'Choose file types.', 'dialog-contact-form' ),
					'multiple'    => true,
				) );
				Metabox::buttonset( array(
					'id'          => 'multiple_files',
					'group'       => 'field',
					'group_class' => 'dcf-input-group col-multiple_files',
					'position'    => $_field_number,
					'meta_key'    => '_contact_form_fields',
					'label'       => __( 'Multiple Files', 'dialog-contact-form' ),
					'default'     => 'off',
					'options'     => array(
						'off' => esc_html__( 'No', 'dialog-contact-form' ),
						'on'  => esc_html__( 'Yes', 'dialog-contact-form' ),
					),
				) );
				?>
            </div>
        </div>
    </div>
</template>
