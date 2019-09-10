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
        <div class="row">
            <div class="col">
                current filters: {{ completeFilter }}
            </div>
        </div>

        <hr class="row m-0">
        <div class="row">
            <div class="col-sm-3 col-lg-2 col-xs-1 mt-5">
                <fond-select @fondSelectionChanged="fondSelectionChanged" :holdings="selectedArchiveHoldings"></fond-select>
            </div>
            <div class="col-sm-8">
                <browser-list v-if="fondSelected" :selectedFond="lastSelectedFond"></browser-list>
                <identity v-else></identity>
            </div>
            <div class="col-sm-2 mt-5">
                <online-actions v-if="fondSelected"></online-actions>
                <primary-contact v-else></primary-contact>
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
            selectedArchiveUuid: '9aae5540-d3ec-11e9-9a0b-ddd5a3958760',
            archiveSelectLabel: "Archive",
            holdings: [],
            selectedArchiveHoldings: [],
        }
    },
    computed: {
        fondSelected: function() {
            return this.fondSelectCounter > 0;
        },
        completeFilter: function() {
            let filter = "?holding=" + encodeURI(this.selectedArchiveUuid);
            filter += "&loc=" + encodeURI(this.selectedLocation);
            if(this.selectedFond){
                filter += "&fond=" + encodeURI(this.selectedFond);
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
    },
    mounted() {
        axios.get("/api/v1/planning/holdings").then( (response) => {
            this.archives = response.data.data;
            Vue.nextTick( () => {
                $('#archivePicker').selectpicker();
            });
        });
    },
    methods: {
        fondSelectionChanged: function(fond, state) {
            if(state){
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
            this.selectedArchiveUuid = archiveUuid;
            axios.get("/api/v1/planning/holdings/"+archiveUuid+"/fonds").then( (response) => {
                this.selectedArchiveHoldings = response.data.data;
            });
        },
        locationSelectionChanged: function(location) {
            this.selectedLocation = location;
        }
    },
}
</script>
