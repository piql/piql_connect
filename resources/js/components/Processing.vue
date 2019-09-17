<template>
    <div class="container-fluid">
        <ingest-filter-search v-bind:filters="['ingest.processing.processingFilter','ingest.processing.uploadingFilter']"></ingest-filter-search>
        <div class="row plistHeader" v-show="currentlyIdle === false">
            <div class="col-sm-5">{{$t('ingest.processing.sip')}}</div>
            <div class="col-sm-3">{{$t('ingest.processing.ingestDate')}}</div>
            <div class="col-sm-3">{{$t('ingest.processing.status')}}</div>
            <div class="col-sm-1">&nbsp;</div>
        </div>

        <FileInProcess v-for="item in items" v-bind:item="item" v-bind:key="item.id"/>
        <div v-if="currentlyIdle" class="mt-5"><h3>{{$t('ingest.processing.noItems')}}</h3></div>
        </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            items : [],
            pollProcessingHandle: null,
            itemsLoaded: false
        }
    },

    computed: {
        currentlyIdle: function() {
            return this.itemsLoaded && (this.items.length == 0);
        }
    },
    methods: {
        startPollProcessing () {
            this.pollProcessingHandle = setInterval(async () => {
                this.items = (await axios.get("/api/v1/ingest/processing/")).data.data;
            }, 500);
        }
    },
    created() {
        this.startPollProcessing();
    },
    beforeDestroy(){
        clearInterval(this.pollProcessingHandle);
    },

    async mounted() {
        this.items = (await axios.get("/api/v1/ingest/processing/")).data.data;
        this.itemsLoaded = true;
    },
}
</script>
