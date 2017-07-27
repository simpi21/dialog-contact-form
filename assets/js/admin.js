;(function ($) {
    'use strict';

    var fieldList = $('#shaplaFieldList');
    var fieldTemplate = $('#shaplaFieldTemplate');

    // Add Form Field from Field Template
    $('#addFormField').on('click', function (event) {
        event.preventDefault();
        fieldList.append(fieldTemplate.html());
        updateValidationFieldName();
    });

    // Update field title
    fieldList.on('keydown keyup', '[name="field[field_title][]"]', function () {
        var _this = $(this);
        var _value = _this.val();
        var _accordion = _this.closest('.accordion');
        // Set field title as accordion header
        _accordion.find('.accordion-header').text(_value);
        // Set field title as placeholder text
        _accordion.find('[name="field[placeholder][]"]').val(_value);
        // Set field title as field id
        _accordion.find('[name="field[field_id][]"]')
            .val(_value.replace(/[\W_]+/g, "_").toLowerCase())
    });

    // Delete Field
    fieldList.on('click', '.deleteField', function (event) {
        event.preventDefault();
        var r = confirm("Are you sure to delete this field?");
        if (r === true) {
            $(this).closest('.accordion').remove();
        }
    });

    // Show Option for Select, Radio and Checkbox
    fieldList.on('change', '[name="field[field_type][]"]', function () {
        var _this;
        _this = $(this);
        var _accordion = _this.closest('.accordion');
        var whiteList = ['checkbox', 'radio', 'select'];

        if ($.inArray(_this.val(), whiteList) >= 0) {
            _accordion.find('.col-addOptions').slideDown('fast');
        } else {
            _accordion.find('.col-addOptions').slideUp('fast');
        }

        if (_this.val() === 'number') {
            _accordion.find('.col-numberOption').slideDown('fast');
        } else {
            _accordion.find('.col-numberOption').slideUp('fast');
        }
    });

    // Make Form Fields Sortable
    fieldList.sortable({
        placeholder: "ui-state-highlight",
        stop: function () {
            updateValidationFieldName();
        }
    });

    // Accordion
    fieldList.on("click", ".accordion-header", function () {
        $(this).toggleClass('active');
        var panel = $(this).next();

        if (parseInt(panel.css('max-height')) > 0) {
            panel.css('max-height', '0');
        } else {
            panel.css('max-height', panel.prop('scrollHeight') + "px");
        }
    });

    /**
     * Update validation field name
     */
    function updateValidationFieldName() {
        fieldList.find('.accordion').each(function (index) {
            $(this).find('.input-validate').each(function () {
                $(this).attr('name', 'field[validation][' + index + '][]');
            });
        });
    }

})(jQuery);
