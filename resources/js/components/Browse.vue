<template>
  <div class="mt-2 mb-2">
      <div class="row">
          <div class="col-sm-1 text-right">
              <i class="fas fa-hdd mr-3 titleIcon"></i>
          </div>
          <div class="col-sm-6 text-left">
              <h1> Browse</h1>
          </div>
      </div>
      <div class="row mt-0 pt-0">
            <div class="col-sm-1"></div>
            <div class="col-sm-6 text-left" style="font-size: 0.75rem">
                Access your archives
            </div>
      </div>

        <form class="form mb-1 ml-0 mr-0" v-on:submit.prevent>
            <div class="row mt-1 mb-0 ">
                <div class="col-md-2 col-lg-2 pl-0 pr-0 ">
                    <archive-picker :archives='archives' :initialSelection='selectedArchiveUuid' :label='archiveSelectLabel' @selectionChanged='archiveSelectionChanged'></archive-picker>
                </div>

                <div class="col-md-2 col-lg-2">
                    <holding-picker :holdings='holdings' :initialSelection='selectedHolding' :label='holdingSelectLabel' @selectionChanged='holdingSelectionChanged' />
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
                    <location-picker :holding='selectedArchiveUuid' :initialSelectedLocation="selectedLocation" :locations="locations" @locationSelectionChanged="locationSelectionChanged"></location-picker>
                </div>
            </div>
        </form>

        <span v-if="fileMode === false">
            <browser-list v-if="holdingSelected" @openObject="openObject" :location="selectedLocation" :dataObjects="currentObjects"
                @addObjectToRetrieval="addObjectToRetrieval" :selectedArchive="selectedArchiveUuid" :selectedHolding="selectedHolding"/>
            <div class="row thumbnailList invisible" v-for="pad in padPackageItems"></div>
            <div class="row text-center pagerRow">
                <div class="col">
                    <Pager :meta='packagePageMeta' @updatePage='packageUpdatePage' />
                </div>
            </div>
    </span>
    <span v-if="fileMode">
        <browser-file-list :dataObjects="currentOpenObjectFiles" :location="selectedLocation" :dipId="currentOpenDipId"
            @close="closeFileList" @addFileToRetrieval="addFileToRetrieval" />
        <div class="row plist thumbnailList invisible" v-for="pad in padFileItems"></div>
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
    import selectpicker from 'bootstrap-select';

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
                archiveSelectLabel: "Archive",
                holdingSelectLabel: "Holding",
                holdings: [],
                selectedArchiveHoldings: [],
                retrievalItems: [],
                dataObjects: [],
                currentOpenObjectFiles: [],
                currentOpenDipId: null,
                fileMode: false,
                packagePageMeta: null,
                filesPageMeta: null,
                pageSize: 4,
                currentPage: 1,
                currentFilesPage: 1,
            }
        },
        computed: {
            archiveSelected: function() {
                return this.selectedArchiveUuid.length > 0;
            },
            holdingSelected: function() {
                return this.holdingSelectCounter > 0;
            },
            online: function() {
                return this.selectedLocation == "online";
            },
            offline: function() {
                return this.selectedLocation == "offline";
            },
            packageLastPage: function() {
                return this.packagePageMeta ? this.packagePageMeta.last_page : 1;
            },
            filesLastPage: function() {
                return this.filesPageMeta ? this.filesPageMeta.last_page : 1;
            },
            packageItemsPerPage() {
                return this.packagePageMeta ? this.packagePageMeta.per_page : 4;
            },
            padPackageItems() {
                let entriesOnLastPage = this.dataObjects.length % this.packageItemsPerPage;
                let padEntries = entriesOnLastPage > 0 ? ( this.packageItemsPerPage - entriesOnLastPage ) : 0;
                return this.currentPage != this.packageLastPage ? 0 : padEntries;
            },
            fileItemsPerPage() {
                return this.filesPageMeta ? this.filesPageMeta.per_page : 4;
            },
            padFileItems() {
                let entriesOnLastPage = this.currentOpenObjectFiles.length % this.fileItemsPerPage;
                let padEntries = entriesOnLastPage > 0 ? ( this.fileItemsPerPage - entriesOnLastPage ) : 0;
                let pad = this.currentFilesPage != this.filesLastPage ? 0 : padEntries;
                return pad;
            },
            pageNumber() {
                return this.packagePageMeta ? this.packagePageMeta.current_page : 1;
            },
            filesPageNumber() {
                return this.filesPageMeta ? this.filesPageMeta.current_page : 1;
            },
            queryString: function() {
                let filter = "?location=" + encodeURI(this.selectedLocation);
                if(this.archiveSelected) {
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
                if( this.currentPage ) {
                    filter += "&page=" + this.currentPage;
                }
                return filter;
            },
            pageQueryString: function() {
                let filter = "?page=" + this.currentFilesPage;
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
            queryString: function() {
                this.refreshObjects(this.queryString);
            },
            pageQueryString: function() {
                this.refreshFileObjects(this.pageQueryString);
            },

        },
        mounted() {
            axios.get("/api/v1/planning/archives").then( (response) => {
                this.archives = response.data.data;
                let firstArchiveUuid = this.archives[0].uuid;
                this.holdingSelectCounter = 1;
                this.selectedHolding = "All";
                Vue.nextTick( () => {
                    $('#archivePicker').selectpicker('val', firstArchiveUuid);
                });
            });
        },
        methods: {
            refreshObjects(queryString){
                axios.get("/api/v1/access/dips"+queryString).then( (dips ) => {
                    this.dataObjects = dips.data.data;
                    this.packagePageMeta = dips.data.meta;
                });
            },
            packageUpdatePage( pageWrapper ) {
                this.currentPage = pageWrapper.page;
            },
            filesUpdatePage( pageWrapper ) {
                this.currentFilesPage = pageWrapper.page;
            },
            openObject: async function( dipId ) {
                axios.get("/api/v1/access/dips/"+dipId+"/files").then( async ( dipFilesResponse ) =>  {
                    this.currentOpenObjectFiles = dipFilesResponse.data.data;
                    this.filesPageMeta = dipFilesResponse.data.meta;
                    this.currentPage = 1;
                    this.currentOpenDipId = dipId;
                    this.fileMode = true;
                });
            },
            refreshFileObjects(filesQueryString){
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

            holdingSelectionChanged: function(holding, state) {
                this.fileMode = false;
                if(holding == 0)
                {
                    this.selectedHolding = "All";
                }
                else if(state){
                    this.lastSelectedHolding = holding.data.name;
                    this.holdingSelectCounter++;
                    this.selectedHolding = holding.data.name;
                }
                else{
                    this.holdingSelectCounter--;
                    if(this.holdingSelectCounter === 0)
                    {
                        this.selectedHolding = "";
                    }
                }
            },
            archiveSelectionChanged: function(archiveUuid) {
                this.fileMode = false;
                this.selectedArchiveUuid = archiveUuid;
                this.selectedHolding = "All";
                axios.get("/api/v1/planning/archives/"+archiveUuid+"/holdings").then( (response) => {
                    this.holdings = response.data.data;
                });
            },
            locationSelectionChanged: function(loc) {
                this.fileMode = false;
                this.selectedLocation = loc;
            },
            addObjectToRetrieval: async function(item) {
                let objectFiles = (await( axios.get("/api/v1/access/aips/"+item.id+"/files"))).data;

                objectFiles.map( async (file) => {
                    this.retrievalItems.push(file);
                    await (axios.post('/api/v1/storage/retrievals/add', {
                        'fileId' : file.id,
                    }));
                });
            },
            addFileToRetrieval: async function(file) {
                this.retrievalItems.push(file);
                await (axios.post('/api/v1/storage/retrievals/add', {
                    'fileId' : file.id,
                }));
            },
        },
    }
</script>
