<template>
    <div class="container-fluid">
        <div class="row plistHeader mb-3">
            <div class="col-1"><input type="checkbox" class="checkbox" id="browserList"></div>
            <div class="col-7">{{$t('access.browse.itemInfo')}}</div>
            <div class="col-3">
           </div>


        </div>
        <div class="col" v-if="fileLocation === 'online'">
            <browser-item  v-for="item in onlineItems" v-bind:item="item" v-bind:key="item.id"></browser-item>
        </div>
        <div class="col" v-if="fileLocation === 'offline'">
            <browser-item-offline  v-for="item in offlineItems" v-bind:item="item" v-bind:key="item.id"></browser-item-offline>
        </div>

    </div>
</template>

<script>
import axios from 'axios';
export default {
    data() {
        return {
            fileLocation: "online",
            onlineItems : [],
            offlineItems : [],
        }
    },

    props: {
        bagId: {
            type: String,
            default: ""
        },
    },

    async mounted() {
        axios.get("/api/v1/ingest/bags/").then( (bags) => { 
            this.onlineItems = bags.data.data;
        });
        axios.get("/api/v1/ingest/bags/").then( (bags) => { 
            this.offlineItems = bags.data.data;
        });


    },
}
</script>
