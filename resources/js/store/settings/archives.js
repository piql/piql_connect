
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
    },

    async addArchiveMetadata({commit},data){
        commit('addArchiveMetaMutation',data)

    }

}

const mutations = {
    addArchiveMutation:(state, data) => {
        state.archives.push(data);
    },
    addArchiveMetaMutation: (state, data) => {
        let archive = state.archives.filter(archive => archive.id === data.id);
        archive[0].metadata = data.metadata.metadata

        state.archives.forEach(a => {
            if(a.id == data.id){
                a = archive[0]
            }
            
        });
        //state.archives.push(archive);
    }


}

export default {
    state,
    getters,
    actions,
    mutations
}