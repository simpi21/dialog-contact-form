/** @global DialogContactForm */
(function () {
    "use strict";

    var forms,
        helpText,
        formData,
        request,
        allFields,
        fields,
        errors,
        error,
        success,
        dcfSuccess,
        dcfError,
        submitBtn,
        validationMessages,
        field_name,
        messages,
        i;

    // Stop working if formData is not supported
    if (!window.FormData) {
        return;
    }

    // Get all contact form
    forms = document.querySelectorAll('.dcf-form');
    Array.prototype.forEach.call(forms, function (form) {
        form.addEventListener('submit', function (event) {
            // Prevent default form behavior
            event.preventDefault();

            dcfSuccess = form.querySelector('.dcf-response > .dcf-success');
            dcfError = form.querySelector('.dcf-response > .dcf-error');
            submitBtn = form.querySelector('.dcf-submit');

            // Add loading class to submit button
            submitBtn.classList.add('is-loading');

            // Hide success message if any
            dcfSuccess.innerHTML = '';
            // Hide error message if any
            dcfError.innerHTML = '';

            // Hide field help message if any
            helpText = form.querySelectorAll('.help');
            for (i = 0; i < helpText.length; i++) {
                helpText[i].parentNode.removeChild(helpText[i]);
            }

            // Remove field validation border-color if any
            allFields = form.querySelectorAll('.input, .textarea, .select select');
            for (i = 0; i < allFields.length; i++) {
                allFields[i].style.borderColor = '';
            }

            // Get form fields data
            formData = new FormData(form);
            // Add action params with form data
            formData.append('action', 'dcf_submit_form');
            // Add nonce field with form data
            formData.append('nonce', DialogContactForm.nonce);

            request = new XMLHttpRequest();
            request.open("POST", DialogContactForm.ajaxurl, true);
            request.onload = function () {
                if (request.status === 200) {
                    // Remove loading class from submit button
                    submitBtn.classList.remove('is-loading');
                    // Get success message and print on success div
                    success = JSON.parse(request.responseText);
                    dcfSuccess.innerHTML = '<p>' + success.message + '</p>';
                    // Remove form fields value
                    form.reset();
                } else {
                    // Remove loading class from submit button
                    submitBtn.classList.remove('is-loading');
                    // Get error message
                    errors = JSON.parse(request.responseText);

                    if (errors.message) {
                        dcfError.innerHTML = '<p>' + errors.message + '</p>';
                    }

                    // Loop through all fields and print field error message if any
                    validationMessages = errors.validation && typeof errors.validation === 'object' ? errors.validation : {};
                    for (field_name in validationMessages) {
                        if (validationMessages.hasOwnProperty(field_name)) {
                            fields = form.querySelector('[name="' + field_name + '"]');
                            messages = validationMessages[field_name];
                            if (messages[0]) {
                                error = '<span class="help is-danger">' + messages[0] + '</span>';
                                fields.style.borderColor = '#f44336';
                                fields.insertAdjacentHTML('afterend', error);
                            }
                        }
                    }
                }
            };
            request.send(formData);
        }, false);
    });
})();

(function () {
    'use strict';

    var target,
        modal,
        modals = document.querySelectorAll('[data-toggle="modal"]'),
        dismiss = document.querySelectorAll('[data-dismiss="modal"]');

    Array.prototype.forEach.call(modals, function (el, i) {
        el.addEventListener('click', function (event) {
            event.preventDefault();
            target = el.getAttribute('data-target');
            modal = document.querySelector(target);
            if (!!modal) {
                modal.classList.add('is-active');
            }
        });
    });

    Array.prototype.forEach.call(dismiss, function (el, i) {
        el.addEventListener('click', function (event) {
            event.preventDefault();
            var closestModal = el.closest('.modal');
            if (!!closestModal) {
                closestModal.classList.remove('is-active');
            }
        });
    });
})();
/**
 * Polyfill for browsers that do not support Element.closest(), but
 * carry support for element.matches()
 * (or a prefixed equivalent, meaning IE9+)
 */
if (!Element.prototype.matches) {
    Element.prototype.matches = Element.prototype.msMatchesSelector || Element.prototype.webkitMatchesSelector;
}

if (!Element.prototype.closest) {
    Element.prototype.closest = function (s) {
        var el = this;
        if (!document.documentElement.contains(el)) return null;
        do {
            if (el.matches(s)) return el;
            el = el.parentElement;
        } while (el !== null);
        return null;
    };
}
