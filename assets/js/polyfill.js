/*
 * classList.js: Cross-browser full element.classList implementation.
 * 1.2.20171210
 *
 * By Eli Grey, http://eligrey.com
 * License: Dedicated to the public domain.
 * See https://github.com/eligrey/classList.js/blob/master/LICENSE.md
 */

/*global self, document, DOMException */

/*! @source http://purl.eligrey.com/github/classList.js/blob/master/classList.js */

if ("document" in self) {

    // Full polyfill for browsers with no classList support
    // Including IE < Edge missing SVGElement.classList
    if (!("classList" in document.createElement("_"))
        || document.createElementNS
        && !("classList" in document.createElementNS("http://www.w3.org/2000/svg", "g"))
    ) {

        (function (view) {

            "use strict";

            if (!('Element' in view)) return;

            var classListProp = "classList",
                protoProp = "prototype",
                elemCtrProto = view.Element[protoProp],
                objCtr = Object,
                strTrim = String[protoProp].trim || function () {
                    return this.replace(/^\s+|\s+$/g, "");
                },
                arrIndexOf = Array[protoProp].indexOf || function (item) {
                    var i = 0, len = this.length;
                    for (; i < len; i++) {
                        if (i in this && this[i] === item) {
                            return i;
                        }
                    }
                    return -1;
                },
                // Vendors: please allow content code to instantiate DOMExceptions
                DOMEx = function (type, message) {
                    this.name = type;
                    this.code = DOMException[type];
                    this.message = message;
                },
                checkTokenAndGetIndex = function (classList, token) {
                    if (token === "") {
                        throw new DOMEx(
                            "SYNTAX_ERR"
                            , "The token must not be empty."
                        );
                    }
                    if (/\s/.test(token)) {
                        throw new DOMEx(
                            "INVALID_CHARACTER_ERR",
                            "The token must not contain space characters."
                        );
                    }
                    return arrIndexOf.call(classList, token);
                },
                ClassList = function (elem) {
                    var trimmedClasses = strTrim.call(elem.getAttribute("class") || ""),
                        classes = trimmedClasses ? trimmedClasses.split(/\s+/) : [],
                        i = 0,
                        len = classes.length;

                    for (; i < len; i++) {
                        this.push(classes[i]);
                    }
                    this._updateClassName = function () {
                        elem.setAttribute("class", this.toString());
                    };
                },
                classListProto = ClassList[protoProp] = [],
                classListGetter = function () {
                    return new ClassList(this);
                };

            // Most DOMException implementations don't allow calling DOMException's toString()
            // on non-DOMExceptions. Error's toString() is sufficient here.
            DOMEx[protoProp] = Error[protoProp];
            classListProto.item = function (i) {
                return this[i] || null;
            };
            classListProto.contains = function (token) {
                return ~checkTokenAndGetIndex(this, token + "");
            };
            classListProto.add = function () {
                var tokens = arguments,
                    i = 0,
                    l = tokens.length,
                    token,
                    updated = false;

                do {
                    token = tokens[i] + "";
                    if (!~checkTokenAndGetIndex(this, token)) {
                        this.push(token);
                        updated = true;
                    }
                }
                while (++i < l);

                if (updated) {
                    this._updateClassName();
                }
            };
            classListProto.remove = function () {
                var tokens = arguments,
                    i = 0,
                    l = tokens.length,
                    token,
                    updated = false,
                    index;

                do {
                    token = tokens[i] + "";
                    index = checkTokenAndGetIndex(this, token);
                    while (~index) {
                        this.splice(index, 1);
                        updated = true;
                        index = checkTokenAndGetIndex(this, token);
                    }
                }
                while (++i < l);

                if (updated) {
                    this._updateClassName();
                }
            };
            classListProto.toggle = function (token, force) {
                var result = this.contains(token),
                    method = result ? force !== true && "remove" : force !== false && "add";

                if (method) {
                    this[method](token);
                }

                if (force === true || force === false) {
                    return force;
                } else {
                    return !result;
                }
            };
            classListProto.replace = function (token, replacement_token) {
                var index = checkTokenAndGetIndex(token + "");
                if (~index) {
                    this.splice(index, 1, replacement_token);
                    this._updateClassName();
                }
            };

            classListProto.toString = function () {
                return this.join(" ");
            };

            if (objCtr.defineProperty) {
                var classListPropDesc = {
                    get: classListGetter,
                    enumerable: true,
                    configurable: true
                };
                try {
                    objCtr.defineProperty(elemCtrProto, classListProp, classListPropDesc);
                } catch (ex) { // IE 8 doesn't support enumerable:true
                    // adding undefined to fight this issue https://github.com/eligrey/classList.js/issues/36
                    // modernie IE8-MSW7 machine has IE8 8.0.6001.18702 and is affected
                    if (ex.number === undefined || ex.number === -0x7FF5EC54) {
                        classListPropDesc.enumerable = false;
                        objCtr.defineProperty(elemCtrProto, classListProp, classListPropDesc);
                    }
                }
            } else if (objCtr[protoProp].__defineGetter__) {
                elemCtrProto.__defineGetter__(classListProp, classListGetter);
            }

        }(self));
    }

    /**
     * There is full or partial native classList support, so just check if we need
     * to normalize the add/remove and toggle APIs.
     */
    (function () {
        "use strict";

        var testElement = document.createElement("_");

        testElement.classList.add("c1", "c2");

        // Polyfill for IE 10/11 and Firefox <26, where classList.add and
        // classList.remove exist but support only one argument at a time.
        if (!testElement.classList.contains("c2")) {
            var createMethod = function (method) {
                var original = DOMTokenList.prototype[method];

                DOMTokenList.prototype[method] = function (token) {
                    var i, len = arguments.length;

                    for (i = 0; i < len; i++) {
                        token = arguments[i];
                        original.call(this, token);
                    }
                };
            };
            createMethod('add');
            createMethod('remove');
        }

        testElement.classList.toggle("c3", false);

        // Polyfill for IE 10 and Firefox <24, where classList.toggle does not
        // support the second argument.
        if (testElement.classList.contains("c3")) {
            var _toggle = DOMTokenList.prototype.toggle;

            DOMTokenList.prototype.toggle = function (token, force) {
                if (1 in arguments && !this.contains(token) === !force) {
                    return force;
                } else {
                    return _toggle.call(this, token);
                }
            };

        }

        // replace() polyfill
        if (!("replace" in document.createElement("_").classList)) {
            DOMTokenList.prototype.replace = function (token, replacement_token) {
                var tokens = this.toString().split(" "),
                    index = tokens.indexOf(token + "");

                if (~index) {
                    tokens = tokens.slice(index);
                    this.remove.apply(this, tokens);
                    this.add(replacement_token);
                    this.add.apply(this, tokens.slice(1));
                }
            }
        }

        testElement = null;
    }());
}
/*!
 * validate v1.1.2: A lightweight form validation script that augments native HTML5 form validation elements and attributes.
 * (c) 2018 Chris Ferdinandi
 * MIT License
 * http://github.com/cferdinandi/validate
 */

;(function (window, document, undefined) {

    'use strict';

    // Make sure that ValidityState is supported in full (all features)
    var supported = function () {
        var input = document.createElement('input');
        return ('validity' in input && 'badInput' in input.validity && 'patternMismatch' in input.validity && 'rangeOverflow' in input.validity && 'rangeUnderflow' in input.validity && 'stepMismatch' in input.validity && 'tooLong' in input.validity && 'tooShort' in input.validity && 'typeMismatch' in input.validity && 'valid' in input.validity && 'valueMissing' in input.validity);
    };

    // Save browser's own implementation if available
    var browserValidityFunctions = (function () {
        var inputValidity = Object.getOwnPropertyDescriptor(HTMLInputElement.prototype, 'validity');
        var buttonValidity = Object.getOwnPropertyDescriptor(HTMLButtonElement.prototype, 'validity');
        var selectValidity = Object.getOwnPropertyDescriptor(HTMLSelectElement.prototype, 'validity');
        var textareaValidity = Object.getOwnPropertyDescriptor(HTMLTextAreaElement.prototype, 'validity');

        var functions = {};
        if (inputValidity) {
            functions.input = inputValidity.get;
        }
        if (buttonValidity) {
            functions.button = buttonValidity.get;
        }
        if (selectValidity) {
            functions.select = selectValidity.get;
        }
        if (textareaValidity) {
            functions.textarea = textareaValidity.get;
        }

        return functions;
    })();

    /**
     * Generate the field validity object
     * @param  {Node} field The field to validate
     * @return {Object}      The validity object
     */
    var getValidityState = function (field) {

        // Variables
        var type = field.getAttribute('type') || field.nodeName.toLowerCase();
        var isNum = type === 'number' || type === 'range';
        var length = field.value.length;
        var valid = true;

        // If radio group, get selected field
        if (field.type === 'radio' && field.name) {
            var group = document.getElementsByName(field.name);
            if (group.length > 0) {
                for (var i = 0; i < group.length; i++) {
                    if (group[i].form === field.form && field.checked) {
                        field = group[i];
                        break;
                    }
                }
            }
        }

        // Run validity checks
        var checkValidity = {
            badInput: (isNum && length > 0 && !/^[-+]?(?:\d+|\d*[.,]\d+)$/.test(field.value)), // value of a number field is not a number
            patternMismatch: (field.hasAttribute('pattern') && length > 0 && new RegExp(field.getAttribute('pattern')).test(field.value) === false), // value does not conform to the pattern
            rangeOverflow: (field.hasAttribute('max') && isNum && field.value > 0 && Number(field.value) > Number(field.getAttribute('max'))), // value of a number field is higher than the max attribute
            rangeUnderflow: (field.hasAttribute('min') && isNum && field.value > 0 && Number(field.value) < Number(field.getAttribute('min'))), // value of a number field is lower than the min attribute
            stepMismatch: (isNum && ((field.hasAttribute('step') && field.getAttribute('step') !== 'any' && Number(field.value) % Number(field.getAttribute('step')) !== 0) || (!field.hasAttribute('step') && Number(field.value) % 1 !== 0))), // value of a number field does not conform to the stepattribute
            tooLong: (field.hasAttribute('maxLength') && field.getAttribute('maxLength') > 0 && length > parseInt(field.getAttribute('maxLength'), 10)), // the user has edited a too-long value in a field with maxlength
            tooShort: (field.hasAttribute('minLength') && field.getAttribute('minLength') > 0 && length > 0 && length < parseInt(field.getAttribute('minLength'), 10)), // the user has edited a too-short value in a field with minlength
            typeMismatch: (length > 0 && ((type === 'email' && !/^([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x22([^\x0d\x22\x5c\x80-\xff]|\x5c[\x00-\x7f])*\x22)(\x2e([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x22([^\x0d\x22\x5c\x80-\xff]|\x5c[\x00-\x7f])*\x22))*\x40([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x5b([^\x0d\x5b-\x5d\x80-\xff]|\x5c[\x00-\x7f])*\x5d)(\x2e([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x5b([^\x0d\x5b-\x5d\x80-\xff]|\x5c[\x00-\x7f])*\x5d))*$/.test(field.value)) || (type === 'url' && !/^(?:(?:https?|HTTPS?|ftp|FTP):\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-zA-Z\u00a1-\uffff0-9]-*)*[a-zA-Z\u00a1-\uffff0-9]+)(?:\.(?:[a-zA-Z\u00a1-\uffff0-9]-*)*[a-zA-Z\u00a1-\uffff0-9]+)*)(?::\d{2,5})?(?:[\/?#]\S*)?$/.test(field.value)))), // value of a email or URL field is not an email address or URL
            valueMissing: (field.hasAttribute('required') && (((type === 'checkbox' || type === 'radio') && !field.checked) || (type === 'select' && (field.selectedIndex === -1 || field.options[field.selectedIndex].value.length < 1)) || (type !== 'checkbox' && type !== 'radio' && type !== 'select' && length < 1))) // required field without a value
        };

        // Run browser's own validation if available
        var fieldTagName = field.tagName.toLowerCase();
        var browserValidity = fieldTagName in browserValidityFunctions ? browserValidityFunctions[fieldTagName].call(field) : {};

        // Check if any errors
        for (var key in checkValidity) {
            if (checkValidity.hasOwnProperty(key)) {
                // If browser has detected an error, adopt it to our validity object
                if (key in browserValidity && browserValidity[key]) {
                    checkValidity[key] = true;
                }

                // If there's an error, change valid value
                if (checkValidity[key]) {
                    valid = false;
                }
            }
        }

        // Add valid property to validity object
        checkValidity.valid = valid;

        // Return object
        return checkValidity;

    };

    // If the full set of ValidityState features aren't supported, polyfill
    if (!supported()) {
        Object.defineProperty(HTMLInputElement.prototype, 'validity', {
            get: function ValidityState() {
                return getValidityState(this);
            },
            configurable: true
        });
        Object.defineProperty(HTMLButtonElement.prototype, 'validity', {
            get: function ValidityState() {
                return getValidityState(this);
            },
            configurable: true
        });
        Object.defineProperty(HTMLSelectElement.prototype, 'validity', {
            get: function ValidityState() {
                return getValidityState(this);
            },
            configurable: true
        });
        Object.defineProperty(HTMLTextAreaElement.prototype, 'validity', {
            get: function ValidityState() {
                return getValidityState(this);
            },
            configurable: true
        });
    }

})(window, document);