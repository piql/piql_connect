<template>
    <div class="form-group">
        <label v-if="showLabel" for="holdingPicker" class="col-form-label-sm">
            {{label}}
        </label>
        <div :class="inputValidation">
            <select v-model="selection" :id="elementId" v-if="!holdings" disabled class="form-control" data-live-search="true" :data-none-selected-text="$t('nothingSelected')" @change="selChange">
                <option v-for="holding in holdingsWithWildcard" :key="holding.id" v-bind:value="holding.uuid">
                    {{holding.title}}
                </option>
            </select>
            <select v-model="selection" :id="elementId" v-else class="form-control" data-live-search="true" :data-none-selected-text="$t('nothingSelected')" @change="selChange">

                <option v-for="holding in holdingsWithWildcard" :key="holding.id" v-bind:value="holding.uuid">
                    {{holding.title}}
                </option>
            </select>
        </div>
    </div>
</template>

<script>
import JQuery from 'jquery';
let $ = JQuery;
import RouterTools from '../mixins/RouterTools.js';

export default {
    mixins: [ RouterTools ],
    async mounted() {
        this.collection = this.$route.query.collection;
    },
    methods: {
        dispatchRouting: function() {
            let query = this.$route.query;
            this.collection = query.collection;
            if( query.collection ) {
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
        },
        selChange: function () {
            let uuid = $(`#${this.elementId}`).val();
            this.$emit('selectedHolder',uuid);
            this.inputValidation = !this.required || uuid ? '' : 'mustFill';
        }
    },
    data() {
        return {
            collections: null,
            collection: null,
            holdings: null,
            selection: null,
            initComplete: false,
            inputValidation: '',
        };
    },
    props: {
        useWildCard: {
            type: Boolean,
            default: true
        },
        wildCardLabel: {
            type: String
        },
        label: {
            type: String,
            default: ""
        },
        elementId: {
            type: String,
            default: "holdingPicker"
        },
        required: {
            type: Boolean,
            default: false
        },
    },
    watch: {
        '$route': 'dispatchRouting',

        async collection( collection ) {
            this.disableSelection();
            this.holdings = null;
            if( !collection ) {
                this.holdings = null
                return;
            }
            if( this.collections == null ) {
                this.collections = ((await(axios.get(`/api/v1/metadata/collections`))).data
                .data.map( (a) => { return { uuid: a.uuid, id: a.id };} ));
            }

            //This lookup is a workaround for laravel's apiresource routes with automatic model lookup
            //TODO: Let's look at consequences for using sequential id's rather than uuids in the urls.
            const collectionId = this.collections.find( ar => ar.uuid === collection ).id;
            if( !collectionId ){
                console.error( `No collection with uuid ${collection} was found!` );
                return;
            }

            axios.get(`/api/v1/metadata/collections/${collectionId}/holdings`).then( (response) => {
                if(response.data.data.length > 0){
                    this.holdings = response.data.data;
                }

            })
        },
        selection: function ( holding) {
            if( !this.initComplete ) {
                /* don't change query params when setting selection from page load */
                this.initComplete = true;
                return;
            }

            Vue.nextTick(() => {
                if( holding === this.wildCardLabel ) {
                    this.updateQueryParams({ holding: null, page : null })
                } else {
                    this.updateQueryParams({ holding, page : null });
                }
            })


        },
        holdings: function( holdings ) {
            Vue.nextTick(()=> {
                this.refreshPicker();
            })
            if( !! holdings ) {
                let holdingQuery = this.$route.query.holding ?? this.wildCardLabel ?? this.holdings[0].uuid;
                Vue.nextTick( () => {
                    this.updatePicker( holdingQuery );
                    this.refreshPicker();
                    this.enableSelection();
                    this.selChange();
                });
            } else {
                Vue.nextTick(()=> {
                    this.disableSelection();
                    this.collection = null;
                })
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
