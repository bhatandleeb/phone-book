
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

 require('./bootstrap');

 window.Vue = require('vue').default;

 import * as VeeValidate from 'vee-validate';
 import { ValidationProvider } from 'vee-validate';

 import "keen-ui/dist/keen-ui.css"
 import KeenUI from 'keen-ui'

 Vue.use(VeeValidate, { fieldsBagName: 'veeFields' });
 Vue.use(KeenUI)
 Vue.component('ValidationProvider', ValidationProvider);

 /**
  * Next, we will create a fresh Vue application instance and attach it to
  * the page. Then, you may begin adding components to this application
  * or customize the JavaScript scaffolding to fit your unique needs.
  */

 Vue.component("contacts", () => import("./components/Contacts"));

 const app = new Vue ({
     el: '#app',
 });
