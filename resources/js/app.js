/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('bootstrap');
require('bootstrap-select');
require('filesize');

window.Vue = require('vue');
import { BootstrapVue } from 'bootstrap-vue';
window.Vue.use(BootstrapVue);
import VueInternationalization from 'vue-i18n';
import VueResize from 'vue-resize';
window.Vue.use(VueResize);
import toast from './mixins/toast.js';
window.Vue.mixin(toast);
import serviceCall from './mixins/serviceCall.js';
window.Vue.mixin(serviceCall);
import DateTime from './mixins/DateTime.js';
window.Vue.mixin(DateTime);
import Echo from "laravel-echo";
window.io = require('socket.io-client');
if (typeof io !== 'undefined') {
    window.Echo = new Echo({ broadcaster: 'socket.io', host: window.location.hostname + ':6001', });
}
import vueSelectSides from "vue-select-sides";
import vueFilterPrettyBytes from 'vue-filter-pretty-bytes';

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Now setup localization with vue-i18n using generated locales.
 * The Window.locale global is set in app.blade.php,
 * originating from \App:getLocale(). This way, we have one
 * localization concept that works across both php and js.
 */
const messages = require('./vue-i18n-locales.generated');
const i18n = new VueInternationalization({
    locale: Window.locale,
    messages: messages.default
});

/**
 * Register all Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

const files = require.context('./', true, /\.vue$/i);
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

let refreshSessionActivity = Vue.mixin({
    beforeUpdate: function () {
        if (this.noRefresh !== true) {
            this.refreshSession();
        }
    },
    methods: {
        refreshSession: () => { sessionStorage.setItem("lastActivityTime", Date.now()); }
    },
    props: {
        noRefresh: {
            type: Boolean,
            default: false
        }
    },
});
//to handle all multiside select options
Vue.use(vueSelectSides, {});
Vue.component("vue-select-sides", vueSelectSides);

// Pretty-print file sizes
Vue.use(vueFilterPrettyBytes);

/**
 * Finally, create the Vue application instance
 */

import { router } from "./router.js";
import env from "./environment"

//import store
import { store } from "./store/store";
import VueKeyCloak from '@dsb-norge/vue-keycloak-js'
import Layout from './views/layout.vue';

function interceptToken() {
    axios.interceptors.request.use(config => {
        config.headers.Authorization = `Bearer ${Vue.prototype.$keycloak.token}`
        return config
    }, error => {
        return Promise.reject(error)
    })
}

function loadLanguage() {
    axios.get("/api/v1/system/users/me").then( async ( resp ) =>  {
        i18n.locale = resp.data.language;
    });
}

Vue.use(VueKeyCloak, {
    config: env.keycloakConfig,
    onReady: () => {
        interceptToken();
        new Vue({
            router,
            i18n,
            store,
            mixins: [refreshSessionActivity],
            render: h => h(Layout)
        }).$mount('#app');
        loadLanguage();
    }
});
