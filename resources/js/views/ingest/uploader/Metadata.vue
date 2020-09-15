<template>
    <div class="w-100">
        <page-heading icon="fa-tags" :title="$t('ingest.metadata.file.title')" :ingress="$t('ingest.metadata.file.ingress')" />
        <dublin-core baseUrl="/api/v1/ingest/files" baseFileUrl="/api/v1/ingest/bags/files/"> </dublin-core>
    </div>
</template>

<script>
export default {
     
}
</script>
