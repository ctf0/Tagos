/*                Libs                */
window.EventHub = require('vuemit')
window.Fuse = require('fuse.js')

// axios
window.axios = require('axios')
axios.defaults.headers.common = {
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    'X-Requested-With': 'XMLHttpRequest'
}
axios.interceptors.response.use(
    (response) => response,
    (error) => Promise.reject(error.response)
)

// vue-tippy
Vue.use(require('vue-tippy'), {
    arrow: true,
    touchHold: true,
    inertia: true,
    performance: true,
    flipDuration: 0,
    popperOptions: {
        modifiers: {
            preventOverflow: {enabled: false},
            hide: {enabled: false}
        }
    }
})

// vue-awesome
import 'vue-awesome/icons/search'
import 'vue-awesome/icons/times'
import 'vue-awesome/icons/trash'
import 'vue-awesome/icons/anchor'
Vue.component('icon', require('vue-awesome/components/Icon').default)

/*                Components                */
Vue.component('Tagos', require('./add.vue').default)
Vue.component('TagosList', require('./table/list.vue').default)
Vue.component('TagosIndex', require('./index.vue').default)
Vue.component('MyNotification', require('vue-notif').default)
