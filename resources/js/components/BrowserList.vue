<template>
    <div>
        <div class="row plistHeader text-truncate mt-2">
            <div class="col-sm-2 text-center">{{$t("access.browse.header.preview")}}</div>
            <div class="col-sm-3 text-left">{{$t("access.browse.header.name")}}</div>
            <div class="col-sm-1 text-left">{{$t("access.browse.header.size")}}</div>
            <div class="col-sm-1 text-center">{{$t("access.browse.header.ingestDate")}}</div>
            <div class="col-sm-1 text-center">{{$t("access.browse.header.holding")}}</div>
            <div class="col-sm-1 text-center">{{$t("access.browse.header.files")}}</div>
            <div class="col-sm-3 text-center">{{$t("access.browse.header.actions")}}</div>
        </div>

        <span v-if="fileLocation === 'online'">
            <browser-item  v-for="item in dataObjects" :item="item" :key="item.id" @showPreview="showPreview"/>
        </span>
        <span v-if="fileLocation === 'offline'">
            <browser-item-offline  v-for="item in dataObjects" @addFileToRetrieval="addFileToRetrieval" @addObjectToRetrieval="addObjectToRetrieval" :archive="selectedArchive" :holding="selectedHolding" v-bind:item="item" v-bind:key="item.id" @openObject="openObject"/>
        </span>
        <Lightbox
            :visible="lbVisible"
            :imgs="previewImages"
            :index="index"
            :hide="hideLightBox"
            :totalImgs="imgLength"
            :perPage="perPage"
            :page="page"
            :pageNav="pageNav"
        />
    </div>
</template>

<script>
import axios from 'axios';
import Lightbox from './lightbox';
    export default {
        components: {
            Lightbox
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
        location: {
            type: String,
            default: "online"
        },
        selectedArchive: String,
        selectedHolding: String,
        dataObjects: Array,
    },
    data() {
        return {
            lbVisible: false,
            index: 0,
            previewImages: [],
            imgLength: 0,
            perPage: 5,
            page: 1,
            previewDip: {}
        }
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
        async showPreview ( dip ) {
            /* Grab all previews from a dip and convert to b64, then push to the lightbox.
             * Code could be tidier.
             */
            this.previewDip = dip;
            this.imgLength = dip.storage_properties.bag.fileCount;
            this.lbVisible = true;
            let allFiles = ( await axios.get('/api/v1/access/dips/'+dip.id+'/files?page=' + this.page) ).data.data;
            let fileIds = [];
            for ( var i in allFiles ) {
                fileIds.push( allFiles[i].id );
            }
            fileIds.map( async (fileId) => {
              let image = (await axios.get('/api/v1/access/dips/'+dip.id+'/previews/files/'+fileId, { responseType: 'blob' }));
              let reader = new FileReader();
              reader.onload = e => this.previewImages.push( reader.result );
              reader.readAsDataURL( image.data );
            });
        },
        hideLightBox: function( e ) {
            this.lbVisible = false;
            this.previewImages = [];
        },
        pageNav: function ( adj ) {
            this.page += adj
            this.previewImages = [];
            this.showPreview(this.previewDip);
        }
    },
}
</script>
