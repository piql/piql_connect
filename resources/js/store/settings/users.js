//import any mixin or library needed
import axios from "axios"
import { wrappers } from "../../mixins/keycloakapi";

const axs = wrappers();

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
    userApiResponse: state => state.response,
    onUserCreatedActions: () => [
        /**
         * refer to refer to
         * https://www.keycloak.org/docs-api/4.8/javadocs/index.html?org/keycloak/models/UserModel.RequiredAction.html
         * for more detail
        */
        { value:'UPDATE_PASSWORD', text: 'user.on-create.action.update-password' },
        { value:'VERIFY_EMAIL', text: 'user.on-create.action.verify-email' },
        { value:'UPDATE_PROFILE', text: 'user.on-create.action.update-profile' },
    ]
}

//actions
const actions = {
    async fetchUsers({ commit }, query) {
        let max = query.limit || 10;
        let first = query.offset || 0;
        let search = query.q || '';
        let params = { max, search, first }
        return new Promise((resolve, reject) => {
            axs.get('/users', { params }).then(response => {
                commit('setUsersMutation', response.data)
                axs.get('/users/count').then(rs => {
                    commit('setUserSearchMeta', {
                        total: rs.data,
                        showing: response.data.length,
                        query: query
                    })
                })
                resolve(response.data)
            }).catch(err => {
                commit('setErrorResponse', err.response)
                reject(err.response.data);
            })
        })
    },

    async updateUser({ commit }, data) {
        return new Promise((resolve, reject) => {
            axs.put(`/users/${data.id}`, data).then(response => {
                commit('setUpdatedUser', data);
                resolve(response.data)
            }).catch(err => {
                commit('setUserError', {id: data.id, error: err.response.data});
                commit('setErrorResponse', err.response)
                reject(err.response.data);
            })
        })
    },

    async fetchSelectUsers({ commit }) {
        return new Promise((resolve, reject) => {
            axs.get('/users').then(response => {
                commit('setUsersMutation', response.data)
                resolve(response.data)
            }).catch(err => {
                commit('setErrorResponse', err.response)
                reject(err.response.data);
            })
        })
    },

    async createUser({ commit }, data) {
        return new Promise((resolve, reject) => {
            axs.post('/users', data).then(response => {
                resolve(response.data)
            }).catch(err => {
                reject(err);
            })
        })
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
    setUserError(state, data) {
        let index = state.users.findIndex((u => u.id == data.id));
        if(state.users[index] == undefined) {
            console.log(`could not find user with id ${data.id}`);
            return
        }
        state.users[index].error = data.error;
    },
    setUpdatedUser(state, data) {
        let index = state.users.findIndex((u => u.id == data.id));
        if(state.users[index] == undefined) {
            console.log(`could not find user with id ${data.id}`);
            return
        }
        data.error = null;
        Object.assign(state.users[index], data)
    },
    setUserSearchMeta(state, data) {
        let limit = data.query.limit || data.showing;
        let offset = data.query.offset || 0;
        let page = 1;
        if (offset > 0) page = Math.ceil(data.total/offset)
        state.pageMeta = {
            current_page: page,
            from: offset + 1,
            last_page: Math.ceil(data.total/limit),
            path: "/users",
            per_page: limit,
            to: offset + data.showing,
            total: data.total
        }
    },
    setUsersMutation: (state, payload) => {
        state.users = [];
        payload.forEach(u => {
            u.error = '';
            state.users.push(u);
        })
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
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
