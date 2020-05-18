<template>
    <div class="w-100">
        <page-heading icon="fa-tags" :title="$t('ingest.offlineStorage.package.metadata.title')" :ingress="$t('ingest.offlineStorage.package.metadata.title')" />
        <dublin-core baseUrl="/api/v1/ingest/offline_storage/pending/jobs"> </dublin-core>
    </div>
</template>

<script>
export default {
}
</script>
