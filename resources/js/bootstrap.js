window._ = require('lodash');

try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
} catch (e) {}

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

let api_token = document.head.querySelector('meta[name="api-token"]');

if (api_token) {
    window.axios.defaults.headers.common['Authorization'] = 'Bearer ' + api_token.content;
}

let csrf_token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

if (csrf_token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = csrf_token;
}
