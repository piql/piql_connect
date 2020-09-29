<template>
    <div class="w-100">
        <archive-metadata :archiveId="archiveId"/>
        <page-heading icon="fa-archive" :title="$t('settings.archives.title')" :ingress="$t('settings.archives.description')" />

        <div class="card">
            <div class="card-header">
                <span v-if="enableMetaForm"><i class="fa fa-tags"></i> {{ $t('settings.archives.assignMeta').toUpperCase() }} | <button class="btn btn-primary" @click="disableMetaForm">{{$t('settings.archives.backToArchives')}}</button></span>
                <button v-else class="btn" @click="newArchiveForm"><i class="fa fa-plus"></i> {{$t('settings.archives.add')}}</button>
            </div>
            <div class="card-body">
                <archives-listing v-show="!enableMetaForm" @assignMeta='assignMeta' :accountId='1' :archives='archives' />
            </div>
        </div>

        <div class="row text-center pagerRow">
            <div class="col">
                <Pager :meta='archivePageMeta' />
            </div>
        </div>

        <b-modal id="create-archive" size="lg" hide-footer>
            <template v-slot:modal-title>
                <h4> <b>{{$t('settings.archives.create').toUpperCase()}}</b></h4>
            </template>
            <b-form v-on:submit.prevent='makeArchive'>
                <b-form-group id="input-group-2" :label="$t('settings.archives.title')" label-for="input-1">
                    <b-form-input
                        id="input-1"
                        v-model="createForm.title"
                        required
                        :placeholder="$t('settings.archives.title')" />
                </b-form-group>

                <b-form-group id="input-group-2" :label="$t('settings.archives.description')" label-for="input-2">
                    <b-form-textarea
                        id="input-2"
                        v-model="createForm.description"
                        :placeholder="$t('settings.archives.description')"
                        rows="5"
                        max-rows="6"
                        required />
                </b-form-group>
                <b-button type="submit" variant="primary">{{$t('settings.archives.add')}}</b-button>
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
        ...mapGetters(['templates', 'templateById', 'firstAccount', 'archives', 'archivePageMeta']),
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
        archiveId: {
            get: function(){
                return this.selectedArchiveId || 0;
            },
            set: function( value ) {
                this.selectedArchiveId = value;
            }
        },
    },
    methods: {
        ...mapActions(['fetchArchives', 'fetchAccounts','createArchive']),
        async dispatchRouting() {
            await this.fetchArchives({ accountId: 1, query: this.apiQueryString });
        },
        newArchiveForm(){
            this.$bvModal.show('create-archive');
        },
        async makeArchive() {
            const accountId = 1;
            this.createArchive( {
                title: this.createForm.title,
                description: this.createForm.description,
                accountId,
            } );
            this.$bvModal.hide('create-archive');
            this.successToast(
                this.$t('settings.archives.toast.createdArchiveHeading'),
                this.$t('settings.archives.toast.createdArchive') + ' ' + this.createForm.title
            );
            await this.fetchArchives({ accountId: 1, query: this.apiQueryString });
            this.createForm = { title: '', description: '' };
        },

        async assignMeta( archiveId ){
            this.archiveId = archiveId;
            await Vue.nextTick();
            this.$bvModal.show( "edit-archive-metadata" );
        },
        disableMetaForm(){
            this.$bvModal.hide( "edit-archive-metadata" );
        }
    }

}
</script>
