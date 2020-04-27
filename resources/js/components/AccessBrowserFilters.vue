<template>
    <div>
        <form class="form" v-on:submit.prevent>
            <div class="row">

                <div v-if="useArchives" class="col-md-2">
                    <archive-picker
                        :wildCardLabel='$t("All")'
                        :label='$t("Archive")'
                    />
                </div>

                <div v-else="useArchives" class="col-md-2">
                    <label class="col-form-label-sm" for="singleArchive">{{$t('Archive')}}</label>
                    <select class="form-control text-center" id="singleArchive" disabled>
                        <option selected>
                            {{singleArchiveTitle}}
                        </option>
                    </select>
                </div>

                <div class="col-md-2">
                    <holding-picker v-if="useHoldings"
                        :label='$t("Holding")'
                        :wildCardLabel='$t("All")'
                        :useHoldings="false"
                    />
                </div>

                <div class="col-md-2">
                    <DatePicker class="" :label="$t('access.browse.archivedFrom')" query="archived_from"/>
                </div>

                <div class="col-md-2">
                    <DatePicker class="" :label="$t('access.browse.archivedTo')" query="archived_to" />
                </div>

                <div class="col pr-3">
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

            </div>
        </form>
    </div>


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
        Vue.nextTick( () => {
            this.checkDateRange();
        });
    },

    methods: {
        dispatchRouting() {
            this.checkDateRange();
        },
        locationSelectionChanged: function(loc) {
            this.fileMode = false;
            this.selectedLocation = loc;
        },
        checkDateRange() {
            let query = this.$route.query;
            if( query.archived_from && query.archived_to ) {
                let from = query.archived_from;
                let to = query.archived_to;
                if( from > to ) {
                    this.infoToast( this.$t('datepicker.toasts.illegalRangeTitle'), this.$t('datepicker.toasts.illegalRangeMessage') );
                    this.updateQueryParams({ archived_from: query.archived_to });
                }
            }
        }

    }
};
</script>
