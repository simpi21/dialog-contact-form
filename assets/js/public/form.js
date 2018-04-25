/** @global DialogContactForm */
(function () {
    "use strict";

    var formData, request, allFields, fields,
        control, error, dcfSuccess, dcfError,
        submitBtn, field_name, messages, i,
        config = window.DialogContactForm || {
            ajaxurl: '',
            nonce: '',
            errorColor: '#f44336'
        };

    var settings = {
        selector: 'dcf-form',
        fieldClass: 'dcf-has-error',
        errorClass: 'dcf-error-message',
        loadingClass: 'is-loading'
    };

    // Stop working if formData is not supported
    if (!window.FormData) {
        return;
    }

    var showServerError = function (form, errors) {
        var vMessages,
            field_name,
            fields,
            control,
            messages,
            error;

        // Get error message and print on error div
        if (errors.message) {
            form.querySelector('.dcf-error').innerHTML = '<p>' + errors.message + '</p>';
        }

        // Loop through all fields and print field error message if any
        vMessages = errors.validation && typeof errors.validation === 'object' ? errors.validation : {};
        for (field_name in vMessages) {
            if (vMessages.hasOwnProperty(field_name)) {
                fields = form.querySelector('[name="' + field_name + '"]');
                control = fields.closest('.dcf-control');
                messages = vMessages[field_name];
                if (messages[0]) {
                    fields.classList.add(settings.fieldClass);
                    error = '<div class="' + settings.errorClass + '">' + messages[0] + '</div>';
                    control.insertAdjacentHTML('beforeend', error);
                }
            }
        }
    };

    var hideAllErrors = function (form) {
        // Hide success message if any
        form.querySelector('.dcf-success').innerHTML = '';
        // Hide error message if any
        form.querySelector('.dcf-error').innerHTML = '';

        // Hide field help message if any
        var helpText = form.querySelectorAll('.' + settings.errorClass);
        for (i = 0; i < helpText.length; i++) {
            helpText[i].parentNode.removeChild(helpText[i]);
        }

        // Remove field validation border-color if any
        allFields = form.querySelectorAll('.input, .textarea, .select select');
        for (i = 0; i < allFields.length; i++) {
            allFields[i].classList.remove(settings.fieldClass);
        }
    };

    var submitFormData = function (event) {
        // Only run on forms flagged for validation
        if (!event.target.classList.contains(settings.selector)) return;

        // Prevent default form behavior
        event.preventDefault();

        var form = event.target;
        dcfSuccess = form.querySelector('.dcf-success');
        dcfError = form.querySelector('.dcf-error');
        submitBtn = form.querySelector('.dcf-submit');

        // Add loading class to submit button
        submitBtn.classList.add(settings.loadingClass);

        hideAllErrors(form);

        // Get form fields data
        formData = new FormData(form);
        // Add action params with form data
        formData.append('action', 'dcf_submit_form');
        // Add nonce field with form data
        formData.append('nonce', config.nonce);

        request = new XMLHttpRequest();

        // Define what happens on successful data submission
        request.addEventListener("load", function (event) {
            // Remove loading class from submit button
            submitBtn.classList.remove(settings.loadingClass);

            var xhr = event.target,
                response = JSON.parse(xhr.responseText);

            if (xhr.status >= 200 && xhr.status < 300) {
                // Get success message and print on success div
                if (response.message) {
                    dcfSuccess.innerHTML = '<p>' + response.message + '</p>';
                }
                // Remove form fields value
                form.reset();
            } else {
                showServerError(form, response);
            }
        });

        // Set up our request
        request.open("POST", config.ajaxurl, true);

        // The data sent is what the user provided in the form
        request.send(formData);
    };

    document.addEventListener('submit', submitFormData, false);

})();
