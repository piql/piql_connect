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
                <label class="col-form-label-sm" for="singleArchive">{{$t('Archive')}}</label>
                <select class="form-control text-center" id="singleArchive" disabled>
                    <option selected>
                        {{singleArchiveTitle}}
                    </option>
                </select>
            </div>

            <div  class="col-md-2 col-lg-2">
                <holding-picker v-if="useHoldings"
                    :label='$t("Holding")'
                    :wildCardLabel='$t("All")'
                    :useHoldings="false"
                />
            </div>
            <div class="col-md-2 col-lg-2">
                <DatePicker :label="$t('access.browse.archivedFrom')" query="archived_from"/>
            </div>

            <div class="col-md-2 col-lg-2">
                <DatePicker :label="$t('access.browse.archivedTo')" query="archived_to"/>
            </div>

            <div class="col">
                <label for="searchContents" class="col-form-label-sm">{{$t('access.browse.withContents')}}</label>
                <div class="input-group">
                    <div class="input-group addon">
                        <input v-model="searchField" id="searchContents" type="text" class="form-control fg-black" >
                        <span class="input-group-addon">
                            <i class="fas fa-search search-icon-inline mt-2 mr-2"></i>
                        </span>
                    </div>
                </div>
            </div>

            <!--div class="col-md-2 pr-0 text-align-right">
                <location-picker :initialSelectedLocation="selectedLocation" :locations="locations" @locationSelectionChanged="locationSelectionChanged"></location-picker>
            </div-->
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
        },
        singleArchiveTitle: {
            type: String,
            default: "Your archive"
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
