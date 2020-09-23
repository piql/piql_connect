import { wrappers } from "@mixins/axiosWrapper";

const ax = wrappers();


const state = {
    archives: [],
    archiveError: null,
}

const getters = {
    archives: state => state.archives,
    metadataByArchiveId: state => id => state.archives.find( archive => archive.id === id ).metadata
}

const actions = {
    async fetchArchives( { commit }, account, queryString = "" ){
        await ax.get(`/api/v1/ingest/account/${account}/archive/${queryString}`)
            .then( response =>
                commit('setArchivesMutation',response.data)
            ).catch( error =>
                commit( 'setArchiveErrorMutation', error.response )
            );
    },
    async addArchive( {commit}, request ) {
        let account = request.account;
        await ax.post( `/api/v1/ingest/account/${account}/archive/`,
            {
                title: request.title,
                description: request.description
            }
        ).then( response =>
            commit( 'addArchiveMutation', response.data )
        ).catch( error =>
            commit( 'setArchiveErrorMutation', error.response )
        );
    },

    async addArchiveMetadata( {commit}, request ){
        let account = request.accountId;
        let archive = request.archiveId;
        await ax.post( `/api/v1/ingest/account/${account}/archive/${archive}/metadata`,
            { metadata: { dc: request.metadata.metadata.dc } }
        ).then( response => {
            commit('addArchiveMetadataMutation', response.data )
        }
        ).catch( error =>
            commit( 'setArchiveErrorMutation', error.response )
        );
    },

    async editArchiveData({commit}, request ){
        let account = request.accountId;
        let archiveId = request.id;
        await ax.put( `/api/v1/ingest/account/${account}/archive/${archiveId}`,
            {
                title: request.title,
                description: request.description
            }
        ).then( response =>
            commit( 'editArchiveMutation', response.data )
        ).catch( error =>
            commit( 'setArchiveErrorMutation', error.response )
        );

    },

    async deleteArchiveData({commit}, id){
        commit('deleteArchiveMutation', id);
    }

}

const mutations = {
    addArchiveMutation ( state, archive ) {
        state.archives = state.archives.concat( archive );
    },
    setArchivesMutation( state, archives ) {
        state.archives = archives.data;
    },
    addArchiveMetadataMutation (state, payload) {
        let updatedArchive = state.archives.find(archive => archive.metadata.id === payload.data.id);
        //TODO: Fix the API, it should replace the template, not add another one
        updatedArchive.metadata.metadata = payload.data.metadata;
        let archiveIndex = state.archives.findIndex( archive => archive.id === updatedArchive.id );
        let end = state.archives.length;
        let before = state.archives.slice( 0, archiveIndex );
        let after = state.archives.slice( archiveIndex+1, end );
        state.archives = before.concat( updatedArchive ).concat( after );
    },
    editArchiveMutation( state, response ) {
        let archiveIndex = state.archives.findIndex( archive => archive.id === response.data.id );
        let end = state.archives.length;
        let before = state.archives.slice( 0, archiveIndex );
        let after = state.archives.slice( archiveIndex+1, end );
        state.archives = before.concat( response.data ).concat( after );
    },
    deleteArchiveMutation( state, id ) {
        state.archives = state.archives.filter(archive => archive.id !== id);
    },
    setArchiveErrorMutation( state, error ) {
        state.archiveError = error;
    },
}

export default {
    state,
    getters,
    actions,
    mutations
}
