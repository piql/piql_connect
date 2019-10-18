<template>
    <div class="container-fluid">
        <ingest-filter-search class="mr-5"></ingest-filter-search>
        <div class="row plistHeader">
            <div class="col">{{$t('ingest.fileList.fileName')}}</div>
            <div class="col-sm-2  d-flex flex-row justify-content-end">{{$t('ingest.fileList.fileSize')}}</div>
            <div class="col-2 listActionItems"></div>
        </div>

        <file v-for="listitem in items" v-bind:item="listitem" v-bind:key="listitem.id"/>
        <div v-for="x in padItems"><div class="row plist invisible" style="min-height: 4.8rem"><div class="col">&nbsp;</div></div></div>
        <div v-if="showPager" class="row">
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
                result: null,
                pageQuery: null
            }
        },
        computed: {
            url() { return this.pageQuery ? this.itemUrl + "?" + this.pageQuery : this.itemUrl; },
            success() { return this.result ? ( this.result.status === 200 ) : false; },
            items() { return this.success ? this.result.data.data : null; },
            pageMeta() { return this.success ? this.result.data.meta : null; },
            showPager() { return this.success && this.pageMeta.total > 1; },
            padItems() { return this.success ? this.pageMeta.per_page - this.items.length : 0; },
            itemUrl() { return this.baseUrl + this.bagId + "/files"; }
        },

        props: {
            bagId: {
                type: String,
                default: ""
            },
            baseUrl: {
                type: String,
                default: "/api/v1/ingest/bags/"
            },

        },

        async mounted() {
            this.getData();
        },
        methods: {
            async update() {
                this.result = await axios.get( this.url );
            },
            updatePage( page ) {
                this.pageQuery = page.query;
                this.update();
            },
            async getData() {
                this.result = await axios.get( this.url );
            }
        },

    }
</script>
