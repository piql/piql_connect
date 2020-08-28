import axios from "axios";

const state = {
    templates: [
            {"id": "3bdcce12-4fc8-4c07-bada-e1418bff9465", "created_at": "2020-05-22", "metadata":{"dc":{"title":"Svalbard","creator":"a photographer","subject":"cold things","description":"brrr","publisher":"","contributor":"","date":"","type":"","format":"","identifier":"63ff83ef-862e-428f-9cc3-2040693d493e","source":"","language":"","relation":"","coverage":"","rights":""}}},
            {"id": "870c6012-506f-4e47-8fbd-54c8b0dea6c0", "created_at": "2020-05-23", "metadata":{"dc":{"title":"Drammen","creator":"that guy", "subject": "awesome place", "description": "yay", "publisher": "", "contributor": "", "date": "", "type": "", "format": "", "identifier": "63ff83ef-862e-428f-9cc3-2040693d493e", "source": "", "language": "", "relation": "", "coverage": "", "rights": "" } } }
        ],
    templatePageMeta: null,
    templateResponse: null,
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
    async addTemplate( {commit}, template ) {
        //TODO: axios post to template api goes here
        commit('addTemplateMutation', JSON.parse(JSON.stringify(template)));    /* Deep copy the fields */
    }
}

const mutations = {
    addTemplateMutation (state,template) {
        state.templates = state.templates.concat(template);
    }
}

export default {
    state,
    getters,
    actions,
    mutations
}
