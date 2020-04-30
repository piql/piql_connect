<template>
    <div class="w-100">
        <page-heading icon="fa-list-ul" :title="$t('ingest.taskList.title')" :ingress="$t('ingest.taskList.ingress')" />

        <form>
            <div class="row listFilter">
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
            <div class="row plistHeader mt-5">
                <div class="col-1"><input type="checkbox" class="checkbox" id="allSips"></div>
                <div class="col-2">{{$t('ingest.taskList.sip')}}</div>
                <div class="col-3">{{$t('ingest.taskList.content')}}</div>
                <div class="col">{{$t('ingest.taskList.ingestDate')}}</div>
                <div class="col listActionItems">&nbsp;</div>
                <div class="col piqlIt">&nbsp;</div>
            </div>

            <Task v-for="item in jobs" v-bind:item="item" v-bind:key="item.id" @piqlIt="piqlIt"/>
        </form>

        <div class="row">
            <div class="col">
                <Pager :meta="meta" @updatePage="updatePage" />
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        data() {
            return {
                pageQuery: "",
                result: null,
            }
        },
        props: {
            baseUrl: {
                type: String,
                default: "/api/v1/ingest/offline_storage/pending/jobs"
            }
        },
        computed: {
            url() { return this.baseUrl },
            success() { return this.result ? ( this.result.status == 200 ) : false; },
            jobs() { return this.success ? this.result.data.data : null; },
            meta() { return this.success ? this.result.data.meta : null; }
        },
        async mounted() {
            this.update();
        },
        methods: {
            async piqlIt( id ) {
                await axios.post("/api/v1/ingest/bags/"+id+"/piql");
                this.update();
            },
            async update() {
                this.items = await axios.get( this.url );
            },
            updatePage( page ) {
                this.pageQuery = page.Query;
                this.update();
            }
        },
    }
</script>
