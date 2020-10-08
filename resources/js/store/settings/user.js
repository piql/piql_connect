import axios from "axios";

const state = {
    response: null,
    settings: null,
    password: null,
    languages: null,
    user: null,
    metadataTemplate: null,
    userMetadataTemplates: []
}

const getters = {
    settingsApiResponse: state => state.response,
    setPasswordData: state => state.password,
    userSettings: state => state.settings,
    userLanguages: state => state.languages,
    currentUser: state => state.user,
    userName: () => Vue.prototype.$keycloak.given_name,
    userMetadataTemplateByUserId:  state => userId => {
        return state.userMetadataTemplates.find( (t) => t.owner_id == userId );
    },
    userMetadataTemplates: state => state.userMetadataTemplates,
    currentLanguage: state => state.settings.interface.language,
   
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
    },

    async setAccountMetadataTemplate( {commit}, data ){
        //TODO: Api call for associating metadata template to the user account
        commit( 'setAccountMetadataTemplateMutation', data );
    },

    async createEmptyTemplateWithUserAsCreator( {commit}, userAccount ) {
        let ownerId = userAccount.id;
        let fullName = userAccount.full_name;
        let m = {"id": "", owner_id: "","created_at": "", "metadata":{"dc":{"title":"","creator":"","subject":"","description":"","publisher":"","contributor":"","date":"","type":"","format":"","identifier":"","source":"","language":"","relation":"","coverage":"","rights":""}}};
        let template = {...m, owner_id: ownerId, metadata: {...m.metadata, dc: { ...m.metadata.dc, creator: fullName}}};
        commit( 'setAccountMetadataTemplateMutation', template );
    },

    async logoutUser({commit}){
        Vue.prototype.$keycloak.logoutFn();
    },
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

    },

    setAccountMetadataTemplateMutation: ( state, payload ) => {
        state.userMetadataTemplates =
            state.userMetadataTemplates
            .filter( t => t.owner_id != payload.owner_id)
            .concat(payload);
    },

}


export default {
    state,
    getters,
    actions,
    mutations

}
