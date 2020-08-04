<template>
    <div class="w-100">
        <page-heading icon="fa-hdd" :title="$t('access.retrieve.request')" :ingress="$t('access.retrieve.request.ingress')" />

        <access-browser-filters :singleArchiveTitle="$t('Your archive')"></access-browser-filters>

         <div class="row plistHeader text-truncate mt-2">
            <div class="col-sm-2 text-center">{{$t("access.browse.header.preview")}}</div>
            <div class="col-sm-2 text-left">{{$t("access.browse.header.name")}}</div>
            <div class="col-sm-1 text-left">{{$t("access.browse.header.size")}}</div>
            <div class="col-sm-1 text-center">{{$t("access.browse.header.ingestDate")}}</div>
            <div class="col-sm-1 text-center">{{$t("access.browse.header.holding")}}</div>
            <div class="col-sm-1 text-center">{{$t("access.browse.header.files")}}</div>
            <div class="col-sm-3 text-center">{{$t("access.browse.header.actions")}}</div>
        </div>

        <browser-item-offline v-for="item in dataObjects" :key="item.id" 
            @addFileToRetrieval="addFileToRetrieval"
            @addObjectToRetrieval="addObjectToRetrieval"
            @openObject="openObject"
            :archive="selectedArchiveUuid"
            :holding="selectedHolding"
            :item="item"
        />

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
let $ = JQuery;

export default {

    mixins: [ RouterTools ],

    data() {
        return {
            retrievalItems: [],
            dataObjects: [],
            currentOpenObjectFiles: [],
            currentOpenDipId: null,
            packagePageMeta: null,
            filesPageMeta: null,
            currentFilesPage: 1,
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
        fileMode: function() {

        },
        online: function() {
            return this.selectedLocation == "online";
        },
        offline: function() {
            return this.selectedLocation == "offline";
        },
        apiQueryString: function() {
            let query = this.$route.query;
            let filter = "";

            if( query.archive ) {
                filter += "&archive=" + encodeURI( query.archive );
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
            axios.get("/api/v1/storage/offline/archive/buckets").then( (dips ) => {
                this.dataObjects = dips.data.data;
                this.packagePageMeta = dips.data.meta;
            });
        },
        openObject: function( file ) {
            let fileId = file.id;
            let bucketId = file.bucket.id;
            this.$router.push({ name: 'access.retrieve.bucket.aip.files', params: { fileId }, query: {} });
        },
        refreshFileObjects(filesQueryString){
            //TODO: Use routing
            let dipId = this.currentOpenDipId;
            axios.get("/api/v1/access/dips/"+dipId+"/files"+filesQueryString)
                .then( async (dipFilesResponse ) => {
                    this.currentOpenObjectFiles = dipFilesResponse.data.data;
                    this.filesPageMeta = dipFilesResponse.data.meta;
                });
        },

        closeFileList: function() {
            this.currentFilesPage = 1;
            this.currentOpenDipId = null;
        },

        addObjectToRetrieval: async function(item) {
            this.infoToast('Retrieval', `Package ${item.storage_properties.bag.name} added to retrieval basket`);
            axios.post('/api/v1/storage/retrievals/add', {
                'aipUuid' : item.storage_properties.aip_uuid
            });
        },
        addFileToRetrieval: async function( fileObject ) {
            /* For now, this is a fileObject resource entity. We really need an abstraction on top,
                    to stop leaking internal data. We could for example push a unique id linked to the
                    film reel and a filename for visualization and that should be enough.
             */

            this.infoToast('Retrieval', `Package ${fileObject.filename} added to retrieval basket`);

            this.retrievalItems.push(fileObject);
            await (axios.post('/api/v1/storage/retrievals/add', {
                'fileObjectId' : fileObject.id,
            }));
        },
    },
}
</script>
