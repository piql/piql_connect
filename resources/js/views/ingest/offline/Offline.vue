<template>
    <div class="w-100">
        <page-heading icon="fa-clock" :title="$t('ingest.taskList.title')" :ingress="$t('ingest.taskList.ingress')" />
        <div class="card">
            <div class="card-header">
                <b><i class="fa fa-clock"></i> {{$t('ingest.taskList.title')}}</b>
            </div>
            <div class="card-body">
                <task-table :items="items" :jobListUrl="jobListUrl" :actionIcons="actionIcons" @piqlIt="piqlIt" @onDelete="update" />
            </div>
        </div>


        <div class="row">
            <div class="col">
                <Pager :meta="pageMeta" @updatePage="updatePage" />
            </div>
        </div>
    </div>
</template>

<script>

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
                default: "/api/v1/ingest/storage/offline/pending/buckets"
            },
            jobListUrl: {
                type: String,
                default: "/api/v1/ingest/storage/offline/pending"
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
                let result = (await axios.patch(this.jobListUrl+"/buckets/"+job.id, {
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
