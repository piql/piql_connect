import { wrappers } from "@mixins/axiosWrapper";

const ax = wrappers();


const state = {
    archives: [],
    archiveError: null,
}

const getters = {
    archives: state => state.archives,
    archiveById: state => id => state.archives.find( archive => archive.id === id )
}

const actions = {
    async fetchArchives( { commit }, account, queryString = "" ){
        await ax.get(`/api/v1/metadata/admin/accounts/${account}/archives/${queryString}`)
            .then( response =>
                commit('setArchivesMutation',response.data)
            ).catch( error =>
                commit( 'setArchiveErrorMutation', error.response )
            );
    },
    async addArchive( {commit}, request ) {
        let account = request.account;
        await ax.put( `/api/v1/metadata/admin/accounts/${account}/archives/`,
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
        let account = request.accountId;
        let archiveId = request.id;
        await ax.put( `/api/v1/metadata/admin/accounts/${account}/archives/${archiveId}`,
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
