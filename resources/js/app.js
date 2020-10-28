require('./bootstrap');

window.Vue = require('vue');

Vue.component('home-page', require('./components/HomePage.vue').default);
Vue.component('to-dos', require('./components/ToDos.vue').default);

const app = new Vue({
    el: '#app',
});
