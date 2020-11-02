<template>
    <div>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>{{ $t('ingest.status.jobName') }}</th>
                    <th>{{ $t('ingest.status.size') }}</th>
                    <th>{{ $t('ingest.status.date') }}</th>
                    <th>{{ $t('ingest.status.status') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in items" v-bind:key="item.id">
                    <td><span :class="item.name">{{ item.name }}</span></td>
                    <td>{{ item.size | prettyBytes }}</td>
                    <td>{{ formatShortDate( item.created_at ) }}</td>
                    <td>{{ translatedStatus( item.status ) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="row">
            <div class="col-md-12" style="text-align:center">
                <Pager :meta="pageMeta" :height="height" />
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { format } from 'date-fns';

export default {
    data() {
        return {
            items : {},
            response: null,
            pageMeta: null
        }
    },
    props: {
        jobListUrl: {
            type: String,
            default: ""
        },
        height: {
            type: Number,
            default: 0
        }
    },
    async mounted() {
        let page = this.$route.query.page;
        if( isNaN( page ) || parseInt( page ) < 2 ) {
            this.$route.query.page = 1;
        }

        this.update(this.apiQueryString);
    },
    watch: {
        '$route': 'dispatchRouting'
    },
    methods: {
        async update(query){
            await axios.get(this.jobListUrl+"/buckets" + query, { params: { limit : 8}}).then( (response) => {
                this.items = response.data.data;
                this.pageMeta = response.data.meta;
                this.response = response.data
            });

        },
        dispatchRouting() {
            this.update( this.apiQueryString);
        },
        translatedStatus(status) {
            let statusKey = `ingest.status.${status}`;
            return this.$t(statusKey);
        }
    },
     computed:  {
            apiQueryString: function() {
                let query = this.$route.query;
                let filter = '';

                if( parseInt( query.page ) ) {
                    filter += "?page=" + query.page;
                }
                return filter;
            },

        }
}
</script>
