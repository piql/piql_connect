<template>
    <div>
        <div class="row mb-5" style="cursor: help" :title="$t('ingest.offlineStorage.ingress')" >
            <div class="col-sm-1 text-left">
              <i class="fas fa-clock titleIcon"></i>
            </div>
            <div class="col-sm-11 text-left">
                <h1 class="ml-0 mt-2" >{{ $t('ingest.offlineStorage.header') }}</h1>
                <div class="ingressText">{{$t('ingest.offlineStorage.ingress')}}</div>
            </div>
        </div>


        <div class="row plistHeader text-center">
            <div class="col-sm-3">{{$t('ingest.offlineStorage.jobName')}}</div>
            <div class="col-sm-1">{{$t('ingest.offlineStorage.numberOfAips')}}</div>
            <div class="col-sm-2">{{$t('ingest.offlineStorage.size')}}</div>
            <div class="col-sm-2">{{$t('ingest.offlineStorage.filled')}}</div>
            <div class="col fg-black">{{$t('ingest.offlineStorage.actions')}}</div>
        </div>

        <job-list-item v-for="item in items" v-bind:item="item" v-bind:key="item.id"
            :jobListUrl="jobListUrl" :actionIcons="actionIcons" @piqlIt="piqlIt"/>
        <div v-if="showPager" class="row pt-2">
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
                default: function () { return { 'list': true, 'config': true, 'delete': true, 'defaultAction': true}; }
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
            async piqlIt(id) {
                await axios.post("/api/v1/ingest/bags/"+id+"/piql");
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
