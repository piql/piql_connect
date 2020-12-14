<template>
<div class="table-responsive">
    <table class="table table-hover table-sm table-bordered">
        <thead>
            <tr>
                <th>{{$t('settings.collections.collection')}}</th>
                <th>{{$t('settings.collections.archiveDesc')}}</th>
                <th>{{$t('settings.collections.createdAt')}}</th>
                <th width="20%">{{$t('settings.collections.actions')}}</th>
            </tr>
        </thead>
        <tbody>
            <tr v-if="collections.length == 0">
                <td colspan="4" class="columnCenter">{{$t('settings.collections.noCollections')}}</td>
            </tr>
            <tr v-for="collection in collections">
                <td>{{ collection.title }}</td>
                <td>{{ collection.description }}</td>
                <td>{{ formatShortDate(collection.created) }}</td>
                <td>
                    <a class="btn" @click="assignMeta(collection.id)" data-toggle="tooltip" :title="$t('settings.collections.assignMeta')">
                        <i class="fa fa-tags buttonIcon"></i>
                    </a>
                    <a class="btn" @click="showEditBox(collection)" data-toggle="tooltip" :title="$t('settings.collections.edit')">
                        <i class="fa fa-edit buttonIcon"></i>
                    </a>
                    <a class="btn" @click="navigateToHoldingsFor(collection.id)" :title="$t('sidebar.settings.holdings')">
                        <i class="fa fa-folder buttonIcon"></i>
                    </a>

                </td>
            </tr>
        </tbody>
    </table>

    <b-modal id="edit-collection" size="lg" hide-footer>
        <template v-slot:modal-title>
        <h4> <b>{{$t('settings.collections.edit').toUpperCase()}} [ {{ edit.title.toUpperCase() }} ]</b></h4>
        </template>
        <div class="d-block">
            <div class="form-group">
                <label>{{$t('settings.collections.collection')}}</label>
                <input type="text" class="form-control" v-model="edit.title" >
            </div>
            <div class="form-group">
                <label>{{$t('settings.groups.description')}}</label>
                <textarea v-model="edit.description" class="form-control"></textarea>
            </div>
        </div>
        <b-button class="mt-3" @click="editCollection" block><i class="fa fa-edit"></i> {{$t('settings.collections.edit').toUpperCase()}}</b-button>
    </b-modal>

    <b-modal id="assign-template" hide-footer>
        <template v-slot:modal-title>
        <h4>{{$t('settings.collections.assignMetaTemplate').toUpperCase()}} | {{ title }}</h4>
        </template>
        <div class="d-block">
            <div class="form-group">
                <label>{{$t('settings.collections.template')}}</label>
                <select class="form-control" v-model="selection">
                    <option v-for="template in templates" :key="template.id" :value="template.id">
                        {{ template.metadata.dc.title }}
                    </option>
                </select>
            </div>
        </div>
        <b-button class="mt-3" block @click="assignTemplate">
            <i class="fa fa-list"></i> {{$t('settings.collections.assignMetaTemplate')}}
        </b-button>
    </b-modal>

</div>

</template>

<script>
import { mapGetters, mapActions } from "vuex"
export default {
    data(){
        return {
            selection: '',
            edit: {},
        }
    },
    props: {
        accountId: {
            type: Number,
            default: 1
        },
        collections: {
            type: Array,
            default: () => []
        },
    },
    computed: {
        ...mapGetters(['templates']),
    },
    methods: {
        ...mapActions(['addCollection','editCollectionData']),
        navigateToHoldingsFor( collectionId ) {
            this.$router.push({path: `/settings/admin/collections/${collectionId}/holdings` });
        },
        assignMeta(id) {
            this.$emit('assignMeta', id);
        },
        showTemplateBox( id ){
            this.collection = this.collections.find( single => single.id === id );
            this.$bvModal.show( 'assign-template' );
        },
        showEditBox( collection ){
            this.edit = {
                id: collection.id,
                title: collection.title ?? "",
                description: collection.description ?? "",
            };
            this.$bvModal.show('edit-collection')
        },
        async editCollection(){
            this.$bvModal.hide('edit-collection')
            await this.editCollectionData( {
                id: this.edit.id,
                accountId: this.accountId,
                title: this.edit.title,
                description: this.edit.description
            } );
            this.successToast(
                this.$t('settings.collections.toast.editingCollection'),
                this.$t('settings.collections.toast.editingCollection') + ' ' + this.title
            );

        },
        async assignTemplate(){
            this.$bvModal.hide( 'assign-template' )
            let template = this.templates.find( single => single.id === this.selection );
            const data = {
                id: this.collection.id,
                metadata:  {
                    metadata: template.metadata.dc
                }
            };
            await this.addCollectionMetadata( data );
            this.successToast(
                this.$t('settings.collections.toast.addingCollectionMeta'),
                this.$t('settings.collections.toast.addingCollectionMeta') + ' ' + this.title
            );
        }
    }

}
</script>
