import { wrappers } from "@mixins/axiosWrapper";

const ax = wrappers();


const state = {
    archives: [],
    archiveError: null,
    pageMeta: null
}

const getters = {
    archives: state => state.archives,
    archiveById: state => id => state.archives.find( archive => archive.id === id ),
    archivePageMeta: state => state.pageMeta,
}

const actions = {
    async fetchArchives( { commit }, { accountId, query } ){
        let queryString = query ?? "";
        await ax.get(`/api/v1/metadata/admin/accounts/${accountId}/archives${queryString}`)
            .then( response =>
                commit('setArchivesMutation',response.data)
            ).catch( error =>
                commit( 'setArchiveErrorMutation', error.response )
            );
    },
    async createArchive( {commit}, request ) {
        let accountId = request.accountId;
        await ax.put( `/api/v1/metadata/admin/accounts/${accountId}/archives/`,
            {
                title: request.title,
                description: request.description,
                accountId
            }
        ).then( response =>
            commit( 'createArchiveMutation', response.data )
        ).catch( error =>
            commit( 'setArchiveErrorMutation', error.response )
        );
    },

    async updateArchiveMetadata( {commit}, payload ) {
        const url = `/api/v1/metadata/admin/accounts/${payload.accountId}/archives`;
        const archive = payload.archive;

        await ax.put( url, archive )
            .then( response => {
                commit( 'addArchiveMetadataMutation', response.data )
            }).catch( error =>
                commit( 'setArchiveErrorMutation', error.response )
            );
    },

    async editArchiveData({commit}, request ){
        let accountId = request.accountId;
        let archiveId = request.id;
        await ax.put( `/api/v1/metadata/admin/accounts/${accountId}/archives`,
            {
                id: archiveId,
                title: request.title,
                description: request.description
            }
        ).then( response =>
            commit( 'editArchiveMutation', response.data )
        ).catch( error =>
            commit( 'setArchiveErrorMutation', error.response )
        );

    },
}

const mutations = {
    createArchiveMutation ( state, payload ){
        state.archives = state.archives.concat( payload.data );
    },
    setArchivesMutation( state, archives ) {
        state.archives = archives.data;
        state.pageMeta = archives.meta;
    },
    addArchiveMetadataMutation (state, payload) {
        let archiveToUpdate = state.archives.find(archive => archive.metadata.id === payload.data.id);
        archiveToUpdate.defaultMetadataTemplate = payload.data.defaultMetadataTemplate;
    },
    editArchiveMutation( state, response ) {
        let archiveIndex = state.archives.findIndex( archive => archive.id === response.data.id );
        let end = state.archives.length;
        let before = state.archives.slice( 0, archiveIndex );
        let after = state.archives.slice( archiveIndex+1, end );
        state.archives = before.concat( response.data ).concat( after );
    },
    setArchiveErrorMutation( state, error ) {
        state.archiveError = error;
    },
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
