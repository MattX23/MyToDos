require('./bootstrap');

window.Vue = require('vue');

Vue.component('alert', require('./components/partials/Alert.vue').default);
Vue.component('home-page', require('./components/HomePage.vue').default);
Vue.component('modal-delete-todo', require('./components/modals/DeleteTodo.vue').default);
Vue.component('modal-manage-todo', require('./components/modals/ManageToDo.vue').default);
Vue.component('modal-view-todo', require('./components/modals/ViewTodo.vue').default);
Vue.component('to-dos', require('./components/ToDos.vue').default);

const app = new Vue({
    el: '#app',
});
