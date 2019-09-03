import Vue from 'vue';
import VueRouter from 'vue-router';
import FormsList from "./FormsList";

Vue.use(VueRouter);

const routes = [
    {path: '/', name: 'FormsList', component: FormsList},
];

export default new VueRouter({
    routes // short for `routes: routes`
});