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

        <bucket-content-item  v-for="item in dataObjects" :item="item" :key="item.id" @onDelete="onDelete" @openObject="openObject"  @showPreview="showPreview"/>

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
        dataObjects: Array,
    },
    data() {
        return {
            lbVisible: false,
            index: 0,
            imgLength: 0,
            perPage: 5,
            page: 1,
            previewDip: {},
            previewImages: []
        }
    },
    methods: {
        openObject: function(itemId) {
            this.$emit('openObject', itemId);
        },
        async onDelete( item ) {
            this.$emit('onDelete', item);
        },
        async showPreview ( dip ) {
            /* Grab all previews from a dip and convert to b64, then push to the lightbox.
             * Code could be tidier.
             */
            this.previewDip = dip;
            this.lbVisible = true;
            let allFiles = ( await axios.get('/api/v1/access/dips/'+dip.id+'/files?page=' + this.page) ).data.data;
            this.imgLength = dip.storage_properties.bag.fileCount;
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
