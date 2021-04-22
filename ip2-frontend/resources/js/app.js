require('./bootstrap');

window.Vue = require('vue').default;

//Imports
import Vue from 'vue';
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';
import VueRouter from 'vue-router';
import routes from './routes.js';
import axios from 'axios';
import VueAxios from 'vue-axios';

import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

//Components global registration
Vue.component('sidebar', require('./components/partials/SideBar.vue').default);
Vue.component('event', require('./components/partials/events/Event.vue').default);
Vue.component('events', require('./components/partials/events/Events.vue').default);
Vue.component('searchbar', require('./components/partials/SearchBar.vue').default);

Vue.use(VueAxios, axios);
Vue.use(VueRouter);
Vue.use(BootstrapVue)
Vue.use(IconsPlugin)

const app = new Vue({
    el: '#app',
    router: new VueRouter(routes)
});
