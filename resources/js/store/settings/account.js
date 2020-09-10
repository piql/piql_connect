import { wrappers } from "@mixins/axiosWrapper";

const ax = wrappers();


const state = {
    accounts: [],
    pageMeta: null,
    accountError: null
};

const getters = {
    accounts: state => state.accounts,
    firstAccount: state => state.accounts.find( a => a ),
    accountPageMeta: state => state.pageMeta,
    accountById: state => id => state.accounts.find( account => account.id === id )
};

const actions = {
    async fetchAccounts( { commit }, queryString = "" ) {
        await ax.get( `/api/v1/ingest/account/${queryString}` )
            .then( response =>
                commit('setAccountsMutation',response.data )
            ).catch( error =>
                commit( 'setAccountErrorMutation', error.response )
            );
    },
 
    async addAccount( {commit}, data ) {
        commit( 'addAccountMutation', data );
    },

    async addAccountMetadata( {commit}, data ) {
        commit( 'addAccountMetaMutation', data );
    },

    async editAccountData( {commit}, data ) {
        commit( 'editAccountMutation', data );
    },

    async deleteAccountData( {commit}, id ) {
        commit( 'deleteAccountMutation', id );
    }
}

const mutations = {
    setAccountsMutation( state, response ) {
        state.accounts = response.data;
        state.pageMeta = response.meta;
    },
    addAccountMutation( state, response ) {
        state.accounts = state.accounts.concat( response.data );
    },
    addAccountMetadataMutation( state, response ) {
        /* Is this neccesary, or should we just mutate the account? */
        let accountIndex = state.accounts.findIndex( account => account.id === response.data.id );
        let account = accounts[accountIndex];
        account.metadata = response.data.metadata;      // replace metadata
        accountIndex[accounts] = account;               // update account in place
    },
    editAccountMutation( state, response ) {
        let accountIndex = state.accounts.findIndex( account => account.id === response.data.id );
        accounts[accountIndex] = response.data;       // update account in place
    },
    deleteAccountMutation( state, id ) {
        state.accounts = state.accounts.filter(account => account.id !== id);
    },
    setAccountErrorMutation( state, error ) {
        state.accountError = error;
    },
}

export default {
    state,
    getters,
    actions,
    mutations
}
