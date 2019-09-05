import Vue from 'vue';
import VueRouter from 'vue-router';
import FormsList from "./FormsList";
import EditForm from './EditForm'

Vue.use(VueRouter);

const routes = [
    {path: '/', name: 'FormsList', component: FormsList},
    {path: '/:id/edit', name: 'EditForm', component: EditForm},
];

export default new VueRouter({
    routes // short for `routes: routes`
});