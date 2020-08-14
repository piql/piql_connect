<template>
    <div class="w-100">
        <page-heading icon="fa-hdd" :title="$t('access.browse')" :ingress="$t('access.browse.ingress')" />

        

        <access-browser-filters :singleArchiveTitle="$t('Your archive')"></access-browser-filters>
        <browser-list @openObject="openObject" :location="selectedLocation" :dataObjects="currentObjects"
            :selectedArchive="selectedArchiveUuid" :selectedHolding="selectedHolding"/>

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

            let filter = "?location=Online"

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
            axios.get("/api/v1/access/dips"+apiQueryString, { params: { limit: 8 } }).then( (dips ) => {
                this.dataObjects = dips.data.data;
                this.packagePageMeta = dips.data.meta;
            });
        },
        openObject: function( dipId ) {
            this.$router.push({ name: 'access.browse.dip', params: { dipId }, query: {} });
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
    },
}
</script>
