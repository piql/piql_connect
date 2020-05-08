<template>
    <div class="w-100">

        <page-heading icon="fa-hourglass-half" :title="$t('ingest.processing.title')" :ingress="$t('ingest.processing.ingress')" />

        <div class="row plistHeader">
            <div class="col-sm-5">{{$t('ingest.processing.sip')}}</div>
            <div class="col-sm-3 text-center">{{$t('ingest.processing.ingestDate')}}</div>
            <div class="col-sm-3 text-center">{{$t('ingest.processing.status')}}</div>
            <div class="col-sm-1">&nbsp;</div>
        </div>

        <div v-if="currentlyIdle" class="mt-5 text-center"><h2 class="pt-5">{{$t('ingest.processing.noItems')}}</h2></div>
        <FileInProcess v-for="item in items" v-bind:item="item" v-bind:key="item.id"/>
        <div v-for="x in padItems"><div class="row plist invisible"><div class="col">&nbsp;</div></div></div>
        <div v-if="showPager" class="row">
            <div class="col">
                <Pager :meta='pageMeta' @updatePage='updatePage' />
            </div>
        </div>

    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        data() {
            return {
                result: null,
                pageQuery: null,
                pollProcessingHandle: null,
            }
        },
        props: {
            baseUrl: {
                type: String,
                default: "/api/v1/ingest/processing"
            },
            pollInterval: {
                type: Number,
                default: 2000
            }
        },
        computed: {
            url() { return this.pageQuery ? this.baseUrl + "?" + this.pageQuery : this.baseUrl; },
            success() { return this.result ? ( this.result.status === 200 ) : false; },
            items() { return this.success ? this.result.data.data : null; },
            pageMeta() { return this.success ? this.result.data.meta : null; },
            showPager() { return this.success && this.pageMeta.total > 1; },
            padItems() { return this.success ? this.pageMeta.per_page - this.items.length : 0; },
            currentlyIdle() { return this.result && (  !this.success || !this.items.length ); },
        },
        methods: {
            startPollProcessing () {
                this.pollProcessingHandle = setInterval( async () => {
                    this.getData();
                }, this.pollInterval );
            },
            updatePage( pageWrapper ) {
                this.pageQuery = pageWrapper.query;
                this.getData();
            },
            async getData() {
                this.result = await axios.get( this.url );
            }
        },
        created() {
            this.startPollProcessing();
        },

        beforeDestroy(){
            clearInterval(this.pollProcessingHandle);
        },

        async mounted() {
            this.currentPollUrl = this.baseUrl;
            this.getData();
        },
    }
</script>
