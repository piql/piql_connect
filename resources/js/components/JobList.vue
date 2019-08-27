<template>
    <div class="container-fluid">
        <ingest-filter-search></ingest-filter-search>
        <div class="row plistHeader">
            <div class="col-sm-5">{{$t('ingest.offlineStorage.jobName')}}</div>
            <div class="col-sm-1">{{$t('ingest.offlineStorage.numberOfAips')}}</div>
            <div class="col-sm-2">{{$t('ingest.offlineStorage.creationDate')}}</div>
            <div class="col-sm-3 listActionItems">&nbsp;</div>
            <div class="col-sm-1 ">&nbsp;</div>
        </div>

        <job-list-item v-for="item in items" v-bind:item="item" v-bind:key="item.id" :jobListUrl="jobListUrl" @piqlIt="piqlIt"/>
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
