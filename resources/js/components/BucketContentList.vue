<template>
    <div>
        <div class="row plistHeader text-truncate mt-2">
            <div class="col-sm-2 text-center">{{$t("access.browse.header.preview")}}</div>
            <div class="col-sm-3 text-left">{{$t("access.browse.header.name")}}</div>
            <div class="col-sm-1 text-center">{{$t("access.browse.header.ingestDate")}}</div>
            <div class="col-sm-2 text-center">{{$t("access.browse.header.holding")}}</div>
            <div class="col-sm-1 text-center">{{$t("access.browse.header.files")}}</div>
            <div class="col-sm-3 text-center">{{$t("access.browse.header.actions")}}</div>
        </div>

        <bucket-content-item  v-for="item in dataObjects" :item="item" :key="item.id" @showPreview="showPreview"/>
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
        dataObjects: Array,
    },
    data() {
        return {
            lbVisible: false,
            index: 0,
            previewImages: []
        }
    },
    methods: {
        openObject: function(item) {
            this.$emit('openObject', item);
        },
        async showPreview ( dip ) {
            /* Grab all previews from a dip and convert to b64, then push to the lightbox.
             * Code could be tidier.
             */

            this.lbVisible = true;
            let allFiles = ( await axios.get('/api/v1/access/dips/'+dip.id+'/files') ).data.data;
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
