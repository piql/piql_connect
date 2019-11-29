<template>
    <div class="container-fluid">
        <div class="row plistHeader">
            <div class="col-sm-1 pl-5 collapse">
                <input type="checkbox" class="checkbox" id="browserList">
            </div>
            <div class="col-sm-3">&nbsp;</div>
            <div class="col-sm-2 text-center">Name</div>
            <div class="col-sm-2 text-center">Date</div>
            <div class="col-sm-1 text-center">Holding</div>
            <div class="col-sm-1 text-center">Files</div>
            <div class="col-sm-3 text-center">Actions</div>
        </div>

        <span v-if="fileLocation === 'online'">
            <browser-item  v-for="item in dataObjects" :archive="selectedArchive" :holding="selectedHolding" v-bind:item="item" v-bind:key="item.id" @openObject="openObject"/>
        </span>
        <span v-if="fileLocation === 'offline'">
            <browser-item-offline  v-for="item in dataObjects" @addFileToRetrieval="addFileToRetrieval" @addObjectToRetrieval="addObjectToRetrieval" :archive="selectedArchive" :holding="selectedHolding" v-bind:item="item" v-bind:key="item.id" @openObject="openObject"/>
        </span>
    </div>
</template>

<script>
import axios from 'axios';
export default {
    props: {
        filters: {
            type: String,
            default: ""
        },
        bagId: {
            type: String,
            default: ""
        },
        location: {
            type: String,
            default: "online"
        },
        selectedArchive: String,
        selectedHolding: String,
        dataObjects: Array,
    },

    async mounted() {
    },

    computed: {
        fileLocation: function() {
            return this.location == "offline" ? "offline" : "online";
        },
    },

    methods: {
        addObjectToRetrieval: function(item) {
            this.$emit('addObjectToRetrieval', item);
        },

        addFileToRetrieval: function(item) {
            this.$emit('addFileToRetrieval', item);
        },

        openObject: function(item) {
            this.$emit('openObject', item);
        },
    },
}
</script>
