require('./bootstrap');

window.Vue = require('vue');

Vue.component('home-page', require('./components/HomePage.vue').default);
Vue.component('completed-to-dos', require('./components/CompletedToDos.vue').default);

const app = new Vue({
    el: '#app',
});
