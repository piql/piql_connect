<template>
    <div class="w-100">
        <page-heading icon="fa-folder" :title="$t('settings.holdings.title')" :ingress="$t('settings.holdings.description')" />
        <div class="card">
            <div class="card-header">
                <span v-if="enableHoldingMetadataForm"><i class="fa fa-tags"></i> {{ $t('settings.holdings.assignMeta').toUpperCase() }}
                    <button class="btn btn-primary" @click="disableMetaForm">{{$t('settings.holdings.backToHoldings')}}</button>
                </span>
                <button v-else class="btn" @click="newHoldingForm"><i class="fa fa-plus"></i> {{$t('settings.holdings.add')}}</button>
            </div>
            <div class="card-body">
                <holding-metadata :originalHolding="holdingForMetadataEdit" :archiveId='firstArchiveId' :collectionId='collectionId' @disableMetaForm='disableMetaForm' />
                <holdings-listing @assignHoldingMetadata='assignHoldingMetadata' :archiveId='firstArchiveId' :parentCollectionId='collectionId' :holdings='holdings' />
            </div>
        </div>

        <div class="row text-center pagerRow">
            <div class="col">
                <Pager :meta='holdingPageMeta' />
            </div>
        </div>


        <b-modal id="create-holding" size="lg" hide-footer>
            <template v-slot:modal-title>
                <h4> <b>{{$t('settings.holdings.create').toUpperCase()}}</b></h4>
            </template>
            <b-form v-on:submit.prevent='makeHolding'>
                <b-form-group id="input-group-2" :label="$t('settings.holdings.holding')" label-for="input-1">
                    <b-form-input
                        id="input-1"
                        v-model="createForm.title"
                        required
                        :placeholder="$t('settings.holdings.holding')" />
                </b-form-group>

                <b-form-group id="input-group-2" :label="$t('settings.groups.description')" label-for="input-2">
                    <b-form-textarea
                        id="input-2"
                        v-model="createForm.description"
                        :placeholder="$t('settings.groups.description')"
                        rows="5"
                        max-rows="6"
                        required />
                </b-form-group>
                <b-button type="submit" variant="primary">{{$t('settings.holdings.add')}}</b-button>
            </b-form>
        </b-modal>
    </div>
</template>

<script>
import {mapGetters, mapActions} from "vuex";
import VueRouter from 'vue-router';
import RouterTools from '@mixins/RouterTools.js';
export default {

    mixins: [ RouterTools ],

    data() {
        return {
            enableHoldingMetadataForm: false,
            collectionId: 0,
            selectedHoldingId: 0,
            firstArchiveId: 1,
            holdingForMetadataEdit : null,
            createForm: {
                title: '',
                description: ''
            },
        }
    },
    async mounted() {
        await this.dispatchRouting();
    },
    watch: {
        '$route': 'dispatchRouting'
    },
    computed: {
        ...mapGetters(['templates', 'templateById', 'firstArchive', 'collections', 'collectionById','holdings','holdingById','holdingPageMeta']),
        apiQueryString: function() {
            let routeQuery = this.$route.query;
            let apiQueryItems = [];
            let filters = [];
            let page;
            if( parseInt( routeQuery.page ) ) {
                filters = filters.concat(`page=${routeQuery.page}`);
            }
            return "?".concat( filters.filter( (f) => f ).join( "&" ) );
        },
    },
    methods: {
        ...mapActions(['fetchArchives', 'fetchCollections', 'fetchHoldingsForCollection', 'addHoldingMetadata','editHoldingData','createHolding']),
        async dispatchRouting() {
            this.collectionId = parseInt( this.$route.params.collectionId );
            this.fetchHoldingsForCollection({ archiveId: 1, collectionId: this.collectionId, query: this.apiQueryString });
            await Vue.nextTick();
        },
        newHoldingForm(){
            this.$bvModal.show('create-holding');
        },
        async makeHolding() {
            let archiveId = 1;
            this.createHolding( {
                title: this.createForm.title,
                description: this.createForm.description,
                archiveId,
                collectionId: this.collectionId
            } );
            this.$bvModal.hide('create-holding');
            this.successToast(
                this.$t('settings.collections.toast.createdHoldingHeading'),
                this.$t('settings.collections.toast.createdHolding') + ' ' + this.createForm.title
            );
            await this.fetchHoldingsForCollection( {archiveId, collectionId: this.collectionId, query: this.apiQueryString } );
            this.createForm = { title: '', description: '' };
        },
        assignHoldingMetadata( holding ) {
            this.holdingForMetadataEdit = holding;
            this.$bvModal.show('edit-holding-metadata');
        },
        disableMetaForm(){
            this.enableHoldingMetadataForm = false;
            this.holdingForMetadataEdit = null;
        }
    }
}
</script>
