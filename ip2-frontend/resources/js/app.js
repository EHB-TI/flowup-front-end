require('./bootstrap');

window.Vue = require('vue').default;

//Imports
import Vue from 'vue';
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';
import VueRouter from 'vue-router';
import routes from './routes.js';
import axios from 'axios';
import VueAxios from 'vue-axios';
import { Datetime } from 'vue-datetime';
import Vue2Editor from "vue2-editor";
import Antd from 'ant-design-vue';

import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import 'vue-datetime/dist/vue-datetime.css'
import 'ant-design-vue/dist/antd.css';


//Components global registration
Vue.component('sidebar', require('./components/partials/SideBar.vue').default);
Vue.component('events', require('./components/partials/events/Events.vue').default);
Vue.component('searchbar', require('./components/partials/SearchBar.vue').default);
Vue.component('datetime', Datetime);
Vue.component('pagination', require('laravel-vue-pagination'));

Vue.use(VueAxios, axios)
Vue.use(VueRouter)
Vue.use(BootstrapVue)
Vue.use(IconsPlugin)
Vue.use(Datetime)
Vue.use(Vue2Editor)
Vue.use(Antd);


Vue.prototype.$api=process.env.MIX_API_CONN


const app = new Vue({
    el: '#app',
    router: new VueRouter(routes),
});
