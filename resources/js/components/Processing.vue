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
        <div v-for="x in padItems"><div class="row plist invisible"><div class="col">&nbsp;</div></div></div>
        <div class="row">
            <div class="col">
                <pager :meta='pageMeta' @updatePage='updatePage' />
            </div>
        </div>

        <div v-if="currentlyIdle" class="mt-5"><h3>{{$t('ingest.processing.noItems')}}</h3></div>
        </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            apiResult: null,
            currentPollUrl: '/api/v1/ingest/processing',
            pollProcessingHandle: null,
        }
    },

    computed: {
        currentlyIdle: function() {
            return this.apiResult && !this.items.length;
        },
        items: function() {
            return this.apiResult ? this.apiResult.data : [];
        },
        padItems: function() {
            if(this.apiResult) {
                let per = this.apiResult.meta.per_page;
                let p = per - this.items.length;
                console.log(p);
                return p;
            }
            return 0;
        },
        pageMeta: function() {
            return this.apiResult ? this.apiResult.meta : null;
        }
    },
    methods: {
        startPollProcessing () {
            this.pollProcessingHandle = setInterval(async () => {
                this.getData();
            }, 2000);
        },
        updatePage( pageWrapper ) {
            this.currentPollUrl = pageWrapper.url;
            this.getData();
        },
        async getData() {
            this.apiResult = (await axios.get(this.currentPollUrl)).data;
        }
    },
    created() {
        this.startPollProcessing();
    },

    beforeDestroy(){
        clearInterval(this.pollProcessingHandle);
    },

    async mounted() {
        this.getData();
    },
}
</script>
