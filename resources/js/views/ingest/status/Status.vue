<template>

    <div class="w-100">
        <page-heading icon="fa-clipboard-check" :title="$t('ingest.offlineStorage.status.title')" :ingress="$t('ingest.offlineStorage.status.ingress')" />

        <status-list :job-list-url="'/api/v1/ingest/storage/offline/archive'"/>
    </div>
</template>

<script>
export default  {
    data() {
    },
}
