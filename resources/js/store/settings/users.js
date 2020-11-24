//import any mixin or library needed
import axios from "axios"
import env from "./../../environment"

let url = new URL(env.keyCloakConfig.url);
axios.defaults.baseURL = `${url.protocol}//${url.hostname}` + (url.port ? `:${url.port}` : '') + `/auth/admin/realms/${env.keyCloakConfig.realm}`;

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
    formattedUsers: state => state.users,
    usersPageMeta: state => state.pageMeta,
    userApiResponse: state => state.response
}

//actions
const actions = {
    async fetchUsers({ commit }, query) {
        let max = query.limit || 10;
        let first = query.offset || 0;
        let search = query.q || '';
        let params = { max, search, first }
        return new Promise((resolve, reject) => {
            axios.get('/users', { params }).then(response => {
                commit('setUsersMutation', response.data)
                axios.get('/users/count').then(rs => {
                    commit('setUserSearchMeta', {
                        total: rs.data,
                        showing: response.data.length,
                        query: query
                    })
                })
                resolve(response.data)
            }).catch(err => {
                reject(err.response.data);
            })
        })
    },

    async fetchSelectUsers({ commit }) {
        let response = await axios.get('/api/v1/admin/users');
        commit('setUsersMutation', response.data)
    },

    async postNewUser({ commit }, request) {
        axios.post("/api/v1/registration/register", request).then(response => {
            //response
            commit('setResponse', response)

        }).catch(err => {
            //error
            commit('setErrorResponse', err.response)

        });
    },

    async disableUserRequest({ commit }, data) {
        axios.post("/api/v1/admin/users/disable", data).then(response => {
            commit('setResponse', response)
        }).catch(err => {
            commit('setErrorResponse', err.response)
        })
    },

    async enableUserRequest({ commit }, data) {
        axios.post("/api/v1/admin/users/enable", data).then(response => {
            commit('setResponse', response)
        }).catch(err => {
            commit('setErrorResponse', err.response)
        })
    }
}

//mutations
const mutations = {
    setUserSearchMeta(state, data) {
        let limit = data.query.limit || data.showing;
        let offset = data.query.offset || 0;
        state.pageMeta = {
            current_page: (data.total / (offset + data.showing)) + 1,
            from: offset + 1,
            last_page: 3,
            path: "/users",
            per_page: limit,
            to: offset + data.showing,
            total: data.total
        }
    },
    setUsersMutation: (state, payload) => {
        state.users = [];
        payload.forEach(u => {
            u.full_name = `${u.firstName} ${u.lastName}`;
            state.users.push(u);
        })
        // state.pageMeta = payload.meta;
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
            status: error.status,
            message: error.data.message
        }
    }
}

export default {
    state,
    getters,
    actions,
    mutations
}