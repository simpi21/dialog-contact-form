(function ($) {
    'use strict';

    var fieldList = $('#shaplaFieldList'),
        confirmDelete,
        _this,
        _value,
        _accordion;

    /**
     * Update validation field name
     */
    function updateValidationFieldName() {
        fieldList.find('.dcf-toggle').each(function (index) {
            $(this).find('input,textarea,select').each(function () {
                var nameAttr = $(this).attr('name');
                if (!!nameAttr) {
                    $(this).attr('name', nameAttr.replace(/\[\d+\]/g, '[' + index + ']'));
                }
            });
        });
    }

    // Draggable
    $(".dcf-fields-list").draggable({
        connectToSortable: "#shaplaFieldList",
        helper: "clone",
        stop: function (event, ui) {
            $.ajax({
                method: 'POST',
                url: ajaxurl,
                data: {
                    action: 'dcf_field_settings',
                    type: $(this).data('type'),
                },
                success: function (template) {
                    ui.helper.replaceWith(template);

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
                    updateValidationFieldName();
                }
            });
        }
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
        _accordion.find('.dcf-toggle-title--label').text(_value);
        // Set field title as placeholder text
        _accordion.find('.dcf-field-placeholder').val(_value);
        // Set field title as field id
        _accordion.find('.dcf-field-id').val(_value.replace(/[\W_]+/g, "_").toLowerCase());
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

        $('#_contact_form_actions')
            .select2()
            .on("select2:select", function () {
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

    // Add new Form
    $('body.post-type-dialog-contact-form').on('click', '.page-title-action', function (e) {
        e.preventDefault();
        $('#modal-form-template').addClass('is-active');
    });

    $(document).on('click', '[data-dismiss="modal"]', function (e) {
        e.preventDefault();
        $(this).closest('.modal').removeClass('is-active');
    });

})(jQuery);
