import axios from "axios";

const state = {
    response: null,
    settings: null,
    password: null,
    languages: null,
    user: null

}

const getters = {
    settingsApiResponse: state => state.response,
    setPasswordData: state => state.password,
    userSettings: state => state.settings,
    userLanguages: state => state.languages,
    currentUser: state => state.user

}

const actions = {

    async fetchUserSettings({commit}){
        let response = await axios.get("/api/v1/system/users/current-user/preferences");
        commit('setSettingsMutation',response)
    },

    async fetchLanguages({commit}){
        let response = await axios.get("/api/v1/system/languages");
        commit('setLanguagesMutation',response)
    },

    async changeLanguage({commit}, language){
        axios.post("/api/v1/system/users/current-user/preferences", {"interface": { "language": language }}).then(response => {
            commit('setResponse',response)
        }).catch(error => {
            commit('setErrorResponse',error.response)
        })
    },

    async setNewPassword({commit},data){
        let response =  await axios.post("/api/v1/system/users/current-user/password", data);
        commit('setPasswordMutation',response)
    }

}

const mutations = {
    setCurrentUserMutation: (state,payload)=> {
        state.user = payload.data;
    },
    setSettingsMutation: (state,payload)=> {
        state.settings = payload.data;
    },
    setPasswordMutation: (state,payload)=> {
        state.password = payload.data;
    },
    setLanguagesMutation: (state,payload)=> {
        state.languages = payload.data;
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