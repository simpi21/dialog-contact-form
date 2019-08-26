import Vue from 'vue';
import VueRouter from 'vue-router';
import Home from './views/Home.vue';
import Settings from './views/Settings.vue';
import EntriesCounts from "./entries/EntriesCounts";
import EntriesList from "./entries/EntriesList";
import SingleEntry from "./entries/SingleEntry";

Vue.use(VueRouter);

const routes = [
    {path: '/', name: 'EntriesCounts', component: EntriesCounts},
    {path: '/forms/:form_id/entries/:status', name: 'EntriesList', component: EntriesList},
    {path: '/entries/:id', name: 'SingleEntry', component: SingleEntry},
];

export default new VueRouter({
    routes // short for `routes: routes`
});