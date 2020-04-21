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

        <form class="form mb-1 ml-0 mr-0" v-on:submit.prevent>
            <div class="row mt-1 mb-0 ">
                <div v-if="useArchives" class="col-md-2 col-lg-2 pl-0 pr-0 ">
                    <archive-picker :useWildCard=true :archives='archives' :initialSelection='selectedArchiveUuid' :label='$t("Archive")' @selectionChanged='archiveSelectionChanged'></archive-picker>
                </div>
                <div v-else="useArchives" class="col-md-2 col-lg-2 pl-0 pr-0">
                    <label class="col-form-label-sm">{{$t('Archive')}}</label>
                    <div class="pl-0 pr-0 form-control align-middle text-center">{{singleArchiveTitle}}</div>
                </div>

                <div v-if="useHoldings" class="col-md-2 col-lg-2">
                    <holding-picker :useWildCard=true :selectionDisabled='holdingSelectionDisabled' :holdings='holdings' :initialSelection='selectedHolding' :label='$t("Holding")' @selectionChanged='holdingSelectionChanged' />
                </div>
                <div v-else="useHoldings" class="col-md-2 col-lg-2">
                    <holding-picker :useWildCard=true :selectionDisabled='true' :holdings='holdings' :initialSelection='0' :label='$t("Holding")' @selectionChanged='holdingSelectionChanged' />
                </div>


                <div class="col-md-2 col-lg-2">
                    <label for="fromDate" class="col-form-label-sm">{{$t('access.browse.archivedFrom')}}</label>
                    <input v-model="fromDateFilter" id="fromDate" type="date" class="form-control w-auto">
                </div>

                <div class="col-md-2 col-lg-2">
                    <label for="toDate" class="col-form-label-sm">{{$t('access.browse.archivedTo')}}</label>
                    <input v-model="toDateFilter" id="toDate" type="date" class="form-control w-auto">
                </div>

                <div class="col-md-2 col-lg-2">
                    <div class="form-group">
                        <label for="searchContents" class="col-form-label-sm">{{$t('access.browse.withContents')}}</label>
                        <div class="input-group">
                            <div class="input-group addon">
                                <input v-model="searchField" id="searchContents" type="text" class="form-control" style="border-radius: 3px">
                                <span class="input-group-addon">
                                  <i class="fas fa-search search-icon-inline mt-2 mr-2"></i>
                              </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-2 pr-0 text-align-right">
                    <location-picker :initialSelectedLocation="selectedLocation" :locations="locations" @locationSelectionChanged="locationSelectionChanged"></location-picker>
                </div>
            </div>
        </form>

        <span v-if="fileMode === false">
            <browser-list v-if="holdingSelected" @openObject="openObject" :location="selectedLocation" :dataObjects="currentObjects"
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

    import axios from 'axios';
    import JQuery from 'jquery';
    let $ = JQuery;

    export default {
        data() {
            return {
                holdingSelectCounter: 0,
                lastSelectedHolding: "",
                selectedHolding: "",
                fromDateFilter: "",
                toDateFilter: "",
                searchField: "",
                selectedLocation: "online",
                locations: [
                    { name: 'Online', value: 'online' },
                    { name: 'Offline', value: 'offline'}
                ],
                archives: [],
                selectedArchiveUuid: "",
                singleArchiveTitle: "",
                holdings: [],
                selectedArchiveHoldings: [],
                retrievalItems: [],
                dataObjects: [],
                currentOpenObjectFiles: [],
                currentOpenDipId: null,
                fileMode: false,
                packagePageMeta: null,
                filesPageMeta: null,
                currentFilesPage: 1,
                useArchives: true,
            }
        },
        computed: {
            useHoldings: function() {
              return this.holdings.length > 0;
            },
            archiveSelected: function() {
                return this.selectedArchiveUuid.length > 0;
            },
            holdingSelected: function() {
              return true; //TODO: Review, is this obsolete?
            },
            online: function() {
                return this.selectedLocation == "online";
            },
            offline: function() {
                return this.selectedLocation == "offline";
            },
            apiQueryString: function() {
                let filter = "?location=" + encodeURI(this.selectedLocation);
                if(this.archiveSelected && this.archiveSelected !== "All") {
                    filter += "&archive=" + encodeURI(this.selectedArchiveUuid);
                }
                if(this.selectedHolding){
                    if(this.selectedHolding !== "All"){
                        filter += "&holding=" + encodeURI(this.selectedHolding);
                    }
                }
                if(this.fromDateFilter){
                    filter += "&from=" + encodeURI(this.fromDateFilter);
                }
                if(this.toDateFilter){
                    filter += "&to=" + encodeURI(this.toDateFilter);
                }
                if(this.searchField){
                    filter += "&search=" + encodeURI(this.searchField);
                }
                if( parseInt( this.$route.query.page ) ) {
                    filter += "&page=" + this.$route.query.page;
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
            axios.get("/api/v1/planning/archives").then( (response) => {
                this.archives = response.data.data;
                if( this.archives.length === 1 ) {
                    this.selectedArchiveUuid = this.archives[0].uuid;
                    this.singleArchiveTitle = this.archives[0].title;
                    this.useArchives = false;
                }
                else {
                    this.useArchives = true;
                }
            });

            let initialQuery = "";
            let page = this.$route.query.page;
            if( isNaN( page ) || parseInt( page ) < 1 ) {
                const query = Object.assign( {}, this.$route.query );
                query.page = 1;
                this.$router.replace( { query } );
            }

            initialQuery += "?page="+page;
            this.refreshObjects(initialQuery);

        },
        methods: {
            /**
             * dispatchRouting is called whenever the route changes
             *
             * Use it to update pagination, filters etc.
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
                this.$router.push({ name: 'access.browse', params: { 'page' : pageWrapper.page } });
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

            holdingSelectionChanged: function(holding) {
                this.fileMode = false;
                this.selectedHolding = holding;
            },
            archiveSelectionChanged: function(archiveUuid) {
              this.fileMode = false;
              this.selectedArchiveUuid = archiveUuid;

              if(archiveUuid === "") {
                  this.holdingSelectionDisabled = true;
              } else {
                  axios.get("/api/v1/planning/archives/"+archiveUuid+"/holdings").then( (response) => {
                      this.holdings = response.data.data;
                      this.holdingSelectionDisabled = false;
                  });
              }
            },
            locationSelectionChanged: function(loc) {
                this.fileMode = false;
                this.selectedLocation = loc;
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
