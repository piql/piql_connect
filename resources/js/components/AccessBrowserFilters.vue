<template>
    <form class="form mb-1 ml-0 mr-0" v-on:submit.prevent>
        <div class="row mt-1 mb-0 ">
            <div v-if="useArchives" class="col-md-2 col-lg-2 pl-0 pr-0 ">
                <archive-picker
                    :wildCardLabel='$t("All")'
                    :label='$t("Archive")'
                />
            </div>
            <div v-else="useArchives" class="col-md-2 col-lg-2 pl-0 pr-0">
                <label class="col-form-label-sm">{{$t('Archive')}}</label>
                <div class="pl-0 pr-0 form-control align-middle text-center">{{singleArchiveTitle}}</div>
            </div>

            <div v-if="useHoldings" class="col-md-2 col-lg-2">
                <holding-picker
                    :label='$t("Holding")'
                    :wildCardLabel='$t("All")'
                    :useHoldings="true"
                />
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


</template>

<script>

import RouterTools from '../mixins/RouterTools.js';

export default {

    mixins: [ RouterTools ],

    data () {
        return {
            fromDateFilter: "",
            toDateFilter: "",
            searchField: "",
            selectedLocation: "online",
            locations: [
                { name: 'Online', value: 'online' },
                { name: 'Offline', value: 'offline'}
            ],
        }
    },

    props: {
        useArchives: {
            type: Boolean,
            default: true
        },
        useHoldings: {
            type: Boolean,
            default: true
        }
    },
    watch: {
        '$route': 'dispatchRouting'
    },
    computed: {
    },

    created () {
    },

    mounted () {
    },

    methods: {
        dispatchRouting() {
        },
        locationSelectionChanged: function(loc) {
            this.fileMode = false;
            this.selectedLocation = loc;
        },

    }
};
</script>
