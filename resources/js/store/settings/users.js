//import any mixin or library needed
import axios from "axios"

function admin(axs) {
    let iss = Vue.prototype.$keycloak.idTokenParsed.iss;
    let realm = iss.substring(iss.lastIndexOf('/') + 1);
    let url = new URL(iss);
    axs.defaults.baseURL= `${url.protocol}//${url.hostname}` + (url.port ? `:${url.port}` : '') + `/auth/admin/realms/${realm}`;
    return axs;
}

//state
const state = {
    users: null,
    pageMeta: null,
    response: null
}

//getters
//if you want to alter state in some way, you could use the getters for that,
//but you can also use then to return state
const getters = {
    formattedUsers:state => state.users,
    usersPageMeta: state => state.pageMeta,
    userApiResponse: state => state.response
}

//actions
const actions = {
    async fetchUsers({commit }, query){
        // console.log(Vue.prototype.$keycloak.idTokenParsed.iss);
        // console.log(root)
        let response  = await admin(axios).get('/users' + query);
        commit('setUsersMutation', response.data)
    },

    async fetchSelectUsers({commit}){
        let response = await axios.get('/api/v1/admin/users');
        commit('setUsersMutation', response.data)
    },

    async postNewUser({commit}, request){
        axios.post("/api/v1/registration/register",request).then(response => {
            //response
            commit('setResponse',response)

        }).catch(err => {
            //error
            commit('setErrorResponse',err.response)

        });
    },

    async disableUserRequest({commit}, data){
        axios.post("/api/v1/admin/users/disable",data).then(response => {
            commit('setResponse',response)
        }).catch(err => {
            commit('setErrorResponse',err.response)
        })

    },

    async enableUserRequest({commit}, data){
        axios.post("/api/v1/admin/users/enable",data).then(response => {
            commit('setResponse',response)
        }).catch(err => {
            commit('setErrorResponse',err.response)
        })

    }

}

//mutations
const mutations = {
    setUsersMutation: (state,payload)=> {
        state.users = payload.data;
        state.pageMeta = payload.meta;
        state.response = payload;
    },
    setResponse: (state, response) => {
        state.response = {
            status: response.status,
            message: response.statusText
        }

    },
    setErrorResponse: (state, error) => {
        state.response = {
            status:error.status,
            message: error.data.message
        }

    }


}

export default{
    state,
    getters,
    actions,
    mutations

}