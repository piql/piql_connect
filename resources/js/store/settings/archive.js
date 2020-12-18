import { wrappers } from "@mixins/axiosWrapper";

const ax = wrappers();


const state = {
    archives: [],
    pageMeta: null,
    archiveError: null
};

const getters = {
    archives: state => state.archives,
    firstArchive: state => state.archives.find( a => a ),
    archivePageMeta: state => state.pageMeta,
    archiveById: state => id => state.archives.find( archive => archive.id === id )
};

const actions = {
    async fetchArchives( { commit }, queryString = "" ) {
        await ax.get( `/api/v1/metadata/admin/archives/${queryString}` )
            .then( response =>
                commit('setArchivesMutation',response.data )
            ).catch( error =>
                commit( 'setArchiveErrorMutation', error.response )
            );
    },

    async addArchive( {commit}, data ) {
        commit( 'addArchiveMutation', data );
    },

    async addArchiveMetadata( {commit}, data ) {
        commit( 'addArchiveMetaMutation', data );
    },

    async editArchiveData( {commit}, data ) {
        commit( 'editArchiveMutation', data );
    },

    async deleteArchiveData( {commit}, id ) {
        commit( 'deleteArchiveMutation', id );
    }
}

const mutations = {
    setArchivesMutation( state, response ) {
        state.archives = response.data;
        state.pageMeta = response.meta;
    },
    addArchiveMutation( state, response ) {
        state.archives = state.archives.concat( response.data );
    },
    addArchiveMetadataMutation( state, response ) {
        /* Is this neccesary, or should we just mutate the archive? */
        let archiveIndex = state.archives.findIndex( archive => archive.id === response.data.id );
        let archive = archives[archiveIndex];
        archive.metadata = response.data.metadata;      // replace metadata
        archiveIndex[archives] = archive;               // update archive in place
    },
    editArchiveMutation( state, response ) {
        let archiveIndex = state.archives.findIndex( archive => archive.id === response.data.id );
        archives[archiveIndex] = response.data;       // update archive in place
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
