<template>
<div class="table-responsive">
    <table class="table table-hover table-sm table-bordered">
        <thead>
            <tr>
                <th>{{$t('settings.archives.archive')}}</th>
                <th>{{$t('settings.archives.archiveDesc')}}</th>
                <th>{{$t('settings.archives.createdAt')}}</th>
                <th width="20%">{{$t('settings.archives.actions')}}</th>
            </tr>
        </thead>
        <tbody>
            <tr v-if="retrievedArchives.length <= 0">
                <td colspan="4" class="columnCenter">{{$t('settings.archives.noArchives')}}</td>
            </tr>
            <tr v-else v-for="archive in retrievedArchives" :key="archive.id">
                <td>{{ archive.title }}</td>
                <td>{{ archive.description }}</td>
                <td>{{ formatShortDate(archive.created) }}</td>
                <td>
                    <a class="btn" @click="assignMeta(archive.id)" data-toggle="tooltip" :title="$t('settings.archives.assignMeta')">
                        <i class="fa fa-tags buttonIcon"></i>
                    </a>
                    <a class="btn" @click="showEditBox(archive.id)" data-toggle="tooltip" :title="$t('settings.archives.edit')">
                        <i class="fa fa-edit buttonIcon"></i>
                    </a>
                    <!-- <a class="btn" @click="showDeleteBox(archive.id)" data-toggle="tooltip" :title="$t('settings.archives.delete')">
                        <i class="fa fa-trash buttonIcon"></i>
                    </a> -->
                </td>
            </tr>
        </tbody>
    </table>

    <b-modal id="delete-archive" hide-footer>
        <template v-slot:modal-title>
            <h4> <b>{{$t('settings.archives.delete').toUpperCase()}}[ {{ archive.title.toUpperCase() }} ]</b></h4>
        </template>
        <div class="d-block">
            <b-alert show variant="warning">{{$t('settings.archives.deleteWarning')}}</b-alert>
        </div>
        <b-button class="mt-3" block @click="deleteArchive">
            <i class="fa fa-trash"></i> {{$t('settings.archives.delete').toUpperCase()}}</b-button>
    </b-modal>

    <b-modal id="edit-archive" size="lg" hide-footer>
        <template v-slot:modal-title>
        <h4> <b>{{$t('settings.archives.edit').toUpperCase()}} [ {{ archive.title.toUpperCase() }} ]</b></h4>
        </template>
        <div class="d-block">
            <div class="form-group">
                <label>{{$t('settings.archives.archive')}}</label>
                <input type="text" class="form-control" v-model="edit.title" >
            </div>
            <div class="form-group">
                <label>{{$t('settings.groups.description')}}</label>
                <textarea v-model="edit.description" class="form-control"></textarea>
            </div>
        </div>
        <b-button class="mt-3" @click="editArchive" block><i class="fa fa-edit"></i> {{$t('settings.archives.edit').toUpperCase()}}</b-button>
    </b-modal>


</div>
  
</template>

<script>
import { mapGetters, mapActions } from "vuex"
export default {
    async mounted(){
        this.fetchArchives();

    },
    data(){
        return {
            selection: '',
            archive: null,
            edit: {
                title: '',
                description: ''
            }
        }

    },
    computed: {
        ...mapGetters(['retrievedArchives','templates']),
    },
    methods: {
        ...mapActions(['fetchArchives','addArchiveMetadata','editArchiveData','deleteArchiveData']),
        assignMeta(id){
            this.$emit('assignMeta', id);
        },
        showEditBox(id){
            let archive = this.retrievedArchives.filter(single => single.id === id)
            this.archive = archive[0];
            this.edit.title = this.archive.title;
            this.edit.description = this.archive.description;
            this.$bvModal.show('edit-archive')

        },
        showDeleteBox(id){
            let archive = this.retrievedArchives.filter(single => single.id === id)
            this.archive = archive[0];
            this.$bvModal.show('delete-archive')

        },
        deleteArchive(){
            this.deleteArchiveData(this.archive.id);

            this.$bvModal.hide('delete-archive')


            this.successToast(
                this.$t('settings.archives.toast.deletingArchive'), 
                this.$t('settings.archives.toast.deletingArchive') + ' ' + this.archive.title
            );


        },
        editArchive(){
            let data = {
                title: this.edit.title,
                description: this.edit.description,
                id: this.archive.id
            }

            this.editArchiveData(data);

            this.$bvModal.hide('edit-archive')


            this.successToast(
                this.$t('settings.archives.toast.editingArchive'), 
                this.$t('settings.archives.toast.editingArchive') + ' ' + this.edit.title
            );

        },
    }

}
</script>
