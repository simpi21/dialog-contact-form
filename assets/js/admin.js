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
        _accordion.find('.col-addOptions').hide();
        _accordion.find('.col-numberOption').hide();
        _accordion.find('.col-field_value').hide();
        _accordion.find('.col-acceptance').hide();
        _accordion.find('.col-min_date').hide();
        _accordion.find('.col-max_date').hide();
        _accordion.find('.col-native_html5').hide();
        _accordion.find('.col-max_file_size').hide();
        _accordion.find('.col-allowed_file_types').hide();
        _accordion.find('.col-multiple_files').hide();
        _accordion.find('.col-rows').hide();

        _accordion.find('.col-validation').hide();
        _accordion.find('.col-error_message').hide();

        if (_value === 'acceptance') {
            _accordion.find('.col-placeholder').hide();
            _accordion.find('.col-field_class').slideUp('fast');
            _accordion.find('.col-acceptance').slideDown('fast');
        }

        if (_value === 'checkbox') {
            _accordion.find('.col-placeholder').hide();
            _accordion.find('.col-field_class').slideUp('fast');
            _accordion.find('.col-required_field').slideUp('fast');
            _accordion.find('.col-addOptions').slideDown('fast');
        }

        if (_value === 'date') {
            _accordion.find('.col-min_date').slideDown('fast');
            _accordion.find('.col-max_date').slideDown('fast');
            _accordion.find('.col-native_html5').slideDown('fast');
        }

        if (_value === 'time') {
            _accordion.find('.col-native_html5').slideDown('fast');
        }

        if (_value === 'file') {
            _accordion.find('.col-placeholder').hide();
            _accordion.find('.col-max_file_size').slideDown('fast');
            _accordion.find('.col-allowed_file_types').slideDown('fast');
            _accordion.find('.col-multiple_files').slideDown('fast');
        }

        if (_value === 'hidden') {
            _accordion.find('.col-placeholder').slideUp('fast');
            _accordion.find('.col-field_class').slideUp('fast');
            _accordion.find('.col-required_field').slideUp('fast');
            _accordion.find('.col-field_width').slideUp('fast');
            _accordion.find('.col-field_value').slideDown('fast');
        }

        if (_value === 'number') {
            _accordion.find('.col-numberOption').slideDown('fast');
        }

        if (_value === 'textarea') {
            _accordion.find('.col-rows').slideDown('fast');
        }

        if ($.inArray(_value, whiteList) >= 0) {
            _accordion.find('.col-addOptions').slideDown('fast');
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
        fieldList.find(".dcf-date-picker").each(function () {
            $(this).datepicker({
                dateFormat: 'yy-mm-dd'
            });
        });
        fieldList.find('.dcf-field-type').each(function () {
            showConditionalFields.call(this);
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

    // Datepicker
    $(document).find(".dcf-date-picker").each(function () {
        $(this).datepicker({
            dateFormat: 'yy-mm-dd'
        });
    });

    // Select2
    $(document).ready(function () {
        $('select.select2').select2();

        $('#actions_after_submit_actions')
            .select2()
            .on("select2:select", function (e) {
                var selections = $(this).val();
                $.each(selections, function (index, value) {
                    $('#action-' + value).slideDown('fast');
                });
            })
            .on("select2:unselect", function (e) {
                var selection = e.params.data.id;
                $('#action-' + selection).slideUp('fast');
            });
    });

    // Action --- Redirect
    function processRedirectAction(redirect) {
        if ('page' === redirect) {
            $('.col-redirect-url').slideUp('first');
            $('.col-redirect-page_id').slideDown('first');
        } else if ('url' === redirect) {
            $('.col-redirect-page_id').slideUp('first');
            $('.col-redirect-url').slideDown('first');
        } else {
            $('.col-redirect-page_id').slideUp('first');
            $('.col-redirect-url').slideUp('first');
        }
    }

    $(document).on('change', '#redirect_redirect_to', function () {
        var redirect = $(this).find(":selected").val();
        processRedirectAction(redirect);
    });

    $(document).ready(function () {
        var redirect = $('#redirect_redirect_to').find(":selected").val();
        processRedirectAction(redirect);
    });

    // Action --- MailChimp
    function processMailChimpAction(value) {
        if ('custom' === value) {
            $('.col-mailchimp_api_key').slideDown('first');
        } else {
            $('.col-mailchimp_api_key').slideUp('first');
        }
    }

    $(document).on('change', '#mailchimp_mailchimp_api_key_source', function () {
        var source = $(this).find(":selected").val();
        processMailChimpAction(source);
    });


})(jQuery);
