import Vue from 'vue';
import axios from 'axios';
import App from './App.vue'
import router from './routers.js';
import store from './store.js';
import {modal} from 'shapla-confirm-modal'
import wpMenuFix from "./utils/admin-menu-fix.js";

if (window.dcfSettings.restNonce) {
    axios.defaults.headers.common['X-WP-Nonce'] = window.dcfSettings.restNonce;
}

Vue.use(modal);

let el = document.querySelector('#dialog-contact-form-admin');
if (el) {
    new Vue({el, store, router, render: h => h(App)});
}

// fix the admin menu for the slug "dialog-contact-form-admin"
// wpMenuFix('dialog-contact-form-admin');
