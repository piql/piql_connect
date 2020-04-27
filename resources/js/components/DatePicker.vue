<template>
    <div class="form-group" >
        <label :for="uid" class="col-form-label-sm">{{label}}</label>
        <b-form-datepicker class="form-fontrol btn-sel" :id="uid"
            v-model="dateValue"
            :date-format-options="{ year: '2-digit', month: '2-digit', day: '2-digit'}"
            :placeholder="placeHolder" :hideHeader="hideHeader"
            :locale="locale" :labelHelp="$t('datepicker.help')"
        >
        </b-form-datepicker> 
    </div>
</template>

<script>
//<input v-model="dateValue" :id="uid" type="date" class="form-control w-auto" :min="min" :max="max">
import JQuery from 'jquery';
let $ = JQuery;
import RouterTools from '../mixins/RouterTools.js';
export default {
    components: {},

    mixins: [ RouterTools ],

    data () {
        return {
            dateValue: null,
            uid: "0"
        }
    },

    props: {
        label: {
            type: String,
            default:  "Date"
        },
        query: {
            type: String,
            default: null
        },
        min: {
            type: String,
            default: "01-01-2020"
        },
        max: {
            type: String,
            default: () => { return new Date().toLocaleDateString("en-US").split("/").join("-"); }
        },
        locale: {
            type: String,
            default: function() { return  this.$t('locale'); }
        },
        hideHeader: {
            type: Boolean,
            default: true
        },
        placeHolder: {
            type: String,
            default: ""
        },
        labelHelp: {
            type: String,
            default: "Mye moro"
        }
    },

    computed: {
        //
    },

    created () {
        //
    },

    mounted () {
        this.uid = encodeURI(`${this.query}_${Math.floor( 100000 * Math.random())}`);
        let query = this.$route.query[this.query] ?? null;
        this.dateValue = query;
    },

    methods: {
        dispatchRouting: function() {
            let query = this.$route.query[this.query];
            if( query ) {
                this.dateValue = query;
            }
        },
    },
    watch: {
        '$route': 'dispatchRouting',
        dateValue: function( dateValue ) {
            if( !this.dateValue ) {
                this.updateQueryParams({ [this.query]: null });
                return;
            }
            this.updateQueryParams({ [this.query]: this.dateValue, page: null, holding: null });
        }
    }
};
</script>
