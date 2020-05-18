/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('bootstrap');
require('bootstrap-select');
require('filesize');

window.Vue = require('vue');
import VueRouter from 'vue-router';
window.Vue.use(VueRouter);
import {BootstrapVue} from 'bootstrap-vue';
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
    window.Echo = new Echo({    broadcaster: 'socket.io',    host: window.location.hostname + ':6001',  });
}
/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}


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
        beforeUpdate: function() {
            if( this.noRefresh !== true ){
                this.refreshSession();
            }
        },
        methods: {
            refreshSession: () => { sessionStorage.setItem( "lastActivityTime", Date.now() );}
        },
		props: {
			noRefresh: {
				type: Boolean,
				default: false
			}
		},
});

/** Set up vue router.
 * TODO: Move this to a separate file ASAP
 */

import Dashboard from './views/stats/dashboard.vue';
import TopBar from './views/partials/TopBar.vue';
import SideBar from './views/partials/SideBar.vue';

import Upload from './views/ingest/Upload.vue';
import IngestMetadata from './views/ingest/IngestMetadata.vue';
import AipContentMetadata from './views/ingest/AipContentMetadata.vue';
import Processing from './views/ingest/Processing.vue';
import TaskList from './views/ingest/TaskList.vue';
import IngestStatus from './views/ingest/IngestStatus.vue';
import BucketContent from "./views/ingest/BucketContent.vue";
import BucketMetadata from './views/ingest/BucketMetadata.vue';
import BucketConfig from "./views/ingest/BucketConfig";


import Browse from './views/access/Browse.vue';
import DipBrowser from './views/access/DipBrowser.vue';
import AipBrowser from './views/ingest/AipBrowser.vue';
import AccessMetadata from './views/access/AccessMetadata.vue';
import ReadyToRetrieve from './views/access/ReadyToRetrieve.vue';
import ReadyForDownload from './views/access/ReadyForDownload.vue';
import RetrievalHistory from './views/access/RetrievalHistory.vue';
import NowRetrieving from './views/access/NowRetrieving.vue';

import Settings from './views/settings/Settings.vue';
import PageNotFound from './views/PageNotFound.vue';

const router = new VueRouter({
    mode: 'history',
    routes: [
        {
            path: "/",
            name: "home.dashboard",
            component: Dashboard
        },
        {
            path: "/ingest/upload",
            name: "ingest.upload",
            component: Upload
        },
        {
            path: "/ingest/metadata/bags/:bagId/file/:fileId",
            name: "ingest.metadata.edit",
            component: IngestMetadata,
        },
        {
            path: "/ingest/process",
            name: "ingest.process",
            component: Processing
        },
        {
            path: "/ingest/offline_storage",
            name: "ingest.offline_storage",
            component: TaskList
        },
        {
            path: "/ingest/offline_storage/:bucketId",
            name: "ingest.offline_storage.bucket_content",
            component: BucketContent
        },
        {
            path: "/api/v1/ingest/offline_storage/pending/jobs/:fileId",
            name: "ingest.offline_storage.bucket_metadata",
            component: BucketMetadata,
        },
        {
            path: "/ingest/offline_storage/dip/:dipId",
            name: "ingest.browse.aip",
            component: AipBrowser
        },
        {
            path: "/ingest/offline_storage/dip/:dipId/file/:fileId",
            name: "ingest.dip.metadata.view",
            component: AipContentMetadata,
        },
        {
            path: "/ingest/offline_storage/:bucketId/configuration",
            name: "ingest.offline_storage.bucket_config",
            component: BucketConfig
        },
        {
            path: "/ingest/status",
            name: "ingest.status",
            component: IngestStatus
        },
        {
            path: "/access/browse",
            name: "access.browse",
            component: Browse
        },
        {
            path: "/access/browse/metadata/file/:fileId",
            name: "access.browse.metadata",
            component: AccessMetadata,
            props: {
                url: "/api/v1/access/files",
                readOnly: true
            },
        },
        {
            path: "/access/browse/dip/:dipId",
            name: "access.browse.dip",
            component: DipBrowser
        },
        {
            path: "/access/retrieve/ready",
            name: "access.retrieve.ready",
            component: ReadyToRetrieve
        },
        {
            path: "/access/retrieve/retrieving",
            name: "access.retrieve.retrieving",
            component: NowRetrieving
        },
        {
            path: "/access/retrieve/download",
            name: "access.retrieve.download",
            component: ReadyForDownload
        },
        {
            path: "/access/retrieve/history",
            name: "access.retrieve.history",
            component: RetrievalHistory
        },
        {
            path: "/settings",
            name: "settings",
            component: Settings
        },
        {
            path: "*",
            name: "PageNotFound",
            component: PageNotFound
        }
    ],
});

/**
 * Finally, create the Vue application instance
 */

const app = new Vue({
    el: '#app',
    i18n,
    router,
    mixins: [refreshSessionActivity],
});
