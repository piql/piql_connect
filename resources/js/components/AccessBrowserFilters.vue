<template>
    <div>
        <form class="form" v-on:submit.prevent>
            <div class="row mb-5">

                <div v-if="filters.archives && useArchives" class="col-lg-3 col-md-3 col-sm">
                    <archive-picker
                        :wildCardLabel='$t("All")'
                        :label='$t("Archive")'
                    />
                </div>

                <div v-if="filters.holdings" class="col-lg-2 col-md-3 col-sm">
                    <holding-picker v-if="useHoldings"
                        :label='$t("Holding")'
                        :wildCardLabel='$t("All")'
                        :useHoldings="false"
                    />
                </div>

                <div v-if="filters.archivedFrom" class="col-lg-2 col-md-3 col-sm">
                    <DatePicker class="" :label="$t('access.browse.archivedFrom')" query="archived_from"/>
                </div>

                <div v-if="filters.archivedTo" class="col-lg-2 col-md-3 col-sm">
                    <DatePicker class="" :label="$t('access.browse.archivedTo')" query="archived_to" />
                </div>

                <div v-if="filters.search" class="col-lg-3 col-md col-sm-12">
                    <SearchField :label="$t('access.browse.withContents')" />
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
        filters: {
            type: Object,
            default: function() {
                return {
                    archives: true,
                    holdings: true,
                    archivedFrom: true,
                    archivedTo: true,
                    search: true
                };
            }
        },
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
