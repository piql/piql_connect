import { wrappers } from "@mixins/axiosWrapper";

const ax = wrappers();

const state = {
    holdings: [],
    holdingError: null,
    pageMeta: null
}

const getters = {
    holdings: state => state.holdings,
    holdingById: state => id => state.holdings.find( holding => holding.id === id ),
    holdingPageMeta: state => state.pageMeta,
}

const actions = {
    async fetchHoldingsForCollection( { commit }, { accountId, collectionId, query} ){
        let queryString = query ?? "";
        await ax.get(`/api/v1/metadata/admin/accounts/${accountId}/collections/${collectionId}/holdings${queryString}`)
            .then( response =>
                commit('setHoldingsMutation',response.data )
            ).catch( error =>
                commit( 'setHoldingErrorMutation', error.response )
            );
    },
    async createHolding( {commit}, request ) {
        const accountId = request.accountId;
        const collectionId = request.collectionId;
        await ax.put( `/api/v1/metadata/admin/accounts/${accountId}/collections/${collectionId}/holdings`,
            {
                title: request.title,
                description: request.description,
                accountId: request.accountId,
            }
        ).then( response =>
            commit( 'createHoldingMutation', response.data )
        ).catch( error =>
            commit( 'setHoldingErrorMutation', error.response )
        );
    },

    async updateHoldingMetadata( {commit}, payload ) {
        const accountId = payload.accountId;
        const collectionId = payload.collectionId;
        const url = `/api/v1/metadata/admin/accounts/${accountId}/collections/${collectionId}/holdings`;
        const holding = payload.holding;

        await ax.put( url, holding )
            .then( response => {
                commit( 'addHoldingMetadataMutation', response.data )
            }).catch( error =>
                commit( 'setHoldingErrorMutation', error.response )
            );
    },

    async editHoldingData( {commit}, request ) {
        let accountId = request.accountId;
        let collectionId = request.collectionId;
        let id = request.id;

        await ax.put( `/api/v1/metadata/admin/accounts/${accountId}/collections/${collectionId}/holdings/${id}`,
            {
                title: request.title,
                description: request.description
            }
        ).then( response =>
            commit( 'editHoldingMutation', response.data )
        ).catch( error =>
            commit( 'setHoldingErrorMutation', error.response )
        );
    },
}

const mutations = {
    createHoldingMutation ( state, holding ) {
        state.holdings = state.holdings.concat( holding.data );
    },
    setHoldingsMutation( state, holdings ) {
        state.holdings = holdings.data;
        state.pageMeta = holdings.meta;
    },
    addHoldingMetadataMutation (state, payload) {
        let holdingToUpdate = state.holdings.find( holding => holding.id === payload.data.id );
        holdingToUpdate.defaultMetadataTemplate = payload.data.defaultMetadataTemplate;
    },
    editHoldingMutation( state, response ) {
        let holdingIndex = state.holdings.findIndex( holding => holding.id === response.data.id );
        let end = state.holdings.length;
        let before = state.holdings.slice( 0, holdingIndex );
        let after = state.holdings.slice( holdingIndex+1, end );
        state.holdings = before.concat( response.data ).concat( after );
    },
    setHoldingErrorMutation( state, error ) {
        state.holdingError = error;
    },
}

export default {
    state,
    getters,
    actions,
    mutations
}
