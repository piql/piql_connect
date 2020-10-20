import axios from "axios";

const state = {
    groups: null,
    pageMeta: null,
    response: null,
    groupUsers: null,
    groupRoles: null
}

const getters = {
    userGroups: state => state.groups,
    groupPageMeta: state => state.pageMeta,
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
    async fetchGroups({commit},query){
        let response =  await axios.get('/api/v1/admin/access-control/roles' + query);
        commit('setGroupsMutation', response.data)
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
    setGroupsMutation: (state,payload)=> {
        state.groups = payload.data;
        state.pageMeta = payload.meta;
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