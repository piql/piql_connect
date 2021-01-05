import axios from "axios"
import { wrappers } from "../../mixins/keycloakapi";

const axs = wrappers();

const state = {
    groups: null,
    pageMeta: null,
    response: null,
    groupUsers: null,
    groupRoles: null
}

const getters = {
    userGroups: state => state.groups,
    groupsPageMeta: state => state.pageMeta,
    groupsApiResponse: state => state.response,
    userGroupUsers: state => {
        return (state.groupUsers)?state.groupUsers:[];
    },
    userGroupRoles: state => {
        return (state.groupRoles)?state.groupRoles:[];
    }
}

const actions = {
    //post actions
    async postNewGroup({commit}, data){
        axios.post('/api/v1/admin/access-control/roles', data).then(response => {
            commit('setResponse',response)
        }).catch(error => {
            commit('setErrorResponse',error.response)
        })

    },

    async postRolesToGroup({commit},data){
        axios.post("/api/v1/admin/access-control/roles/"+ data.groupId +"/permissions",{
            permissions: data.roles
        }).then(response => {
            commit('setResponse',response)
        }).catch(error => {
            commit('setErrorResponse',error.response)
        })

    },

    async postUsersToGroup({commit}, data){
        axios.post("/api/v1/admin/access-control/users/assign",data).then(response => {
            commit('setResponse',response)
        }).catch(error => {
            commit('setErrorResponse',error.response)
        })

    },

    //get actions
    async fetchGroups({ commit }, query) {
        let max = query.limit || 10;
        let first = query.offset || 0;
        let search = query.q || '';
        let params = { max, search, first }
        return new Promise((resolve, reject) => {
            axs.get('/groups', { params }).then(response => {
                commit('setGroupsMutation', response.data)
                axs.get('/groups/count').then(rs => {
                    commit('setGroupSearchMeta', {
                        total: rs.data.count,
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

    async fetchGroupUsers({commit},groupId){
        let response = await axios.get('/api/v1/admin/access-control/roles/' + groupId +'/users');
        commit('setGroupUsersMutation', response.data)
    },

    async fetchGroupRoles({commit},groupId){
        let response = await axios.get('/api/v1/admin/access-control/roles/'+ groupId +'/permissions');
        commit('setGroupRolesMutation', response.data)
    }

}

const mutations = {
    setGroupSearchMeta(state, data) {
        let limit = data.query.limit || data.showing;
        let offset = data.query.offset || 0;
        let page = 1;
        if (offset > 0) page = Math.ceil(data.total/offset)
        state.pageMeta = {
            current_page: page,
            from: offset + 1,
            last_page: Math.ceil(data.total/limit),
            path: "/groups",
            per_page: limit,
            to: offset + data.showing,
            total: data.total
        }
    },
    setGroupsMutation: (state, payload) => {
        state.groups = payload;
        state.response = payload;
    },
    setGroupUsersMutation: (state,payload)=> {
        state.groupUsers = payload.data;
        state.response = payload;
    },
    setGroupRolesMutation: (state,payload)=> {
        state.groupRoles = payload.data;
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

export default {
    state,
    getters,
    actions,
    mutations
}
