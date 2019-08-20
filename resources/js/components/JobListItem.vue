<template>
    <div v-if="modal">
        <div class="row plist">
            <div class="col-1"><input type="checkbox" :id=item.uuid class="checkbox"></div>
            <div class="col">
                {{item.name}}
            </div>
            <div class="col-1">
                {{bags.length}}
            </div>
            <div class="col-3">
                {{item.created_at}}
            </div>
            <div class="col-2 listActionItems">
                <i class="fas fa-list cursor-pointer" @click="onClick('/ingest/offline_storage/'+item.id)"></i>
                <i class="fas fa-tags"></i>&nbsp;
                <i class="fas fa-trash-alt"></i>&nbsp;
            </div>
            <div class="col-2 piqlIt" v-on:click="piqlIt">&nbsp;</div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    async mounted() {
        console.log('Task component mounted.');
        this.bags = (await axios.get(this.jobListUrl+"/jobs/"+this.item.id+"/bags")).data;
    },
    props: {
        item: Object,
        jobListUrl: {
            type: String,
            default: ""
        },
    },
    methods: {
        onClick(url){
            window.location = url;
        },
        async piqlIt(e) {
            e.preventDefault(); //????

            let job = (await axios.patch(this.jobListUrl+"/jobs/"+this.item.id)).data;
            if(job.status == 'ingesting')
                this.modal = false;
            console.log(job);
            console.log('Piqled.')
        },


    },

    data() {
        return {
            modal: true,
            bags: [],
        };
    },

}
</script>
