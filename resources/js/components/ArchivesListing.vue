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
            <tr v-if="archives.length == 0">
                <td colspan="4" class="columnCenter">{{$t('settings.archives.noArchives')}}</td>
            </tr>
            <tr v-for="archive in archives">
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
                </td>
            </tr>
        </tbody>
    </table>

    <b-modal id="edit-archive" size="lg" hide-footer>
        <template v-slot:modal-title>
        <h4> <b>{{$t('settings.archives.edit').toUpperCase()}} [ {{ archive.title.toUpperCase() }} ]</b></h4>
        </template>
        <div class="d-block">
            <div class="form-group">
                <label>{{$t('settings.archives.archive')}}</label>
                <input type="text" class="form-control" v-model="title" >
            </div>
            <div class="form-group">
                <label>{{$t('settings.groups.description')}}</label>
                <textarea v-model="description" class="form-control"></textarea>
            </div>
        </div>
        <b-button class="mt-3" @click="editArchive" block><i class="fa fa-edit"></i> {{$t('settings.archives.edit').toUpperCase()}}</b-button>
    </b-modal>

    <b-modal id="assign-template" hide-footer>
        <template v-slot:modal-title>
        <h4>{{$t('settings.archives.assignMetaTemplate').toUpperCase()}} | {{ title }}</h4>
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
    async mounted() {
        await this.fetchAccounts();
        await this.fetchArchives( this.firstAccount.id ); //TODO: Actual account handling
    },
    data(){
        return {
            selection: '',
            _id: '',
            _title: '',
            _description: ''
        }

    },
    computed: {
        ...mapGetters(['firstAccount', 'archives','templates']),
        archive: {
            get: function() {
                return {
                    id: this._id,
                    title: this._title,
                    description: this._description
                };
            },
            set: function( value ) {
                this._id = value.id || "",
                this._title = value.title || "";
                this._description = value.description || "";
            }
        },
        title: {
            get: function( ) {
                return this._title;
            },
            set: function( value ) {
                this._title = value;
            }
        },
        description: {
            get: function( ) {
                return this._description;
            },
            set: function( value ) {
                this._description = value;
            }
        },
    },
    methods: {
        ...mapActions(['fetchAccounts', 'fetchArchives','addArchive','editArchiveData']),
        assignMeta(id) {
            this.$emit('assignMeta', id);
        },
        showTemplateBox( id ){
            this.archive = this.archives.find( single => single.id === id );
            this.$bvModal.show( 'assign-template' );
        },
        showEditBox( id ){
            this.archive = this.archives.find( single => single.id === id );
            this.$bvModal.show('edit-archive')
        },
        async editArchive(){
            this.$bvModal.hide('edit-archive')
            await this.editArchiveData( {
                id: this.editArchiveId,
                accountId: this.firstAccount.id,
                title: this.title,
                description: this.description
            } );
            this.successToast(
                this.$t('settings.archives.toast.editingArchive'), 
                this.$t('settings.archives.toast.editingArchive') + ' ' + this.title
            );

        },
        async assignTemplate(){
            this.$bvModal.hide( 'assign-template' )
            let template = this.templates.find( single => single.id === this.selection );
            const data = { 
                id: this.archive.id,
                metadata:  {
                    metadata: template.metadata.dc
                } 
            };
            await this.addArchiveMetadata( data );
            this.successToast(
                this.$t('settings.archives.toast.addingArchiveMeta'), 
                this.$t('settings.archives.toast.addingArchiveMeta') + ' ' + this.title
            );
        }
    }

}
</script>
