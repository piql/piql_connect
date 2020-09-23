
const state = {
    holdings: []
}

const getters = {
    retrievedHoldings: state => state.holdings

}

const actions = {

    async addHolding({commit},data){
        commit('addHoldingMutation',data);
    },

    async addHoldingMetadata({commit},data){
        commit('addHoldingMetaMutation',data);

    },

    async editHoldingData({commit}, data){
        commit('editHoldingMutation', data);
    },

    async deleteHoldingData({commit}, id){
        commit('deleteHoldingMutation', id);
    }

}

const mutations = {
    addHoldingMutation:(state, data) => {
        state.holdings.push(data);
    },
    addHoldingMetaMutation: (state, data) => {
        let holding = state.holdings.find(holding => holding.id === data.id) || {};
        holding.metadata = data.metadata.metadata

        state.holdings.forEach(a => {
            if(a.id == data.id){
                a = holding[0]
            }
            
        });
    },
    editHoldingMutation: (state, data) => {
        let holding = state.holdings.filter(holding => holding.id === data.id);
        holding[0].title= data.title;
        holding[0].description= data.description
        holding[0].archiveId = data.archiveId

        state.holdings.forEach(a => {
            if(a.id == data.id){
                a = holding[0]
            }
            
        });
    },
    deleteHoldingMutation: (state, id) => {
        state.holdings = state.holdings.filter(holding => holding.id !== id);

    }



}

export default {
    state,
    getters,
    actions,
    mutations
}
