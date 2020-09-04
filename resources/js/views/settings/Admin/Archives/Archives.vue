<template>
<div class="w-100">
    <page-heading icon="fa-archive" :title="$t('settings.archives.title')" :ingress="$t('settings.archives.description')" />
    <div class="card">
        <div class="card-header">
            <span v-if="enableMetaForm"><i class="fa fa-tags"></i> {{ $t('settings.archives.assignMeta').toUpperCase() }}</span>
            <button v-else class="btn" @click="newArchiveForm"><i class="fa fa-plus"></i> {{$t('settings.archives.add')}}</button>
        </div>
        <div class="card-body">
            <archive-metadata v-if="enableMetaForm" :archiveId="archiveId" />
            <archives-listing v-else @assignMeta='assignMeta' />
        </div>
    </div>

</div>
  
</template>

<script>
export default {
    data() {
        return {
            enableMetaForm: false,
            archiveId: null
        }
    },
    methods: {
        newArchiveForm(){
            this.$router.push({ name:'settings.admin.archives.create'});
        },
        assignMeta(archiveId){
            this.enableMetaForm = true;
            this.archiveId = archiveId

        }
    }

}
</script>
