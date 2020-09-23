import { wrappers } from "@mixins/axiosWrapper";

const ax = wrappers();

const state = {
    templates: [],
    pageMeta: null,
    templateError: null,
}

const getters = {
    templates: state => state.templates,
    templatePageMeta: state => state.pageMeta,
    templateById: state => id => state.templates.find( template => template.id === id )
}

const actions = {
    async fetchTemplates( { commit }, queryString = "" ){
        await ax.get(`/api/v1/ingest/metadata-template/${queryString}`)
            .then( response =>
                commit('setTemplatesMutation',response.data)
            ).catch( error =>
                commit( 'setTemplateError', error.response )
            );
    },
    async addTemplate( { commit }, template ) {
        await ax.post("/api/v1/ingest/metadata-template",
            { 'metadata': template.metadata }
        ).then( response => {
            commit('addTemplateMutation', response.data.data )
        }).catch( error => {
            commit('setTemplateError',error.response)
        });
    },
}

const mutations = {

    addTemplateMutation ( state, template ) {
        state.templates = state.templates.concat( template );
    },

    setTemplatesMutation ( state, templates ) {
        state.templates = templates.data;
        state.pageMeta = templates.meta;
    },

    setTemplateError( state, error ) {
        state.templateError = error;
        console.error( "Failed to update metadata templates: ", error );
    },
}

export default {
    state,
    getters,
    actions,
    mutations
}
