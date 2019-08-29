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

/* Function to collapse/expand sidemenu
Affects the sideMenu and contentContainer. Reduces size of sideMenu
whilst expanding the size of contentContainer (and vice versa)
*/

window.collapseMenu = function() {
    if (document.getElementById("sideMenu")) {          
        document.getElementById("sideMenu").setAttribute("id", "sideMenuCollapsed");
        document.getElementById("contentContainer").setAttribute("style", "width: calc(100% - 130px); transition: width 1s");
    }
    else {          
        document.getElementById("sideMenuCollapsed").setAttribute("id", "sideMenu");
        document.getElementById("contentContainer").setAttribute("style", "width: calc(100% - 360px); transition: width 1s");
    }
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

Vue.component('gallery', './components/gallery/index.vue');
Vue.component('thumbnail', './components/thumbnail/index.vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    i18n,
    data: {
    },
    methods: {
    }
});
