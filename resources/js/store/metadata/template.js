import axios from "axios";

const state = {
    templates: [
        ],
    templatePageMeta: null,
    templateResponse: null,
    templateError: null,
    schemes: null,
    schemesPageMeta: null,
    schemesResponse: null,
}

const getters = {
    templates: state => state.templates,
    templatePageMeta: state => state.pageMeta,
    templateById: (state) => (id) => state.templates.find( template => template.id === id )
}

const actions = {
    async fetchTemplates( { commit } ){
        let response = await axios.get("/api/v1/ingest/metadata/metadata-template");
        console.debug("hello", response);
        commit('setTemplatesMutation',response.data.metadata)
    },
    async addTemplate( { commit }, template ) {
        return await axios.post("/api/v1/ingest/metadata-template", { 'metadata': template.metadata }).then(response => {
            commit('addTemplateMutation', response.metadata );
        }).catch(error => {
            commit('setTemplateError',error.response)
        })
    },
}

const mutations = {

    addTemplateMutation (state,template) {
        state.templates = state.templates.concat(template);
    },

    setTemplateError( state, error ){
        console.debug( error );
        state.templateError = error
    },
}

export default {
    state,
    getters,
    actions,
    mutations
}
