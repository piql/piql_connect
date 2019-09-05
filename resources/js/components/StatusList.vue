<template>
    <div class="container-fluid">

        <ingest-filter-search v-bind:filters="['ingest.status.preparing','ingest.status.writing']"/>


        <div class="row plistHeader">
            <div class="col-4">{{ $t('ingest.status.jobName') }}</div>
            <div class="col">&nbsp;</div>
            <div class="col-3">{{ $t('ingest.status.status') }}</div>
        </div>

        <status-item v-for="item in items" v-bind:item="item" v-bind:key="item.id" :jobListUrl="jobListUrl" @piqlIt="piqlIt"/>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            items : {}
        }
    },
    props: {
        jobListUrl: {
            type: String,
            default: ""
        },
    },
    async mounted() {
        this.items = (await axios.get(this.jobListUrl+"/jobs")).data;
        console.log(this.items);

        console.log('TaskList component mounted.')
    },
    methods: {
        async piqlIt(id) {
            await axios.post("/api/v1/ingest/bags/"+id+"/piql");
            this.items = (await axios.get("/api/v1/ingest/bags/complete")).data;
        },
    },
}
</script>
