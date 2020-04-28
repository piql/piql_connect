<template>
    <div class="mt-2 mb-2">
        <div class="row">
            <div class="col-sm-1 text-left">
                <i class="fas fa-hdd mr-3 titleIcon"></i>
            </div>
            <div class="col-sm-6 text-left">
                <h1>{{$t('access.browse')}}</h1>
            </div>
        </div>
        <div class="row mt-0 pt-0">
            <div class="col-sm-1"></div>
            <div class="col-sm-6 text-left ingressText">
                {{$t('access.browse.ingress')}}
            </div>
        </div>

        <access-browser-filters :singleArchiveTitle="$t('Your archive')"></access-browser-filters>

        <span v-if="fileMode === false">
            <browser-list @openObject="openObject" :location="selectedLocation" :dataObjects="currentObjects"
                @addObjectToRetrieval="addObjectToRetrieval" :selectedArchive="selectedArchiveUuid" :selectedHolding="selectedHolding"/>
            <div class="row text-center pagerRow">
                <div class="col">
                    <Pager :meta='packagePageMeta' />
                </div>
            </div>
        </span>
        <span v-if="fileMode">
            <browser-file-list :dataObjects="currentOpenObjectFiles" :location="selectedLocation" :dipId="currentOpenDipId"
                @close="closeFileList" @addFileToRetrieval="addFileToRetrieval" />
            <div class="row text-center pagerRow">
                <div class="col">
                    <Pager :meta='filesPageMeta' @updatePage='filesUpdatePage' />
                </div>
            </div>
        </span>

    </div>
</template>

<script>

import RouterTools from '../../mixins/RouterTools.js';

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
            fileMode: false,
            packagePageMeta: null,
            filesPageMeta: null,
            currentFilesPage: 1,
            selectedLocation: null,
            selectedHolding: null,
            selectedArchiveUuid: null
        }
    },
    computed: {
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
            axios.get("/api/v1/access/dips"+apiQueryString).then( (dips ) => {
                this.dataObjects = dips.data.data;
                this.packagePageMeta = dips.data.meta;
            });
        },
        filesUpdatePage( ) {
            //TODO: Use routing
            //            this.$router.push({ name: 'access.browse', params: { 'page' : pageWrapper.page } });
        },
        openObject: async function( dipId ) {
            //TODO: Use routing
            axios.get("/api/v1/access/dips/"+dipId+"/files").then( async ( dipFilesResponse ) =>  {
                this.currentOpenObjectFiles = dipFilesResponse.data.data;
                this.filesPageMeta = dipFilesResponse.data.meta;
                this.currentPage = 1;
                this.currentOpenDipId = dipId;
                this.fileMode = true;
            });
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
            this.fileMode = false;
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
