<template>
    <div>
        <label for="searchContents" class="col-form-label-sm">{{label}}</label>
        <div class="input-group">
            <div class="input-group addon">
                <input v-model="searchTerms" id="searchContents" type="text" class="form-control fg-black" >
                <span class="input-group-addon">
                    <i class="fas fa-search search-icon-inline mt-2 mr-2"></i>
                </span>
            </div>
        </div>
    </div>
</template>

<script>
import RouterTools from '../mixins/RouterTools.js';
import DeferUpdate from '../mixins/DeferUpdate.js';
export default {
    components: {},

    mixins: [ RouterTools, DeferUpdate ],

    data () {
        return {
            searchTerms: "",
        }
    },

    props: {
        label: {
            type: String,
            default: "Search"
        },
        query: {
            type: String,
            default: "search"
        },

    },
    watch: {
        '$route': 'dispatchRouting',
        searchTerms: function( searchTerms ) {
            if( this.updatesDeferred() ) return;
            if( this.$route.query[this.query] ) {
                this.replaceQueryParams({ [this.query]: this.searchTerms, page: null });
            } else {
                this.updateQueryParams({ [this.query]: this.searchTerms, page: null });
            }
        },
    },
    computed: {
    },

    created () {
    },

    mounted () {
        this.deferUpdates();
        let query = this.$route.query[this.query] ?? null;
        this.searchTerms = query;
    },

    methods: {
        dispatchRouting() {
            let query = this.$route.query[this.query] ?? null;
            if( this.searchTerms && query && query != this.searchTerms ) {
                this.replaceQueryParams({ [this.query]: this.searchTerms, page: null });
            } else {
                this.searchTerms = query;
            }
        }
    }
};
</script>
