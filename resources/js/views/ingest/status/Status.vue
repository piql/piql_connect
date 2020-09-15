<template>
    <div class="w-100">
        <page-heading 
        icon="fa-clipboard-check" 
        :title="$t('ingest.offlineStorage.status.title')" 
        :ingress="$t('ingest.offlineStorage.status.ingress')" />

        <breadcumb/>

        <div class="card">
            <div class="card-header">
                <b><i class="fa fa-clipboard-check"></i> {{ $t('ingest.offlineStorage.status.title') }}</b>
            </div>
            <div class="card-body">
                <status-list :job-list-url="'/api/v1/ingest/storage/offline/archive'"/>
            </div>
        </div>

        
    </div>
</template>

<script>
export default  {
    data() {
    },
}
