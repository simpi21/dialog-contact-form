;(function () {
    "use strict";
    var forms,
        form,
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
        i;

    // Stop working if formData is not supported
    if (!window.FormData) {
        return;
    }

    // Get all contact form
    forms = document.querySelectorAll('.dcf-form');
    Array.prototype.forEach.call(forms, function (el) {
        el.addEventListener('submit', function (e) {
            // Prevent default form behavior
            e.preventDefault();

            form = this;
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
                    for (i = 0; i < errors.length; i++) {
                        fields = form.querySelector('[name="' + errors[i].field + '"]');

                        if (errors[i].message[0]) {
                            error = '<span class="help is-danger">' + errors[i].message[0] + '</span>';
                            fields.style.borderColor = '#f44336';
                            fields.insertAdjacentHTML('afterend', error);
                        }
                    }
                }
            };
            request.send(formData);
        }, false);
    });
})();
