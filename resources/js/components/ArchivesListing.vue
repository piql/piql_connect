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
                     <a class="btn" @click="showTemplateBox(archive.id)" data-toggle="tooltip" :title="$t('settings.archives.assignMetaTemplate')">
                        <i class="fa fa-list buttonIcon"></i>
                    </a>
                    <a class="btn" data-toggle="tooltip" :title="$t('settings.archives.edit')">
                        <i class="fa fa-edit buttonIcon"></i>
                    </a>
                    <a class="btn" data-toggle="tooltip" :title="$t('settings.archives.delete')">
                        <i class="fa fa-trash buttonIcon"></i>
                    </a>
                </td>
            </tr>
        </tbody>
    </table>

    <b-modal id="assign-template" hide-footer>
        <template v-slot:modal-title>
        <h4>{{$t('settings.archives.assignMetaTemplate').toUpperCase()}} | {{ archive.title }}</h4>
        </template>
        <div class="d-block">
            <div class="form-group">
                <label>{{$t('settings.archives.template')}}</label>
                <select class="form-control" v-model="selection">
                    <option v-for="template in templates" :key="template.id" :value="template.id">
                        {{ template.metadata.dc.title }}
                    </option>
                </select>
            </div>
        </div>
        <b-button class="mt-3" block @click="assignTemplate">
            <i class="fa fa-list"></i> {{$t('settings.archives.assignMetaTemplate')}} 
        </b-button>
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
            archive: null
        }

    },
    computed: {
        ...mapGetters(['retrievedArchives','templates']),
    },
    methods: {
        ...mapActions(['fetchArchives','addArchiveMetadata']),
        assignMeta(id){
            this.$emit('assignMeta', id);
        },
        showTemplateBox(id){
            let archive = this.retrievedArchives.filter(single => single.id === id)
            this.archive = archive[0];
            this.$bvModal.show('assign-template')
        },
        assignTemplate(){
            let template = this.templates.filter(single => single.id === this.selection);
            let data = { 
                id: this.archive.id,
                metadata:  {
                    metadata: template[0].metadata.dc
                } 
            };

            this.addArchiveMetadata(data);
            
            this.$bvModal.hide('assign-template')


            this.successToast(
                this.$t('settings.archives.toast.addingArchiveMeta'), 
                this.$t('settings.archives.toast.addingArchiveMeta') + ' ' + this.archive.title
            );


        }
    }

}
</script>
