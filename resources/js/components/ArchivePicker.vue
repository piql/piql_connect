<template>
    <div class="form-group w-100">
        <span v-if="!singleCollection">
            <label v-if="showLabel" for="collectionPicker" class="col-form-label-sm">
            {{label}}
        </label>
        <div :class="inputValidation">
            <select v-model="selection" :id="elementId" class="form-control w-100" v-bind:disabled="selectionDisabled" :data-none-selected-text="wildCardLabel" data-live-search="true" @change="selChange">
                <option v-for="collection in collectionsWithWildcard" :key="collection.id" :value="collection.uuid">
                    {{collection.title}}
                </option>
            </select>
        </div>
    </span>
    <span v-else>
        <label class="col-form-label-sm" for="singleCollection">{{$t('Collection')}}</label>
        <select class="form-control text-center" disabled>
            <option selected>
                {{singleCollectionTitle}}
            </option>
        </select>
    </span>
    </div>
</template>

<script>
import RouterTools from '../mixins/RouterTools.js';

export default {
    mixins: [ RouterTools ],

    mounted() {
        axios.get("/api/v1/metadata/collections").then( (response) => {
            this.collections = response.data.data;
        });
    },
    methods: {
        dispatchRouting: function() {
            let query = this.$route.query;
            Vue.nextTick( () => {
                this.updatePicker( query.collection );
            });
        },
        refreshPicker: function() {
            $(`#${this.elementId}`).selectpicker('refresh');
        },
        updatePicker: function( collection ) {
            $(`#${this.elementId}`).selectpicker('val', collection);
            this.refreshPicker();
            this.selChange();
        },
        selChange: function () {
            let val = $(`#${this.elementId}`).val();
            this.inputValidation = this.required && (!val || val == '0') ? 'mustFill' : '';
        }
    },
    data() {
        return {
            selection: null,
            collections: [],
            initComplete: false,
            inputValidation: '',
        };
    },
    props: {
        singleCollection: {
            type: Boolean,
            default: false
        },
        singleCollectionTitle: {
            type: String,
            default: "Your Collection"
        },
        selectionDisabled: {
            type: Boolean,
            default: false
        },
        useWildCard: {
            type: Boolean,
            default: true
        },
        wildCardLabel: {
            type: String
        },
        label: {
            type: String,
            default: null
        },
        elementId: {
            type: String,
            default: "collectionPicker"
        },
        required: {
            type: Boolean,
            default: false
        },
    },
    watch: {
        '$route': 'dispatchRouting',

        selection: function ( collection ){
            if( collection === '0' ){
                this.updateQueryParams({ collection : null, page: null, holding: null });
            } else {
                this.updateQueryParams({ collection, page : null, holding: null });
            }

            this.$emit('loadNewHolders')
        },
        collections: function( collections ){
            if( !! collections ) {
                let collectionQuery = this.$route.query.collection ?? '0';
                    this.refreshPicker();
                    Vue.nextTick( () => {
                        this.updatePicker( collectionQuery );
                    });
            }
        },

    },
    computed: {
        showLabel: function() {
            return this.label.length > 0;
        },
        collectionsWithWildcard: function() {
            /* If it has elements, push a wildcard element ("All") at the start of the list */
            return this.collections && this.collections.length > 1
                ? [ {'id' : 0, 'title': this.wildCardLabel, 'uuid': '0' }, ...this.collections ]
                : this.collections;
        }
    }
}

</script>
