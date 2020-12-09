import axios from "axios";

const state = {
    roles: null,
    pageMeta: null,
    response: null,
}

const getters = {
    userRoles: state => state.roles,
    rolesPageMeta: state => state.pageMeta,
    rolesApiResponse: state => state.response,
   
}

const actions = {
    async fetchSelectedRoles({commit}){
        let response  = await axios.get("/api/v1/admin/access-control/permission-groups");
        commit('setRolesMutation', response.data)
    },

    async fetchRoles({commit},query){
        let response  = await axios.get("/api/v1/admin/access-control/permission-groups"+ query);
        commit('setRolesMutation', response.data)
    },

    async postNewRole({commit}, data){
        axios.post('/api/v1/admin/access-control/permission-groups', data).then(response => {
            commit('setResponse',response)
        }).catch(error => {
            commit('setErrorResponse',error.response)
        })

    },

    async updateRole({commit},data){
        await axios.put("/api/v1/admin/access-control/permission-groups/"+ data.roleId, {
            name: data.name,
            description: data.description
        }).then(response => {
            commit('setResponse',response)
        }).catch(error => {
            commit('setErrorResponse',error.response)
        })
    },

    async removeRole({commit},roleId){
        axios.delete("/api/v1/admin/access-control/permission-groups/"+ roleId).then(response => {
            commit('setResponse',response)
        }).catch(error => {
            commit('setErrorResponse',error.response)
        })
    }



}

const mutations = {
    setRolesMutation: (state,payload)=> {
        state.roles = payload.data;
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

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
