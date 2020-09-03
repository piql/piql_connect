<template>
<div class="w-100">
    <page-heading icon="fa-archive" :title="$t('settings.archives.title')" :ingress="$t('settings.archives.description')" />
    <div class="card">
        <div class="card-header">
            <button class="btn" @click="newArchiveForm"><i class="fa fa-plus"></i> {{$t('settings.archives.add')}}</button>
        </div>
        <div class="card-body">
            <archive-listing></archive-listing>
        </div>
    </div>

</div>
  
</template>

<script>
export default {
    methods: {
        newArchiveForm(){
            this.$router.push({ name:'settings.admin.archives.newarchive'});
        }
    }

}
</script>
