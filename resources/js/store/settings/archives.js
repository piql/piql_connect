
const state = {
    archives: []

}

const getters = {
    retrievedArchives: state => state.archives

}

const actions = {
    async fetchArchives({commit}){
        
    },

    async addArchive({commit},data){
        commit('addArchiveMutation',data);
    }

}

const mutations = {
    addArchiveMutation:(state, data) => {
        state.archives.push(data);
    },
    addArchiveMetaMutation: (state, data) => {
        let archive = state.archives.filter(archive => archive.id === data.id);
        archive.push(data.metadata);

        state.archives = state.archives.filter(archive => archive.id !== data.id);
        state.archives.push(archive);
    }


}

export default {
    state,
    getters,
    actions,
    mutations
}