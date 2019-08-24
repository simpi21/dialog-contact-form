import Vue from 'vue';
import VueRouter from 'vue-router';
import Home from './views/Home.vue';
import Settings from './views/Settings.vue';
import EntriesCounts from "./entries/EntriesCounts";
import EntriesList from "./entries/EntriesList";

Vue.use(VueRouter);

const routes = [
    {path: '/', name: 'EntriesCounts', component: EntriesCounts},
    {path: '/entries/:form_id/:status', name: 'EntriesList', component: EntriesList}
];

export default new VueRouter({
    routes // short for `routes: routes`
});