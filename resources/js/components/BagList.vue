<template>
    <div>
        <form>
            <div class="row listFilter mb-5">
                <label for="fromDate">{{$t('ingest.taskList.from')}}</label>
                <input type="date" name="fromDate" placeholder="DDMMYY">
                <label for="toDate">{{$t('ingest.taskList.to')}}</label>
                <input type="date" name="toDate" placeholder="DDMMYY">
                <select name="status">
                    <option style="display: none;" disabled="" selected="">{{$t('ingest.taskList.statusFilter')}}</option>
                    <option>{{$t('ingest.taskList.uploadingFilter')}}</option>
                    <option>{{$t('ingest.taskList.processingFilter')}}</option>
                </select>
                <input type="search" :placeholder="$t('Search')">
            </div>

            <div class="row plistHeader">
                <div class="col-1"><input type="checkbox" class="checkbox" id="allSips"></div>
                <div class="col">{{$t('ingest.taskList.aip')}}</div>
                <div class="col-3">{{$t('ingest.taskList.ingestDate')}}</div>
                <div class="col-2">{{$t('ingest.taskList.size')}}</div>
                <div class="col-2 listActionItems">&nbsp;</div>
            </div>

            <bag-list-item v-for="item in items" v-bind:item="item" v-bind:key="item.id" @piqlIt="piqlIt"/>

            <div :key="x" v-for="x in padItems"><div class="row plist invisible" style="min-height: 4.8rem"><div class="col">&nbsp;</div></div></div>
            <div v-if="showPager" class="row">
                <div class="col">
                    <Pager :meta='pageMeta' @updatePage='updatePage' />
                </div>
            </div>

        </form>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        data() {
            return {
                result: null,
                pageQuery: null
            }
        },
        props: {
            baseUrl: {
                type: String,
                default: "/api/v1/ingest/storage/offline/pending/buckets/"
            },
            jobId: {
                type: String,
                default: ""
            },
        },
        computed: {
            url() { return this.pageQuery ? this.itemUrl + "?" + this.pageQuery : this.itemUrl; },
            success() { return this.result ? ( this.result.status === 200 ) : false; },
            items() { return this.success ? this.result.data.data : null; },
            pageMeta() { return this.success ? this.result.data.meta : null; },
            showPager() { return this.success && this.pageMeta.total > 1; },
            padItems() { return this.success ? this.pageMeta.per_page - this.items.length : 0; },
            itemUrl() { return this.baseUrl + this.jobId + "/bags"; }
        },
        async mounted() {
            this.getData();
        },
        methods: {
            async piqlIt(id) {
                await axios.post("/api/v1/ingest/bags/"+id+"/piql");
                this.getData();
            },
            updatePage( pageWrapper ) {
                this.pageQuery = pageWrapper.query;
                this.getData();
            },
            async getData() {
                this.result = await axios.get( this.url );
            }
        },
    }
</script>
