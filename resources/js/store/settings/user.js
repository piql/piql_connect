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
    userName: () => Vue.prototype.$keycloak.tokenParsed.preferred_username,
    userIsAdmin: () => Vue.prototype.$keycloak.resourceAccess['piql-connect-api'].roles.includes('admin'),
    userOrganizationId: () => Vue.prototype.$keycloak.tokenParsed.organization,
    userMetadataTemplateByUserId: state => userId => {
        return state.userMetadataTemplates.find((t) => t.owner_id == userId);
    },
    userMetadataTemplates: state => state.userMetadataTemplates,
    currentLanguage: state => state.settings.interface.language || 'en',
    userTableRowCount: state => state.settings.interface.tableRowCount,
}

const actions = {

    async fetchUserSettings({ commit }) {
        return new Promise((resolve, reject) => {
            axios.get("/api/v1/system/users/current-user/preferences")
            .then(response => {
                commit('setSettingsMutation', response)
                resolve(response.data)
            }).catch(error => {
                commit('setErrorResponse', error.response);
                reject(error.response)
            })
        });
    },


    async fetchLanguages({ commit }) {
        let response = await axios.get("/api/v1/system/languages");
        commit('setLanguagesMutation', response)
    },

    async changeLanguage({ commit }, language) {
        axios.post("/api/v1/system/users/current-user/preferences", { "interface": { "language": language } }).then(response => {
            commit('setResponse', response)
        }).catch(error => {
            commit('setErrorResponse', error.response)
        })
    },

    async setNewPassword({ commit }, data) {
        let response = await axios.post("/api/v1/system/users/current-user/password", data);
        commit('setPasswordMutation', response)
    },

    async setArchiveMetadataTemplate({ commit }, data) {
        //TODO: Api call for associating metadata template to the user archive
        commit('setArchiveMetadataTemplateMutation', data);
    },

    async updateTableRowCount({ commit }, count) {
        return new Promise((resolve, reject) => {
            axios.post("/api/v1/system/users/current-user/preferences", { "interface": { "tableRowCount": count } })
            .then(response => {
                commit('setResponse', response)
                commit('setSettingsMutation', response)
                resolve(response.data)
            }).catch(error => {
                commit('setErrorResponse', error.response);
                reject(error.response)
            })
        })

    },

    async createEmptyTemplateWithUserAsCreator({ commit }, userArchive) {
        let ownerId = userArchive.id;
        let fullName = userArchive.full_name;
        let m = { "id": "", owner_id: "", "created_at": "", "metadata": { "dc": { "title": "", "creator": "", "subject": "", "description": "", "publisher": "", "contributor": "", "date": "", "type": "", "format": "", "identifier": "", "source": "", "language": "", "relation": "", "coverage": "", "rights": "" } } };
        let template = { ...m, owner_id: ownerId, metadata: { ...m.metadata, dc: { ...m.metadata.dc, creator: fullName } } };
        commit('setArchiveMetadataTemplateMutation', template);
    },

    async logoutUser({ commit }) {
        Vue.prototype.$keycloak.logoutFn();
    },
}

const mutations = {
    setCurrentUserMutation: (state, payload) => {
        state.user = payload.data;
    },
    setSettingsMutation: (state, payload) => {
        state.settings = payload.data;
    },
    setPasswordMutation: (state, payload) => {
        state.password = payload.data;
    },
    setLanguagesMutation: (state, payload) => {
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
            status: error.status,
            message: error.data.message
        }

    },

    setArchiveMetadataTemplateMutation: (state, payload) => {
        state.userMetadataTemplates =
            state.userMetadataTemplates
                .filter(t => t.owner_id != payload.owner_id)
                .concat(payload);
    },

}


export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations

}
