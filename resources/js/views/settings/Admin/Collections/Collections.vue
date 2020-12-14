<template>
    <div class="w-100">
        <collection-metadata :collectionId="collectionId"/>
        <page-heading icon="fa-archive" :title="$t('settings.collections.title')" :ingress="$t('settings.collections.description')" />

        <div class="card">
            <div class="card-header">
                <span v-if="enableMetaForm"><i class="fa fa-tags"></i> {{ $t('settings.collections.assignMeta').toUpperCase() }} | <button class="btn btn-primary" @click="disableMetaForm">{{$t('settings.collections.backToCollections')}}</button></span>
                <button v-else class="btn" @click="newArchiveForm"><i class="fa fa-plus"></i> {{$t('settings.collections.add')}}</button>
            </div>
            <div class="card-body">
                <collections-listing v-show="!enableMetaForm" @assignMeta='assignMeta' :accountId='1' :collections='collections' />
            </div>
        </div>

        <div class="row text-center pagerRow">
            <div class="col">
                <Pager :meta='collectionPageMeta' />
            </div>
        </div>

        <b-modal id="create-collection" size="lg" hide-footer>
            <template v-slot:modal-title>
                <h4> <b>{{$t('settings.collections.create').toUpperCase()}}</b></h4>
            </template>
            <b-form v-on:submit.prevent='makeArchive'>
                <b-form-group id="input-group-2" :label="$t('settings.collections.title')" label-for="input-1">
                    <b-form-input
                        id="input-1"
                        v-model="createForm.title"
                        required
                        :placeholder="$t('settings.collections.title')" />
                </b-form-group>

                <b-form-group id="input-group-2" :label="$t('settings.collections.description')" label-for="input-2">
                    <b-form-textarea
                        id="input-2"
                        v-model="createForm.description"
                        :placeholder="$t('settings.collections.description')"
                        rows="5"
                        max-rows="6"
                        required />
                </b-form-group>
                <b-button type="submit" variant="primary">{{$t('settings.collections.add')}}</b-button>
            </b-form>
        </b-modal>
    </div>
</template>

<script>
import {mapGetters, mapActions} from "vuex";
export default {
    data() {
        return {
            enableMetaForm: false,
            selectedArchiveId: 0,
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
        ...mapGetters(['templates', 'templateById', 'firstAccount', 'collections', 'collectionPageMeta']),
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
        collectionId: {
            get: function(){
                return this.selectedArchiveId || 0;
            },
            set: function( value ) {
                this.selectedArchiveId = value;
            }
        },
    },
    methods: {
        ...mapActions(['fetchCollections', 'fetchAccounts','createCollection']),
        async dispatchRouting() {
            await this.fetchCollections({ accountId: 1, query: this.apiQueryString });
        },
        newArchiveForm(){
            this.$bvModal.show('create-collection');
        },
        async makeArchive() {
            const accountId = 1;
            this.createCollection( {
                title: this.createForm.title,
                description: this.createForm.description,
                accountId,
            } );
            this.$bvModal.hide('create-collection');
            this.successToast(
                this.$t('settings.collections.toast.createdArchiveHeading'),
                this.$t('settings.collections.toast.createdArchive') + ' ' + this.createForm.title
            );
            await this.fetchCollections({ accountId: 1, query: this.apiQueryString });
            this.createForm = { title: '', description: '' };
        },

        async assignMeta( collectionId ){
            this.collectionId = collectionId;
            await Vue.nextTick();
            this.$bvModal.show( "edit-collection-metadata" );
        },
        disableMetaForm(){
            this.$bvModal.hide( "edit-collection-metadata" );
        }
    }

}
</script>
