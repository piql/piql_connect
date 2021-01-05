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
                <tr v-if="holdings.length === 0">
                    <td colspan="4" class="columnCenter">{{$t('settings.holdings.noHoldings')}}</td>
                </tr>
                <tr v-else v-for="holding in holdings" :key="holding.id">
                    <td>{{ holding.title }}</td>
                    <td>{{ holding.description }}</td>
                    <td>{{ formatShortDate(holding.created) }}</td>
                    <td>
                        <a class="btn" @click="assignHoldingMetadata(holding)" data-toggle="tooltip" :title="$t('settings.holdings.assignMeta')">
                            <i class="fa fa-tags buttonIcon"></i>
                        </a>
                        <a class="btn" @click="showEditBox(holding)" data-toggle="tooltip" :title="$t('settings.holdings.edit')">
                            <i class="fa fa-edit buttonIcon"></i>
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>

        <b-modal id="edit-holding" size="lg" hide-footer>
            <template v-slot:modal-title>
                <h4> <b>{{$t('settings.holdings.edit').toUpperCase()}} [ {{ edit.title.toUpperCase() }} ]</b></h4>
            </template>
            <div class="d-block">
                <div class="form-group">
                    <label>{{$t('settings.holdings.holding')}}</label>
                    <input type="text" class="form-control" v-model="edit.title" >
                </div>

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
    },
    data(){
        return {
            selection: '',
            edit: {
                id: '',
                title: '',
                description: '',
                collectionId: ''
            },
        }

    },
    computed: {
        ...mapGetters(['templates']),
    },
    props: {
        archiveId: {
            type: Number,
            default: 1
        },
        parentCollectionId: {
            type: Number,
            default: 0
        },
        holdings: {
            type: Array,
            default: () => []
        },
    },
    methods: {
        ...mapActions(['fetchHoldings', 'addHoldingMetadata','editHoldingData']),
        assignHoldingMetadata( holding ){
            this.$emit( 'assignHoldingMetadata', holding );
        },
        showEditBox( holding ){
            this.edit = {
                id: holding.id,
                title: holding.title ?? "",
                description: holding.description ?? "",
                collectionId: this.parentCollectionId
            };
            this.$bvModal.show('edit-holding');
        },
        editHolding(){
            let data = {
                title: this.edit.title,
                description: this.edit.description,
                collectionId: this.edit.collectionId,
                id: this.edit.id,
                archiveId: this.archiveId,
                collectionId: this.parentCollectionId
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
