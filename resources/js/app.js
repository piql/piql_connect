/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
require('jquery');
require('bootstrap');
require('bootstrap-select');
window.Vue = require('vue');
import VueInternationalization from 'vue-i18n';

/** Function to collapse/expand sidemenu
Affects the sideMenu and contentContainer. Reduces size of sideMenu
whilst expanding the size of contentContainer (and vice versa)
*/

window.collapseMenu = function() {
    if (document.getElementById("sideMenu")) {
        document.getElementById("sideMenu").setAttribute("id", "sideMenuCollapsed");
        document.getElementById("sidebarWrapper").setAttribute("class", "col-1 pl-0 sidebarWrapper");
        document.getElementById("mainContent").setAttribute("class", "col-10 ml-2 mr-1");
        document.getElementById("poweredBy").setAttribute("style", "display: inline-flex;");
    }
    else {
        document.getElementById("sideMenuCollapsed").setAttribute("id", "sideMenu");
        document.getElementById("sidebarWrapper").setAttribute("class", "col-3 pl-0 sidebarWrapper");
        document.getElementById("mainContent").setAttribute("class", "col-8 ml-3 mr-0");
        document.getElementById("poweredBy").setAttribute("style", "display: none;");
    }
}

window.isAutoCollapsed = false;
window.autoCollapseMenu = function() {
    if(!window.isAutoCollapsed){
        window.collapseMenu();
        window.isAutoCollapsed = true;
    }
}

window.onload = function() {
    window.onresize();
}

window.onresize = function() {
    let wide = document.getElementById("sideMenu");
    let bodyWidth = document.body.getClientRects()[0].width;
    if( wide ) {
        if( bodyWidth < 1200 ) {
            window.collapseMenu();
        }
    } else {
        if( bodyWidth > 1200 ) {
            window.collapseMenu();
        }
    }
}

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
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

const files = require.context('./', true, /\.vue$/i);
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

/**
 * The filesize es6 module - giving us human readable file sizes -
 * must be required by full path according to the documentation at
 * https://www.npmjs.com/package/filesize
 */
require('../../node_modules/filesize/lib/filesize.es6.js');

const app = new Vue({
    el: '#app',
    i18n,
    data: {
    },
    methods: {
    }
});
