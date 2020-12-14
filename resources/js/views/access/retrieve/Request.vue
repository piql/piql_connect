<template>
    <div class="w-100">
        <page-heading iconImg="/images/retrieval-icon-gray.svg" :title="$t('access.retrieve.request')" :ingress="$t('access.retrieve.request.ingress')" />

        <access-browser-filters :singleCollectionTitle="$t('Your collection')"></access-browser-filters>

        <div class="row plistHeader text-truncate mt-2">
            <div class="col-sm-2 text-center">{{$t("access.browse.header.preview")}}</div>
            <div class="col-sm-3 text-left">{{$t("access.browse.header.name")}}</div>
            <div class="col-sm-1 text-left">{{$t("access.browse.header.size")}}</div>
            <div class="col-sm-2 text-center">{{$t("access.browse.header.ingestDate")}}</div>
            <div class="col-sm-1 text-center">{{$t("access.browse.header.bucket")}}</div>
            <div class="col-sm-1 text-center">{{$t("access.browse.header.files")}}</div>
            <div class="col-sm-2 text-center">{{$t("access.browse.header.actions")}}</div>
        </div>


        <browser-item-offline v-for="item in currentObjects" :key="item.id" :item="item" @addObjectToRetrieval="addObjectToRetrieval" />

        <div class="row text-center pagerRow">
            <div class="col">
                <Pager :meta='packagePageMeta' :height='height' />
            </div>
        </div>
    </div>
</template>

<script>

import RouterTools from '@mixins/RouterTools.js';

export default {

    mixins: [ RouterTools ],

    data() {
        return {
            retrievalItems: [],
            dataObjects: [],
            packagePageMeta: null,
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

            let collectionFilter = query.collection ? `collection=${encodeURI( query.collection )}` : "";
            let holdingFilter = query.holding ? `holding=${encodeURI( query.holding )}` : "";
            let archivedFromFilter = query.archived_from ? `archived_from=${encodeURI( query.archived_from )}` : "";
            let archivedToFilter = query.archived_to ? `archived_to=${encodeURI( query.archived_to )}` : "";
            let querySearchFilter = query.search ? `search=${encodeURI( query.search )}` : "";
            let pageFilter = parseInt( query.page ) ? `page=${encodeURI( query.page )}` : "";
            let filters = [collectionFilter, holdingFilter, archivedFromFilter, archivedToFilter, querySearchFilter, pageFilter ];

            return "?".concat( filters.filter( (f) => f ).join( "&" ) );
        },
        currentObjects: function() {
            return this.dataObjects;
        },
        numberOfFilesForRetrieval() {
            return this.retrievalItems.length;
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
            axios.get("/api/v1/storage/requests/aips"+apiQueryString).then( (buckets) => {
                this.dataObjects = buckets.data.data;
                this.packagePageMeta = buckets.data.meta;
            });
        },
        addObjectToRetrieval: function(item) {
            let infoToast = this.infoToast;
            let title = this.$t('access.retrieve.toasts.added.title');
            let message = this.$t('access.retrieve.toasts.added.message');
            axios.post('/api/v1/storage/retrievals/add', {
                'aipUuid' : item.aip_external_uuid
            }).then( function ($result) {
                infoToast( title, message, {'ITEMTITLE': item.title } );
            });
        },
    },
}
</script>
