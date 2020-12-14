<template>
    <div class="w-100">
        <page-heading icon="fa-hdd" :title="$t('ingest.offlineStorage.package.header')" :ingress="$t('ingest.offlineStorage.package.ingress')" />
        <breadcumb :subTitle="$t('ingest.taskList.title')" :subTitleRoute="{ name: 'ingest.offline' }"/>
        <bucket-content-list @openObject="openObject" @onDelete="onDelete" :location="selectedLocation" :dataObjects="currentObjects"
            :selectedCollection="selectedArchiveUuid" :selectedHolding="selectedHolding"/>
        <div class="row text-center pagerRow">
            <div class="col">
                <Pager :meta='packagePageMeta' :height='height' />
            </div>
        </div>
    </div>
</template>

<script>

import RouterTools from '@mixins/RouterTools.js';

import axios from 'axios';
import JQuery from 'jquery';
import BucketContentList from "@components/BucketContentList";
let $ = JQuery;

export default {
    components: {BucketContentList},
    mixins: [ RouterTools ],

    data() {
        return {
            dataObjects: [],
            packagePageMeta: null,
            selectedLocation: null,
            selectedHolding: null,
            selectedArchiveUuid: null
        }
    },
    props: {
        height: {
            type: Number,
            default: 0
        }
    },
    computed: {
        apiQueryString: function() {
            let query = this.$route.query;

            let filter = "?location=Online"

            if( query.collection ) {
                filter += "&collection=" + encodeURI( query.collection );
            }
            if( query.holding ){
                    filter += "&holding=" + encodeURI( query.holding );
            }
            if( query.archived_from ){
                filter += "&archived_from=" + query.archived_from;
            }
            if( query.archived_to ){
                filter += "&archived_to=" + query.archived_to;
            }
            if(query.search){
                filter += "&search=" + query.search;
            }
            if( parseInt( query.page ) ) {
                filter += "&page=" + query.page;
            }
            return filter;
        },
        currentObjects: function() {
            return this.dataObjects;
        },
        bucketId: function() {
            return this.$route.params.bucketId;
        },
    },
    watch: {
        '$route': 'dispatchRouting'
    },
    async mounted() {
        let page = this.$route.query.page;
        if( isNaN( page ) || parseInt( page ) < 2 ) {
            this.updateQueryParams({ page: null })
        }
        this.refreshObjects( this.apiQueryString );

    },
    methods: {
        /**
         * dispatchRouting is called whenever the route changes
         *
         * Use it to update API queries with pagination, filters etc.
         */
        dispatchRouting() {
            this.refreshObjects( this.apiQueryString );
        },
        refreshObjects( apiQueryString ){
            axios.get("/api/v1/ingest/storage/offline/pending/buckets/"+this.bucketId+"/dips"+apiQueryString).then( (aips ) => {
                this.dataObjects = aips.data.data;
                this.packagePageMeta = aips.data.meta;
                if(this.packagePageMeta.last_page < this.$route.query.page) {
                    this.replaceQueryParams({page: this.packagePageMeta.last_page})
                }
            });
        },
        openObject: function( dipId ) {
            this.$router.push({ name: 'ingest.offline.buckets.dips.files', params: { bucketId: this.bucketId, dipId }, query: {} });
        },
        onDelete: function( dipId ) {
            this.refreshObjects( this.apiQueryString );
        },
    },
}
</script>
