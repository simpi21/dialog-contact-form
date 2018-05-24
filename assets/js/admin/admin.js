(function ($) {
    'use strict';

    var fieldList = $('#shaplaFieldList'),
        whiteList = ['checkbox', 'radio', 'select'],
        confirmDelete,
        _this,
        _value,
        _accordion,
        template;

    /**
     * Update validation field name
     */
    function updateValidationFieldName() {
        fieldList.find('.dcf-toggle').each(function (index) {
            $(this).find('input,textarea,select').each(function () {
                $(this).attr('name', $(this).attr('name').replace(/\[\d+\]/g, '[' + index + ']'));
            });
        });
    }

    /**
     * Show or Hide field as per field type
     */
    function showConditionalFields() {
        _this = $(this);
        _value = _this.find(":selected").val();
        _accordion = _this.closest('.dcf-toggle');

        /**
         * Hide validation field
         * @since 3.0.0
         */
        _accordion.find('.col-validation').hide();
        _accordion.find('.col-error_message').hide();
        _accordion.find('.col-field_value').hide();

        if ($.inArray(_value, whiteList) >= 0) {
            _accordion.find('.col-addOptions').slideDown('fast');
        } else {
            _accordion.find('.col-addOptions').slideUp('fast');
        }

        if (_value === 'number') {
            _accordion.find('.col-numberOption').slideDown('fast');
        } else {
            _accordion.find('.col-numberOption').slideUp('fast');
        }

        if (_value === 'acceptance') {
            _accordion.find('.col-placeholder').slideUp('fast');
            _accordion.find('.col-field_value').slideUp('fast');
            _accordion.find('.col-field_class').slideUp('fast');

            _accordion.find('.col-acceptance').slideDown('fast');
        } else {
            _accordion.find('.col-acceptance').slideUp('fast');
        }
    }

    // Add Form Field from Field Template
    $('#addFormField').on('click', function (event) {
        event.preventDefault();
        template = $('#shaplaFieldTemplate').html();
        fieldList.append(template);
        fieldList.find(".dcf-toggle").each(function () {
            $(this).accordion({
                header: '.dcf-toggle-title',
                collapsible: true,
                heightStyle: "content",
                active: false
            });
        });
        updateValidationFieldName();
    });

    // Delete Field
    fieldList.on('click', '.deleteField', function (event) {
        event.preventDefault();
        confirmDelete = confirm("Are you sure to delete this field?");
        if (confirmDelete === true) {
            $(this).closest('.dcf-toggle').remove();
            updateValidationFieldName();
        }
    });

    // Update field title
    fieldList.on('keydown keyup', '.dcf-field-title', function () {
        _this = $(this);
        _value = _this.val();
        _accordion = _this.closest('.dcf-toggle');
        // Set field title as accordion header
        _accordion.find('.dcf-toggle-title').text(_value);
        // Set field title as placeholder text
        _accordion.find('.dcf-field-placeholder').val(_value);
        // Set field title as field id
        _accordion.find('.dcf-field-id')
            .val(_value.replace(/[\W_]+/g, "_").toLowerCase())
    });

    // Show Option for Select, Radio and Checkbox
    fieldList.on('change', '.dcf-field-type', function () {
        showConditionalFields.call(this);
    });

    $(document).ready(function () {
        fieldList.find('.dcf-field-type').each(function () {
            showConditionalFields.call(this);
        });
    });

    // Make Form Fields Sortable
    fieldList.sortable({
        placeholder: "ui-state-highlight",
        stop: function () {
            updateValidationFieldName();
        }
    });

    // Accordion
    $(document).find(".dcf-toggle").each(function () {
        if ($(this).data('id') === 'closed') {
            $(this).accordion({
                header: '.dcf-toggle-title',
                collapsible: true,
                heightStyle: "content",
                active: false
            });
        } else {
            $(this).accordion({
                header: '.dcf-toggle-title',
                collapsible: true,
                heightStyle: "content"
            });
        }
    });


    $("#dcf-metabox-tabs").tabs();

    // WordPress ColorPicker
    $('.dcf-colorpicker').each(function () {
        $(this).wpColorPicker({
            palettes: [
                '#f5f5f5',
                '#212121',
                '#00d1b2',
                '#009688',
                '#2196f3',
                '#4caf50',
                '#ffc107',
                '#f44336'
            ]
        });
    });

})(jQuery);
