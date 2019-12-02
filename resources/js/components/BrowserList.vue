<template>
    <div class="container-fluid">
        <div class="row plistHeader">
            <div class="col-sm-1 pl-5 collapse">
                <input type="checkbox" class="checkbox" id="browserList">
            </div>
            <div class="col-sm-3">&nbsp;</div>
            <div class="col-sm-2 text-center">Name</div>
            <div class="col-sm-2 text-center">Ingest Date</div>
            <div class="col-sm-1 text-center">Holding</div>
            <div class="col-sm-1 text-center">Files</div>
            <div class="col-sm-3 text-center">Actions</div>
        </div>

        <span v-if="fileLocation === 'online'">
            <browser-item  v-for="item in dataObjects" :archive="selectedArchive" :holding="selectedHolding" v-bind:item="item" v-bind:key="item.id" @openObject="openObject" @showPreview="showPreview"/>
        </span>
        <span v-if="fileLocation === 'offline'">
            <browser-item-offline  v-for="item in dataObjects" @addFileToRetrieval="addFileToRetrieval" @addObjectToRetrieval="addObjectToRetrieval" :archive="selectedArchive" :holding="selectedHolding" v-bind:item="item" v-bind:key="item.id" @openObject="openObject"/>
        </span>
        <VueEasyLightbox
            :visible="lbVisible"
            :imgs="previewImages"
            :index="index"
            @hide="hideLightBox"
        />
    </div>
</template>

<script>
import axios from 'axios';
import VueEasyLightbox from 'vue-easy-lightbox';
    export default {
        components: {
            VueEasyLightbox
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
            previewImages: []
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

            this.lbVisible = true;
            let allFiles = ( await axios.get('/api/v1/access/dips/'+dip.id+'/files') ).data;
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
        }
    },
}
</script>
