<template>
    <div class="container-fluid">
        <div class="row plistHeader">
            <div class="col-sm-1"><input type="checkbox" class="checkbox" id="browserList" v-if="false"></div>
            <div class="col-sm-9">{{$t('Filename')}}</div>
            <div class="col-sm-2"></div>
        </div>

        <span v-if="online">
            <browser-file-item  v-for="item in dataObjects" :archive="selectedArchive" :holding="selectedHolding" v-bind:item="item" v-bind:key="item.id" @openObject="openObject"/>
        </span>
        <span v-if="offline">
            <browser-file-item-offline  v-for="item in dataObjects" :archive="selectedArchive" :holding="selectedHolding" v-bind:item="item" v-bind:key="item.id" @openObject="openObject"/>
        </span>

        <div class="row plist">
            <div class="col-sm-1">
            </div>
            <div class="col-sm-8 mr-5 ">
                Close bag
            </div>
            <div class="col-sm-1">
                <a href="#" @click="close"><i class="fas fa-backspace titleIcon"></i></a>
            </div>
        </div>
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
        online: function() {
            return this.location == "online";
        },
        offline: function() {
            return this.location == "offline";
        },
   },

    methods: {
        addToRetrieval: function(item) {
            this.$emit('addToRetrieval', item);
        },
        openObject: function(item) {
            this.$emit('openObject', item);
        },
        close: function(){
            this.$emit('close');
        },
    },
}
</script>
