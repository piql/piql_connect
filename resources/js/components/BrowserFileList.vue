<template>
    <div class="w-100">
        <div class="row plistHeader text-truncate text-center mt-2">
          <div class="col-sm-1">{{$t("access.browse.header.preview")}}</div>
            <div class="col text-left">{{$t('access.browse.header.files')}}</div>
            <div class="col-sm-2">{{$t('access.browse.header.actions')}}</div>
        </div>

        <browser-file-item v-for="item in dataObjects" :item="item" v-bind:key="item.id" />

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
        selectedCollection: String,
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
