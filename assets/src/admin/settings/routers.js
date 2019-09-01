import Vue from 'vue';
import VueRouter from 'vue-router';
import Settings from "./Settings";

Vue.use(VueRouter);

const routes = [
    {path: '/', name: 'Settings', component: Settings},
];

export default new VueRouter({
    routes // short for `routes: routes`
});