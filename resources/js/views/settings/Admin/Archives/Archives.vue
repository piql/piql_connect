<template>
    <div class="w-100">
        <page-heading icon="fa-archive" :title="$t('settings.archives.title')" :ingress="$t('settings.archives.description')" />
        <div class="card">
            <div class="card-header">
                <span v-if="enableMetaForm"><i class="fa fa-tags"></i> {{ $t('settings.archives.assignMeta').toUpperCase() }} | <button class="btn btn-primary" @click="disableMetaForm">{{$t('settings.archives.backToArchives')}}</button></span>
                <button v-else class="btn" @click="newArchiveForm"><i class="fa fa-plus"></i> {{$t('settings.archives.add')}}</button>
            </div>
            <div class="card-body">
                <archive-metadata v-show="enableMetaForm" :archiveId="archiveId" @disableMetaForm='disableMetaForm' />
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
            selectedArchiveId: null,
        }
    },
    computed: {
        ...mapGetters(['templates', 'templateById', 'accountById','metadataByArchiveId']),
        archiveId: {
            get: function(){
                return this.selectedArchiveId;
            },
            set: function( value ) {
                this.selectedArchiveId = value;
            }
        },
        editThisMetadata() {
            return this.metadataByArchiveId( this.archiveId );
        },
    },
    methods: {
        ...mapActions(['fetchArchives']),
        newArchiveForm(){
            this.$router.push({ name:'settings.admin.archives.create'});
        },
        async assignMeta( archiveId ){
            this.archiveId = archiveId;
            await Vue.nextTick();
            this.enableMetaForm = true;
        },
        disableMetaForm(){
            this.enableMetaForm = false;
        }
    }

}
</script>
