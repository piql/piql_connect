<template>
    <div>
        <form>
        <div class="row listFilter">
                    <label for="fromDate">{{$t('ingest.taskList.from')}}</label>
                    <input type="date" name="fromDate" placeholder="DDMMYY">
                    <label for="toDate">{{$t('ingest.taskList.to')}}</label>
                    <input type="date" name="toDate" placeholder="DDMMYY">
                    <select name="status">
                        <option style="display: none;" disabled="" selected="">{{$t('ingest.taskList.statusFilter')}}</option>
                        <option>{{$t('ingest.taskList.uploadingFilter')}}</option>
                        <option>{{$t('ingest.taskList.processingFilter')}}</option>
                    </select>
                    <input type="search" :placeholder="$t('Search')">
        </div>
        <br/>
        <div class="row plistHeader">
            <div class="col-1"><input type="checkbox" class="checkbox" id="allSips"></div>
            <div class="col">AIP</div>
            <div class="col-3">{{$t('ingest.taskList.ingestDate')}}</div>
            <div class="col-2">{{$t('ingest.taskList.size')}}</div>
            <div class="col-2 listActionItems">&nbsp;</div>
        </div>

        <bag-list-item v-for="item in items" v-bind:item="item" v-bind:key="item.id" @piqlIt="piqlIt"/>
        </form>
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
            jobId: {
                type: String,
                default: ""
            },
        },
        async mounted() {
            let url = "/api/v1/ingest/offline_storage/pending/jobs/"+this.jobId+"/bags";
            console.log(url);
            console.log(this);
            this.items = (await axios.get(url)).data;
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
