(function () {
    'use strict';

    var settings = {

        // Classes and Selectors
        selector: 'dcf-form',
        fieldClass: 'dcf-has-error',
        errorClass: 'dcf-error-message',

        // Messages
        messageValueMissing: 'Please fill out this field.',
        messageValueMissingSelect: 'Please select a value.',
        messageValueMissingSelectMulti: 'Please select at least one value.',
        messageTypeMismatchEmail: 'Please enter an email address.',
        messageTypeMismatchURL: 'Please enter a URL.',
        messageTooShort: 'Please lengthen this text to {minLength} characters or more. You are currently using {length} characters.',
        messageTooLong: 'Please shorten this text to no more than {maxLength} characters. You are currently using {length} characters.',
        messagePatternMismatch: 'Please match the requested format.',
        messageBadInput: 'Please enter a number.',
        messageStepMismatch: 'Please select a valid value.',
        messageRangeOverflow: 'Please select a value that is no more than {max}.',
        messageRangeUnderflow: 'Please select a value that is no less than {min}.',
        messageGeneric: 'The value you entered for this field is invalid.',

        disableSubmit: true
    };

    /**
     * Validate the field
     * @param field
     * @returns {string}
     */
    var hasError = function (field) {

        // Merge user options with existing settings or defaults
        var localSettings = settings;

        // Don't validate submits, buttons, file and reset inputs, and disabled fields
        if (field.disabled || field.type === 'file' || field.type === 'reset' || field.type === 'submit' || field.type === 'button') return;

        // Get validity
        var validity = field.validity;

        // If valid, return null
        if (validity.valid) return;

        // If field is required and empty
        // if (validity.valueMissing) return 'Please fill out this field.';
        if (validity.valueMissing) {
            if (field.type === 'select-multiple') return localSettings.messageValueMissingSelectMulti;
            if (field.type === 'select-one') return localSettings.messageValueMissingSelect;
            return localSettings.messageValueMissing;
        }

        // If not the right type
        if (validity.typeMismatch) {

            // Email
            if (field.type === 'email') return localSettings.messageTypeMismatchEmail;

            // URL
            if (field.type === 'url') return localSettings.messageTypeMismatchURL;

        }

        // If too short
        if (validity.tooShort) return localSettings.messageTooShort.replace('{minLength}', field.getAttribute('minLength')).replace('{length}', field.value.length);

        // If too long
        if (validity.tooLong) return localSettings.messageTooLong.replace('{minLength}', field.getAttribute('maxLength')).replace('{length}', field.value.length);

        // If number input isn't a number
        if (validity.badInput) return localSettings.messageBadInput;

        // If a number value doesn't match the step interval
        if (validity.stepMismatch) return localSettings.messageStepMismatch;

        // If a number field is over the max
        if (validity.rangeOverflow) return localSettings.messageRangeOverflow.replace('{max}', field.getAttribute('max'));

        // If a number field is below the min
        if (validity.rangeUnderflow) return localSettings.messageRangeUnderflow.replace('{min}', field.getAttribute('min'));

        // If pattern doesn't match
        if (validity.patternMismatch) {

            // If pattern info is included, return custom error
            if (field.hasAttribute('title')) return field.getAttribute('title');

            // Otherwise, generic error
            return localSettings.messagePatternMismatch;

        }

        // If all else fails, return a generic catchall error
        return localSettings.messageGeneric;

    };


    /**
     * Show an error message
     *
     * @param field
     * @param error
     */
    var showError = function (field, error) {

        // Merge user options with existing settings or defaults
        var localSettings = settings;

        // Add error class to field
        field.classList.add(localSettings.fieldClass);

        // If the field is a radio button and part of a group, error all and get the last item in the group
        if ((field.type === 'radio' || field.type === 'checkbox') && field.name) {
            var group = document.getElementsByName(field.name);
            if (group.length > 0) {
                for (var i = 0; i < group.length; i++) {
                    // Only check fields in current form
                    if (group[i].form !== field.form) continue;
                    group[i].classList.add(localSettings.fieldClass);
                }
                field = group[group.length - 1];
            }
        }

        // Get field id or name
        var id = field.id || field.name;
        if (!id) return;

        // Check if error message field already exists
        // If not, create one
        var message = field.form.querySelector('.' + localSettings.errorClass + '#error-for-' + id);
        if (!message) {
            message = document.createElement('div');
            message.className = localSettings.errorClass;
            message.id = 'error-for-' + id;

            // If the field is a radio button or checkbox, insert error after the label
            var label;
            if (field.type === 'radio' || field.type === 'checkbox') {
                label = field.form.querySelector('label[for="' + id + '"]') || field.parentNode;
                if (label) {
                    label.parentNode.insertBefore(message, label.nextSibling);
                }
            }

            // Otherwise, insert it after the field
            if (!label) {
                field.parentNode.insertBefore(message, field.nextSibling);
            }

        }

        // Add ARIA role to the field
        field.setAttribute('aria-describedby', 'error-for-' + id);

        // Update error message
        message.innerHTML = error;

        // Show error message
        message.style.display = 'block';
        message.style.visibility = 'visible';
    };


    /**
     * Remove the error message
     *
     * @param field
     */
    var removeError = function (field) {

        // Merge user options with existing settings or defaults
        var localSettings = settings;

        // Remove error class to field
        field.classList.remove(localSettings.fieldClass);

        // Remove ARIA role from the field
        field.removeAttribute('aria-describedby');

        // If the field is a radio button and part of a group, remove error from all and get the last item in the group
        if (field.type === 'radio' && field.name) {
            var group = document.getElementsByName(field.name);
            if (group.length > 0) {
                for (var i = 0; i < group.length; i++) {
                    // Only check fields in current form
                    if (group[i].form !== field.form) continue;
                    group[i].classList.remove(localSettings.fieldClass);
                }
                field = group[group.length - 1];
            }
        }

        // Get field id or name
        var id = field.id || field.name;
        if (!id) return;


        // Check if an error message is in the DOM
        var message = field.form.querySelector('.' + localSettings.errorClass + '#error-for-' + id + '');
        if (!message) return;

        // If so, hide it
        message.innerHTML = '';
        message.style.display = 'none';
        message.style.visibility = 'hidden';
    };

    /**
     * Add the `novalidate` attribute to all forms
     * @private
     * @param {Boolean} remove  If true, remove the `novalidate` attribute
     */
    var addNoValidate = function (remove) {
        var forms = document.querySelectorAll(settings.selector);
        for (var i = 0; i < forms.length; i++) {
            if (remove) {
                forms[i].removeAttribute('novalidate');
                continue;
            }
            forms[i].setAttribute('novalidate', true);
        }
    };

    /**
     * Check field validity when it loses focus
     * @private
     * @param  {Event} event The blur event
     */
    var blurHandler = function (event) {

        // Only run if the field is in a form to be validated
        if (!event.target.form || !event.target.form.classList.contains(settings.selector)) return;

        // Validate the field
        var error = hasError(event.target);

        // If there's an error, show it
        if (error) {
            showError(event.target, error);
            return;
        }

        // Otherwise, remove any existing error message
        removeError(event.target);
    };

    /**
     * Check radio and checkbox field validity when clicked
     * @private
     * @param  {Event} event The click event
     */
    var clickHandler = function (event) {

        // Only run if the field is in a form to be validated
        if (!event.target.form || !event.target.form.classList.contains(settings.selector)) return;

        // Only run if the field is a checkbox or radio
        var type = event.target.getAttribute('type');
        if (!(type === 'checkbox' || type === 'radio')) return;

        // Validate the field
        var error = hasError(event.target);

        // If there's an error, show it
        if (error) {
            showError(event.target, error);
            return;
        }

        // Otherwise, remove any errors that exist
        removeError(event.target);

    };

    /**
     * Check all fields on submit
     * @private
     * @param  {Event} event  The submit event
     */
    var submitHandler = function (event) {

        // Only run on forms flagged for validation
        if (!event.target.classList.contains(settings.selector)) return;

        // Get all of the form elements
        var fields = event.target.elements;

        // Validate each field
        // Store the first field with an error to a variable so we can bring it into focus later
        var hasErrors;
        for (var i = 0; i < fields.length; i++) {
            var error = hasError(fields[i]);
            if (error) {
                showError(fields[i], error);
                if (!hasErrors) {
                    hasErrors = fields[i];
                }
            }
        }

        // Prevent form from submitting if there are errors or submission is disabled
        if (hasErrors || settings.disableSubmit) {
            event.preventDefault();
        }

        // If there are errors, focus on first element with error
        if (hasErrors) {
            hasErrors.focus();
        }
    };

    /**
     * Listen to all blur events
     */
    document.addEventListener('blur', blurHandler, true);
    document.addEventListener('click', clickHandler, true);
    document.addEventListener('submit', submitHandler, false);
})();