(function () {
    'use strict';

    var target,
        modal,
        modals = document.querySelectorAll('[data-toggle="modal"]'),
        dismiss = document.querySelectorAll('[data-dismiss="modal"]');

    if (modals.length < 1) {
        return;
    }
    Array.prototype.forEach.call(modals, function (el, i) {
        el.addEventListener('click', function (event) {
            event.preventDefault();
            target = el.getAttribute('data-target');
            modal = document.querySelector(target);
            if (!modal) {
                return;
            }
            addClass(modal, 'is-active');
        });
    });
    if (dismiss.length < 1) {
        return;
    }
    Array.prototype.forEach.call(dismiss, function (el, i) {
        el.addEventListener('click', function (event) {
            event.preventDefault();
            var closestModal = el.closest('.modal');
            if (!closestModal) {
                return;
            }
            removeClass(modal, 'is-active');
        });
    });

    function hasClass(el, className) {
        if (el.classList) {
            return el.classList.contains(className);
        }
        return !!el.className.match(new RegExp('(\\s|^)' + className + '(\\s|$)'));
    }

    function addClass(el, className) {
        if (el.classList) {
            el.classList.add(className)
        }
        else if (!hasClass(el, className)) {
            el.className += " " + className;
        }
    }

    function removeClass(el, className) {
        if (el.classList) {
            el.classList.remove(className)
        }
        else if (hasClass(el, className)) {
            var reg = new RegExp('(\\s|^)' + className + '(\\s|$)');
            el.className = el.className.replace(reg, ' ');
        }
    }
})();