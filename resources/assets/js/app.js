
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
// require('vue2-autocomplete-js/dist/style/vue2-autocomplete.css')

import vSelect from 'vue-select';
// import Autocomplete from 'vue2-autocomplete-js';

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// Vue.component('autocomplete', Autocomplete);

Vue.component('v-select', vSelect);
// Vue.component('tag-edit-form', require('./components/TagEditForm.vue'));
Vue.component('entity-type-select', require('./components/EntityTypeSelect.vue'));
Vue.component('entity-editor', require('./components/EntityEditor.vue'));
Vue.component('statement', require('./components/Statement.vue'));

const app = new Vue({
    el: '#app'
});
