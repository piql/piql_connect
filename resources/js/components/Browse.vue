<template>
    <div>
        <form class="form mb-3" v-on:submit.prevent>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-3 col-lg-3 col-xs-1">
                        <archive-picker :holdings='archives' :initialSelection='selectedArchiveUuid' :label='archiveSelectLabel' @selectionChanged='archiveSelectionChanged'></archive-picker>
                    </div>

                    <div class="col-sm-1 ml-5 mr-5">
                        <label for="fromDate" class="col-form-label-sm">{{$t('access.browse.archivedFrom')}}</label>
                        <input v-model="fromDateFilter" id="fromDate" type="date" class="form-control w-auto">
                    </div>

                    <div class="col-sm-1 mr-5">
                        <label for="toDate" class="col-form-label-sm">{{$t('access.browse.archivedTo')}}</label>
                        <input v-model="toDateFilter" id="toDate" type="date" class="form-control w-auto">
                    </div>

                    <div class="col-sm-3 mr-5 form-group">
                        <label for="searchContents" class="col-form-label-sm">{{$t('access.browse.withContents')}}</label>
                        <div class="input-group">
                            <div class="input-group addon">
                                <input v-model="searchField" id="searchContents" type="text" class="form-control">
                                <span class="input-group-addon">
                                    <i class="fas fa-search search-icon-inline"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-1 ml-5">
                        <location-picker :holding='selectedArchiveUuid' :initialSelectedLocation="selectedLocation" :locations="locations" @locationSelectionChanged="locationSelectionChanged"></location-picker>
                    </div>
                </div>
            </div>
        </form>

        <hr class="row m-0">
        <div class="row">
            <div class="col-sm-3 col-lg-2 col-xs-1 mt-5">
                <fond-select v-if="archiveSelected" @fondSelectionChanged="fondSelectionChanged" :holdings="selectedArchiveHoldings"></fond-select>
            </div>
            <div class="col-sm-8">
                <span v-if="fileMode === false">
                    <browser-list v-if="fondSelected" @openObject="openObject" :location="selectedLocation" :dataObjects="currentObjects"  @addObjectToRetrieval="addObjectToRetrieval" :selectedArchive="selectedArchiveUuid" :selectedHolding="selectedFond"/>
                    <identity v-else></identity>
                </span>
                <span v-if="fileMode">
                    <browser-file-list :dataObjects="currentOpenObjectFiles" :location="selectedLocation" @close="closeFileList" @addFileToRetrieval="addFileToRetrieval" />
                </span>
            </div>
            <div class="col-sm-2 mt-5">
              <primary-contact v-if="fondSelected === false" />
                    <online-actions v-if="online"/>
                <div v-if="offline" class="retrievalItems border-none w-75">
                  Items for retrieval: <span class="float-right"> {{numberOfFilesForRetrieval}}</span>
                </div>

                    <offline-actions v-if="offline"/>
                </span>
            </div>
        </div>
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
            fondSelectCounter: 0,
            lastSelectedFond: "",
            selectedFond: "",
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
            holdings: [],
            selectedArchiveHoldings: [],
            retrievalItems: [],
            dataObjects: [],
            currentOpenObjectFiles: [],
            fileMode: false
        }
    },
    computed: {
        archiveSelected: function() {
            return this.selectedArchiveUuid.length > 0;
        },
        fondSelected: function() {
            return this.fondSelectCounter > 0;
        },
        online: function() {
            return this.selectedLocation == "online";
        },
        offline: function() {
            return this.selectedLocation == "offline";
        },
        queryString: function() {
            let filter = "?location=" + encodeURI(this.selectedLocation);
            if(this.archiveSelected) {
                filter += "&archive=" + encodeURI(this.selectedArchiveUuid);
            }
            if(this.selectedFond){
                if(this.selectedFond !== "All"){
                    filter += "&holding=" + encodeURI(this.selectedFond);
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
    },
    mounted() {
        axios.get("/api/v1/planning/holdings").then( (response) => {
            this.archives = response.data.data;
            let firstArchiveUuid = this.archives[0].uuid;
            this.fondSelectCounter = 1;
            this.selectedFond = "All";
            Vue.nextTick( () => {
                $('#archivePicker').selectpicker('val', firstArchiveUuid);
            });
        });
    },
    methods: {
        refreshObjects(queryString){
            axios.get("/api/v1/ingest/bags/"+queryString).then( (bags) => {
                this.dataObjects = bags.data.data;
            });
        },
        fondSelectionChanged: function(fond, state) {
            this.fileMode = false;
            if(fond == 0)
            {
                this.selectedFond = "";
            }
            else if(state){
                this.lastelectedFond = fond.data.name;
                this.fondSelectCounter++;
                this.selectedFond = fond.data.name;
            }
            else{
                this.fondSelectCounter--;
                if(this.fondSelectCounter === 0)
                {
                    this.selectedFond = "";
                }
            }
        },
        archiveSelectionChanged: function(archiveUuid) {
            this.fileMode = false;
            this.selectedArchiveUuid = archiveUuid;
            this.selectedFond = "All";
            axios.get("/api/v1/planning/holdings/"+archiveUuid+"/fonds").then( (response) => {
                this.selectedArchiveHoldings = response.data.data;
            });
        },
        locationSelectionChanged: function(loc) {
            this.fileMode = false;
            this.selectedLocation = loc;
        },
        addObjectToRetrieval: async function(item) {
            console.log("browse add object:");
            console.log(item);
            let objectFiles = (await( axios.get("/api/v1/ingest/bags/"+item.id+"/files"))).data;

            objectFiles.map( async (file) => {
                this.retrievalItems.push(file);
                await (axios.post('/api/v1/storage/retrievals/add', {
                    'fileId' : item.id,
                }));
          });
        },
        addFileToRetrieval: async function(item) {
            this.retrievalItems.push(item);
            await (axios.post('/api/v1/storage/retrievals/add', {
                'fileId' : item.id,
           }));
        },
        openObject: async function(bagId) {
            this.currentOpenObjectFiles = (await( axios.get("/api/v1/ingest/bags/"+bagId+"/files"))).data;
            this.fileMode = true;
        },
        closeFileList: function() {
            this.fileMode = false;
        },
    },
}
</script>
