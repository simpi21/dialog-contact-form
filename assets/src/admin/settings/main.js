import Vue from 'vue';
import App from "./App";
import store from "./store";
import router from "./routers";

let elSettings = document.querySelector('#dialog-contact-form-settings');
if (elSettings) {
    new Vue({el: elSettings, store, router, render: h => h(App)});
}
