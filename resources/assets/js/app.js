require('./bootstrap');
require('./jquery_scripts');
require('sweetalert2/dist/sweetalert2');
import AwesomeNotifications from 'vue-awesome-notifications';

window.Vue = require('vue');

Vue.use(AwesomeNotifications, {	position: 'top-right' });

require('./config/interceptors');

Vue.component('deposit', require('./components/Deposit.vue'));
Vue.component('withdrawal', require('./components/Withdrawal.vue'));
Vue.component('trade', require('./components/Trade.vue'));
Vue.component('escrow', require('./components/Escrow.vue'));
Vue.component('escrowmaster', require('./components/EscrowMaster.vue'));

const app = new Vue({
    el: '#app'
});