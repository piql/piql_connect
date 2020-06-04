<template>

    <div class="w-100">
        <page-heading icon="fa-list-ul" :title="$t('ingest.offlineStorage.status.title')" :ingress="$t('ingest.offlineStorage.status.ingress')" />

        <status-list :job-list-url="'/api/v1/ingest/offline_storage/archive'"/>
    </div>
</template>

<script>
export default  {
    data() {
    },
}
