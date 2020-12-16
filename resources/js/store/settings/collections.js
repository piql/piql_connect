import { wrappers } from "@mixins/axiosWrapper";

const ax = wrappers();


const state = {
    collections: [],
    collectionError: null,
    pageMeta: null
}

const getters = {
    collections: state => state.collections,
    collectionById: state => id => state.collections.find( collection => collection.id === id ),
    collectionPageMeta: state => state.pageMeta,
}

const actions = {
    async fetchCollections( { commit }, { accountId, query } ){
        let queryString = query ?? "";
        await ax.get(`/api/v1/metadata/admin/accounts/${accountId}/collections${queryString}`)
            .then( response =>
                commit('setCollectionMutation',response.data)
            ).catch( error =>
                commit( 'setCollectionErrorMutation', error.response )
            );
    },
    async createCollection( {commit}, request ) {
        let accountId = request.accountId;
        await ax.put( `/api/v1/metadata/admin/accounts/${accountId}/collections/`,
            {
                title: request.title,
                description: request.description,
                accountId
            }
        ).then( response =>
            commit( 'createCollectionMutation', response.data )
        ).catch( error =>
            commit( 'setCollectionErrorMutation', error.response )
        );
    },

    async updateCollectionMetadata( {commit}, payload ) {
        const url = `/api/v1/metadata/admin/accounts/${payload.accountId}/collections`;
        const collection = payload.collection;

        await ax.put( url, collection )
            .then( response => {
                commit( 'addCollectionMetadataMutation', response.data )
            }).catch( error =>
                commit( 'setCollectionErrorMutation', error.response )
            );
    },

    async editCollectionData({commit}, request ){
        let accountId = request.accountId;
        let collectionId = request.id;
        await ax.put( `/api/v1/metadata/admin/accounts/${accountId}/collections`,
            {
                id: collectionId,
                title: request.title,
                description: request.description
            }
        ).then( response =>
            commit( 'editCollectionMutation', response.data )
        ).catch( error =>
            commit( 'setCollectionErrorMutation', error.response )
        );

    },
}

const mutations = {
    createCollectionMutation ( state, payload ){
        state.collections = state.collections.concat( payload.data );
    },
    setCollectionMutation( state, collections ) {
        state.collections = collections.data;
        state.pageMeta = collections.meta;
    },
    addCollectionMetadataMutation (state, payload) {
        let collectionToUpdate = state.collections.find(collection => collection.metadata.id === payload.data.id);
        collectionToUpdate.defaultMetadataTemplate = payload.data.defaultMetadataTemplate;
    },
    editCollectionMutation( state, response ) {
        let collectionIndex = state.collections.findIndex( collection => collection.id === response.data.id );
        let end = state.collections.length;
        let before = state.collections.slice( 0, collectionIndex );
        let after = state.collections.slice( collectionIndex+1, end );
        state.collections = before.concat( response.data ).concat( after );
    },
    setCollectionErrorMutation( state, error ) {
        state.collectionError = error;
    },
}

export default {
    state,
    getters,
    actions,
    mutations
}
