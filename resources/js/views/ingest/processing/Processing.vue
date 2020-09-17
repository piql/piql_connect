<template>
    <div class="w-100">

        <page-heading icon="fa-hourglass-half" :title="$t('ingest.processing.title')" :ingress="$t('ingest.processing.ingress')" />

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <files-in-process :currentlyIdle="currentlyIdle" :items="items"></files-in-process>

                        <div v-if="showPager" class="row">
                            <div class="col text-center pagerRow">
                                <Pager :meta='pageMeta' @updatePage='updatePage' />
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- <div class="row plistHeader">
            <div class="col-sm-5">{{$t('ingest.processing.sip')}}</div>
            <div class="col-sm-3 text-center">{{$t('ingest.processing.ingestDate')}}</div>
            <div class="col-sm-3 text-center">{{$t('ingest.processing.status')}}</div>
            <div class="col-sm-1">&nbsp;</div>
        </div> -->

        <!-- <div v-if="currentlyIdle" class="mt-5 text-center"><h2 class="pt-5">{{$t('ingest.processing.noItems')}}</h2></div>
        <FileInProcess v-for="item in items" v-bind:item="item" v-bind:key="item.id"/> -->

        <!-- <div v-for="x in padItems"><div class="row plist invisible"><div class="col">&nbsp;</div></div></div> -->
        

    </div>
</template>

<script>
    import axios from 'axios';
    import RouterTools from '@mixins/RouterTools.js';

export default {
        mixins: [ RouterTools ],
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
            url() { return this.pageQuery ? this.baseUrl + "?page=" + this.pageQuery : this.baseUrl; },
            success() { return this.result ? ( this.result.status === 200 ) : false; },
            items() { 
                if(this.success){
                    let items = this.result.data.data;
                    items.forEach(item => {
                        item.progressPercentage = this.progressPercentage(item.status);
                        item.progressBarStyle = this.progressBarStyle(item.progressPercentage);
                        item.translatedStatus = this.translatedStatus(item.status);

                    });

                    return items;

                }else{
                    return null;
                }
                
                },
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
            async getData() {
                this.result = await axios.get( this.url );
            },
            dispatchRouting() {
                let query = this.$route.query;
                this.pageQuery = query.page ?? "";
                this.getData();
            },
            progressPercentage(status) {
                switch( status ) {
                    case 'closed':
                        return 2;
                    case 'bag_files':
                        return 20;
                    case 'move_to_outbox':
                        return 30;
                    case 'initiate_transfer':
                        return 40;
                    case 'approve_transfer':
                        return 50;
                    case 'transferring':
                        return 60;
                    case 'ingesting':
                        return 80;
                    case 'complete':
                        return 100;
                    default:
                        0;
                }
            },
            progressBarStyle(percent) {
                return  `width: ${percent}%;`;
            },
            translatedStatus(status) {
                let statusText = `ingest.processing.status.${status}`;
                return this.$t(statusText);
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
        watch: {
            '$route': 'dispatchRouting'
        },
    }
</script>
