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
export default {
    components: {},

    mixins: [ RouterTools ],

    data () {
        return {
            searchTerms: ""
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
            if( !this.searchTerms ) {
                this.updateQueryParams({ [this.query]: null });
                return;
            }
            this.updateQueryParams({ [this.query]: this.searchTerms, page: null });
        },
    },
    computed: {
    },

    created () {
    },

    mounted () {
        let query = this.$route.query[this.query] ?? null;
        this.searchTerms = query;
    },

    methods: {
        dispatchRouting() {
        }
    }
};
</script>
