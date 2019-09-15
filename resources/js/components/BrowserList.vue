<template>
    <div class="container-fluid">
        <div class="row plistHeader">
            <div class="col-sm-1"><input type="checkbox" class="checkbox" id="browserList" v-if="false"></div>
            <div class="col-sm-3">{{$t('access.browse.itemInfo')}}</div>
            <div class="col-sm-3">Archive</div>
            <div class="col-sm-3">Holding</div>
        </div>

        <span v-if="fileLocation === 'online'">
            <browser-item  v-for="item in dataObjects" :archive="selectedArchive" :holding="selectedHolding" v-bind:item="item" v-bind:key="item.id"/>
        </span>
        <span v-if="fileLocation === 'offline'">
            <browser-item-offline  v-for="item in dataObjects" @addToRetrieval="addToRetrieval" :archive="selectedArchive" :holding="selectedHolding" v-bind:item="item" v-bind:key="item.id"/>
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
        addToRetrieval: function(item) {
            this.$emit('addToRetrieval', item);
        }
    },
}
</script>
