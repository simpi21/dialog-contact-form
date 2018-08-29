import Vue from 'vue';
import App from './App.vue'
import router from './routers.js';
import menuFix from "./utils/admin-menu-fix.js";

jQuery.ajaxSetup({
    beforeSend: function (xhr) {
        xhr.setRequestHeader('X-WP-Nonce', window.dcfApiSettings.nonce);
    }
});

new Vue({
    el: '#dialog-contact-form',
    router: router,
    render: h => h(App)
});

// fix the admin menu for the slug "dialog-contact-form"
menuFix('dialog-contact-form');
