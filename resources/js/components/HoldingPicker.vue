<template>
    <div class="form-group">
        <label v-if="showLabel" for="holdingPicker" class="col-form-label-sm">
            {{label}}
        </label>
        <select v-model="selection" :id="elementId" class="form-control" data-live-search="true" >
            <option v-for="holding in holdingsWithWildcard" :key="holding.id" v-bind:value="holding.title">
                {{holding.title}}
            </option>
        </select>
    </div>
</template>

<script>
import JQuery from 'jquery';
let $ = JQuery;
import RouterTools from '../mixins/RouterTools.js';

export default {
    mixins: [ RouterTools ],
    async mounted() {
        this.archive = this.$route.query.archive;

    },
    methods: {
        dispatchRouting: function() {
            let query = this.$route.query;
            this.archive = query.archive;
            if( query.archive ) {
                let holdingQuery = query.holding ?? this.wildCardLabel;
                this.updatePicker( holdingQuery );
            }
        },
        enableSelection: function() {
            $(`#${this.elementId}`).selectpicker( 'setStyle', 'collapse', 'remove' );
        },
        disableSelection: function() {
            $(`#${this.elementId}`).selectpicker( 'setStyle', 'collapse', 'add' );
        },
        refreshPicker: function() {
            $(`#${this.elementId}`).selectpicker( 'refresh' );
        },
        updatePicker: function( value ) {
            $(`#${this.elementId}`).selectpicker( 'val', value );
        }
    },
    data() {
        return {
            archive: null,
            holdings: null,
            selection: null,
            initComplete: false
        };
    },
    props: {
        useWildCard: {
            type: Boolean,
            default: true
        },
        wildCardLabel: {
            type: String,
            default: 'All'
        },
        label: {
            type: String,
            default: ""
        },
        elementId: {
            type: String,
            default: "holdingPicker"
        }
    },
    watch: {
        '$route': 'dispatchRouting',

        archive: function( archive ) {
            this.disableSelection();
            this.holdings = null;
            if( !archive ) return;

            axios.get(`/api/v1/planning/archives/${archive}/holdings`).then( (response) => {
                this.holdings = response.data.data; 
                //default selection
                this.selection = this.holdings[0].title;
            })
        },
        selection: function ( holding ) {
            if( !this.initComplete ) {
                /* don't change query params when setting selection from page load */
                this.initComplete = true;
                return;
            }
            if( holding === this.wildCardLabel ) {
                this.updateQueryParams({ holding: null, page : null })
            } else {
                this.updateQueryParams({ holding, page : null });
            }
        },
        holdings: function( holdings ) {
            if( !! holdings ) {
                let holdingQuery = this.$route.query.holding ?? this.wildCardLabel ?? this.holdings[0].title;;
                Vue.nextTick( () => {
                    this.updatePicker( holdingQuery );
                    this.refreshPicker();
                    this.enableSelection();
                });
            } else {
                this.disableSelection();
            }
        },
    },
    computed: {
        showLabel: function() {
            return this.label.length > 0;
        },
        holdingsWithWildcard: function() {
            /* If it has elements, push a wildcard element ("All") at the start of the list */
            if( this.useWildCard ) {
            return this.holdings
                ? [{'id' : 0, 'title': this.wildCardLabel}, ...this.holdings ]
                    : null;
            }
            return this.holdings;
        }
    }
}

</script>
