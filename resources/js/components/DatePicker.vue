<template>
    <div class="form-group" >
        <label :for="uid" class="col-form-label-sm">{{label}}</label>
        <b-form-datepicker class="btn-sel" :id="uid"
            v-model="dateValue"
            :max="maxDate" :min="minDate"
            :date-format-options="{ year: '2-digit', month: '2-digit', day: '2-digit'}"
            :placeholder="placeHolder" :hideHeader="hideHeader"
            :locale="locale" :labelHelp="$t('datepicker.help')"
        >
        </b-form-datepicker>
    </div>
</template>

<script>
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
        minDate: {
            type: String,
            default: "1970-1-1"
        },
        maxDate: {
            type: String,
            default: function() {
                let today = new Date();
                return `${today.getFullYear()}-${(today.getMonth() + 1)}-${today.getDate()}`;
            },
        },
        locale: {
            type: String,
            default: function() { return this.$t('locale'); }
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
            default: "Select a date"
        }
    },

    computed: {
    },

    created () {
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
                Vue.nextTick( () => {
                    this.dateValue = query;
                });
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
            this.updateQueryParams({ [this.query]: this.dateValue, page: null });
        }
    }
};
</script>
