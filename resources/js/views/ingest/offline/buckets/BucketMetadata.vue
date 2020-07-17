<template>
    <div class="w-100">
        <page-heading icon="fa-tags" :title="$t('ingest.offlineStorage.package.metadata.title')" :ingress="$t('ingest.offlineStorage.package.metadata.title')" />
	<breadcumb :subTitle="$t('ingest.taskList.title')" :subTitleRoute="{ name: 'ingest.offline' }"/>
        <dublin-core baseUrl="/api/v1/ingest/storage/offline/pending/buckets" urlParam="bucketId" baseFileUrl="storage/offline/pending/buckets/" > </dublin-core>
    </div>
</template>

<script>
export default {
}
</script>
