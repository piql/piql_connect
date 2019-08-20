<template>
    <div v-if="modal">
        <div class="row plist">
            <div class="col-4">
                {{item.name}}
            </div>
            <div class="col">
            </div>
            <div class="col-3">
                {{ $t('ingest.status.preparing') }}
            </div>
       </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    async mounted() {
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
    },

    data() {
        return {
            modal: true,
            bags: [],
        };
    },

}
</script>
