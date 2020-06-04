<template>
    <div class="w-100">
        <page-heading icon="fa-list-ul" :title="$t('ingest.taskList.title')" :ingress="$t('ingest.taskList.ingress')" />

        <div class="row plistHeader mt-5">
            <div class="col-sm-3">{{$t('ingest.offlineStorage.jobName')}}</div>
            <div class="col-sm-1 text-right">{{$t('ingest.offlineStorage.numberOfAips')}}</div>
            <div class="col-sm-2 text-right">{{$t('ingest.offlineStorage.size')}}</div>
            <div class="col-sm">{{$t('ingest.offlineStorage.filled')}}</div>
            <div class="col-4 text-center align-self-center">{{$t('ingest.offlineStorage.actions')}}</div>
        </div>

        <Task v-for="item in items" v-bind:item="item" v-bind:key="item.id"
                           :jobListUrl="jobListUrl" :actionIcons="actionIcons" @piqlIt="piqlIt" @onDelete="update"/>

        <div class="row">
            <div class="col">
                <Pager :meta="pageMeta" @updatePage="updatePage" />
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        data() {
            return {
                pageQuery: null,
                result: null
            }
        },
        props: {
            actionIcons: {
                type: Object,
                default: function () { return { 'metadata': true, 'list': true, 'config': true, 'delete': true, 'defaultAction': true}; }
            },
            baseUrl: {
                type: String,
                default: "/api/v1/ingest/offline_storage/pending/jobs"
            },
            jobListUrl: {
                type: String,
                default: "/api/v1/ingest/offline_storage/pending"
            }
        },
        computed: {
            url() { return this.pageQuery ? this.baseUrl + "?" + this.pageQuery : this.baseUrl; },
            success() { return this.result ? ( this.result.status === 200 ) : false; },
            items() { return this.success ? this.result.data.data : null; },
            pageMeta() { return this.success ? this.result.data.meta : null; },
            showPager() { return this.success && this.pageMeta.total > 1; }
        },
        async mounted() {
            this.update();
        },
        methods: {
            async piqlIt( job ) {
                let result = (await axios.patch(this.jobListUrl+"/jobs/"+job.id, {
                    'status': 'ingesting'
                }));
                if(result.data.status == 'ingesting') {
                    this.modal = false; //????
                }
                this.infoToast(
                    this.$t('ingest.offlineStorage.toasts.piqled.title'),
                    this.$t('ingest.offlineStorage.toasts.piqled.message'),
                    {'PACKAGENAME': job.name }
                );
                this.update();
            },
            async update() {
                this.result = await axios.get( this.url );
            },
            updatePage( page ) {
                this.pageQuery = page.query;
                this.update();
            }
        },
    }
</script>
