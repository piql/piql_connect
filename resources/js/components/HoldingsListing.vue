<template>
<div class="table-responsive">
    <table class="table table-hover table-sm table-bordered">
        <thead>
            <tr>
                <th>{{$t('settings.holdings.holding')}}</th>
                <th>{{$t('settings.holdings.holdingDesc')}}</th>
                <th>{{$t('settings.holdings.createdAt')}}</th>
                <th width="20%">{{$t('settings.holdings.actions')}}</th>
            </tr>
        </thead>
        <tbody>
            <tr v-if="retrievedHoldings.length <= 0">
                <td colspan="4" class="columnCenter">{{$t('settings.holdings.noHoldings')}}</td>
            </tr>
            <tr v-else v-for="holding in retrievedHoldings" :key="holding.id">
                <td>{{ holding.title }}</td>
                <td>{{ holding.description }}</td>
                <td>{{ formatShortDate(holding.created) }}</td>
                <td>
                    <a class="btn" @click="assignMeta(holding.id)" data-toggle="tooltip" :title="$t('settings.holdings.assignMeta')">
                        <i class="fa fa-tags buttonIcon"></i>
                    </a>
                    <a class="btn" @click="showEditBox(holding.id)" data-toggle="tooltip" :title="$t('settings.holdings.edit')">
                        <i class="fa fa-edit buttonIcon"></i>
                    </a>
                    <!-- <a class="btn" @click="showDeleteBox(holding.id)" data-toggle="tooltip" :title="$t('settings.holdings.delete')">
                        <i class="fa fa-trash buttonIcon"></i>
                    </a> -->
                </td>
            </tr>
        </tbody>
    </table>

    <b-modal id="delete-holding" hide-footer>
        <template v-slot:modal-title>
            <h4> <b>{{$t('settings.holdings.delete').toUpperCase()}}[ {{ holding.title.toUpperCase() }} ]</b></h4>
        </template>
        <div class="d-block">
            <b-alert show variant="warning">{{$t('settings.holdings.deleteWarning')}}</b-alert>
        </div>
        <b-button class="mt-3" block @click="deleteHolding">
            <i class="fa fa-trash"></i> {{$t('settings.holdings.delete').toUpperCase()}}</b-button>
    </b-modal>

    <b-modal id="edit-holding" size="lg" hide-footer>
        <template v-slot:modal-title>
        <h4> <b>{{$t('settings.holdings.edit').toUpperCase()}} [ {{ holding.title.toUpperCase() }} ]</b></h4>
        </template>
        <div class="d-block">
            <div class="form-group">
                <label>{{$t('settings.holdings.holding')}}</label>
                <input type="text" class="form-control" v-model="edit.title" >
            </div>

            <b-form-select v-model="edit.archiveId" :options="options"></b-form-select>

            <div class="form-group">
                <label>{{$t('settings.groups.description')}}</label>
                <textarea v-model="edit.description" class="form-control"></textarea>
            </div>
        </div>
        <b-button class="mt-3" @click="editHolding" block><i class="fa fa-edit"></i> {{$t('settings.holdings.edit').toUpperCase()}}</b-button>
    </b-modal>


</div>
  
</template>

<script>
import { mapGetters, mapActions } from "vuex"
export default {
    async mounted(){
        this.options = this.retrievedArchives.map(archive => {
            return {
                text: archive.title,
                value: archive.id
            }
        })

    },
    data(){
        return {
            selection: '',
            holding: null,
            edit: {
                title: '',
                description: '',
                archiveId: ''
            },
            options: []
        }

    },
    computed: {
        ...mapGetters(['retrievedHoldings','templates','retrievedArchives']),
    },
    methods: {
        ...mapActions(['addHoldingMetadata','editHoldingData','deleteHoldingData']),
        assignMeta(id){
            this.$emit('assignMeta', id);
        },
        showEditBox(id){
            let holding = this.retrievedHoldings.filter(single => single.id === id)
            this.holding = holding[0];
            this.edit.title = this.holding.title;
            this.edit.description = this.holding.description;
            this.edit.archiveId = this.holding.archiveId
            this.$bvModal.show('edit-holding')

        },
        showDeleteBox(id){
            let holding = this.retrievedHoldings.filter(single => single.id === id)
            this.holding = holding[0];
            this.$bvModal.show('delete-holding')

        },
        deleteHolding(){
            this.deleteHoldingData(this.holding.id);

            this.$bvModal.hide('delete-holding')


            this.successToast(
                this.$t('settings.holdings.toast.deletingHoldings'), 
                this.$t('settings.holdings.toast.deletingHolding') + ' ' + this.holding.title
            );


        },
        editHolding(){
            let data = {
                title: this.edit.title,
                description: this.edit.description,
                archiveId: this.edit.archiveId,
                id: this.holding.id
            }

            this.editHoldingData(data);

            this.$bvModal.hide('edit-holding')


            this.successToast(
                this.$t('settings.holdings.toast.editingHolding'), 
                this.$t('settings.holdings.toast.editingHolding') + ' ' + this.edit.title
            );

        },
    }

}
</script>
