import Vue from 'vue';
import VueRouter from 'vue-router';
import Home from './views/Home.vue';
import Entries from './views/Entries.vue';
import Entry from './views/Entry.vue';
import Settings from './views/Settings.vue';
import Form from './views/Form.vue';

Vue.use(VueRouter);

const routes = [
	{path: '/', name: 'Home', component: Home},
	{path: '/forms/:form_id/edit', name: 'Form', component: Form},
	{path: '/entries', name: 'Entries', component: Entries},
	{path: '/entries/:form_id/:status', name: 'Entry', component: Entry},
	{path: '/settings', name: 'Settings', component: Settings},
];

export default new VueRouter({
	routes // short for `routes: routes`
});
