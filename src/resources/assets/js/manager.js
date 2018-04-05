/*                Libs                */
window.Vue = require('vue')
window.EventHub = require('vuemit')

// axios
window.axios = require('axios')
axios.defaults.headers.common = {
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    'X-Requested-With': 'XMLHttpRequest'
}
axios.interceptors.response.use(
    (response) => {return response},
    (error) => {return Promise.reject(error.response)}
)

// vue-awesome
import 'vue-awesome/icons/search'
import 'vue-awesome/icons/times'
Vue.component('icon', require('vue-awesome/components/Icon'))

/*                Components                */
Vue.component('Tagos', require('./add.vue'))
Vue.component('TagosList', require('./list.vue'))
Vue.component('TagosIndex', require('./index.vue'))
Vue.component('MyNotification', require('vue-notif'))

