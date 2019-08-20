(function () {
    'use strict';

    let target, modal,
        modals = document.querySelectorAll('[data-toggle="modal"]'),
        dismiss = document.querySelectorAll('[data-dismiss="modal"]');

    Array.prototype.forEach.call(modals, el => {
        el.addEventListener('click', event => {
            event.preventDefault();
            target = el.getAttribute('data-target');
            modal = document.querySelector(target);
            if (!!modal) {
                modal.classList.add('is-active');
            }
        });
    });

    Array.prototype.forEach.call(dismiss, el => {
        el.addEventListener('click', event => {
            event.preventDefault();
            let closestModal = el.closest('.modal');
            if (!!closestModal) {
                closestModal.classList.remove('is-active');
            }
        });
    });
})();