<template>
    <div> 
        <div class="row plistHeader text-truncate text-center mt-2">
          <div class="col-sm-3">{{$t("access.browse.header.preview")}}</div>
            <div class="col-sm-7">{{$t('access.browse.header.file')}}</div>
            <div class="col-sm-1 text-left">{{$t('access.browse.header.actions')}}</div>
            <div class="col-sm-1"><a href="#" :title="$t('access.browse.archive.closeButtonTitle')" @click.once="close"><i class="fas fa-backspace plistIcon"></i></a></div>
        </div>

        <span v-if="online">
          <browser-file-item  v-for="item in dataObjects" :archive="selectedArchive" :holding="selectedHolding" 
            v-bind:item="item" v-bind:key="item.id" @openObject="openObject"/>
        </span>
        <span v-if="offline">
            <browser-file-item-offline  v-for="item in dataObjects" :archive="selectedArchive" :holding="selectedHolding" 
            v-bind:item="item" v-bind:key="item.id" @openObject="openObject" @addFileToRetrieval="addFileToRetrieval"/>
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
        dipId: {
            type: Number,
            default: 0 
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
        addFileToRetrieval: function(item) {
            this.$emit('addFileToRetrieval', item);
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
