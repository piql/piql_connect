
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
        commit('addArchiveMetaMutation',data);

    },

    async editArchiveData({commit}, data){
        commit('editArchiveMutation', data);
    },

    async deleteArchiveData({commit}, id){
        commit('deleteArchiveMutation', id);
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
    },
    editArchiveMutation: (state, data) => {
        let archive = state.archives.filter(archive => archive.id === data.id);
        archive[0].title= data.title;
        archive[0].description= data.description

        state.archives.forEach(a => {
            if(a.id == data.id){
                a = archive[0]
            }
            
        });
    },
    deleteArchiveMutation: (state, id) => {
        state.archives = state.archives.filter(archive => archive.id !== id);

    }



}

export default {
    state,
    getters,
    actions,
    mutations
}