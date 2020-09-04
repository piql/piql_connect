import Vuex from "vuex";
import Vue from "vue";

//import modules
import users from "./settings/users"
import groups from "./settings/groups"
import roles from "./settings/roles";
import user from "./settings/user";
import template from "./metadata/template";
import archives from "./settings/archives";

//load Vuex
Vue.use(Vuex)

//create the store

export const store = new Vuex.Store({
    modules: {
        //list our modules
        users,
        groups,
        roles,
        user,
        template,
        archives
    }
})
