require('./bootstrap');

window.Vue = require('vue');

Vue.component('home-page', require('./components/HomePage.vue').default);
Vue.component('to-dos', require('./components/ToDos.vue').default);
Vue.component('modal-add-todo', require('./components/modals/AddTodo.vue').default);
Vue.component('modal-view-todo', require('./components/modals/ViewTodo.vue').default);
Vue.component('modal-delete-todo', require('./components/modals/DeleteTodo.vue').default);

const app = new Vue({
    el: '#app',
});
