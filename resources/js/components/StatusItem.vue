<template>
    <div v-if="modal">
        <div class="row plist">
            <div class="col">
                {{item.name}}
            </div>
            <div class="col-2">
                {{fileSize}}
            </div>
            <div class="col-3">
                {{item.updated_at}}
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
        getFileSizeSI(bytes) {
            let value = 0;
            let exp = 0;
            if (bytes) {
                exp = Math.floor(Math.log(bytes) / Math.log(1000));
                value = (bytes / Math.pow(1000, exp)).toFixed(2);
            }
            return value + " " + (exp ? 'KMGTPEZY'[exp - 1] + 'B' : 'Bytes')
        },
    },
    computed: {
        fileSize: function() {
            if(this.item.size !== undefined)
                return this.getFileSizeSI(this.item.size);
            else
                return "---";
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
