<template>
    <div class="container-fluid">
        <div class="row plistHeader">
            <div class="col-sm-1"><input type="checkbox" class="checkbox" id="browserList" v-if="false"></div>
            <div class="col-sm-3">{{$t('access.browse.itemInfo')}}</div>
            <div class="col-sm-3">Archive</div>
            <div class="col-sm-3">Holding</div>
        </div>

        <span v-if="fileLocation === 'online'">
            <browser-item  v-for="item in onlineItems" :archive="selectedArchive" :holding="selectedHolding" v-bind:item="item" v-bind:key="item.id"/>
        </span>
        <span v-if="fileLocation === 'offline'">
            <browser-item-offline  v-for="item in offlineItems" @addToRetrieval="addToRetrieval" :archive="selectedArchive" :holding="selectedHolding" v-bind:item="item" v-bind:key="item.id"/>
        </span>
    </div>
</template>

<script>
import axios from 'axios';
export default {
    data() {
        return {
            onlineItems : [],
            offlineItems : [],
        }
    },

    props: {
        filters: {
            type: String,
            default: ""
        },
        bagId: {
            type: String,
            default: ""
        },
        selectedArchive: String,
        selectedHolding: String,
    },

    async mounted() {
        axios.get("/api/v1/ingest/bags/").then( (bags) => { 
            this.onlineItems = bags.data.data;
        });
        axios.get("/api/v1/ingest/bags/").then( (bags) => { 
            this.offlineItems = bags.data.data;
        });
    },

    computed: {
        fileLocation: function() {
            return this.filters.includes("loc=offline") ? "offline" : "online";
        },
    },
    methods: {
        addToRetrieval: function(item) {
            this.$emit('addToRetrieval', item);
        }
    },
}
</script>
