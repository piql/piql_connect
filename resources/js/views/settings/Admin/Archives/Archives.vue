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
                <archives-listing v-show="!enableMetaForm" @assignMeta='assignMeta' />
            </div>
        </div>
    </div>
</template>

<script>
import {mapGetters, mapActions} from "vuex";
export default {
    data() {
        return {
            enableMetaForm: false,
            selectedArchiveId: 0,
        }
    },
    async mounted() {
        await this.fetchAccounts();
        await this.fetchArchives( this.firstAccount.id ); //TODO: Actual account handling
    },
    computed: {
        ...mapGetters(['templates', 'templateById', 'firstAccount']),
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
        ...mapActions(['fetchArchives', 'fetchAccounts']),
        newArchiveForm(){
            this.$router.push({ name:'settings.admin.archives.create'});
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
