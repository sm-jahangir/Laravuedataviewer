require('./bootstrap');

window.Vue = require('vue').default;


Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('customer-component', require('./components/CustomerComponent.vue').default);


const app = new Vue({
    el: '#app',
});
