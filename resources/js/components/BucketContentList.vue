<template>
    <div>
        <div class="row plistHeader text-truncate mt-2">
            <div class="col-sm-2 text-center">{{$t("access.browse.header.preview")}}</div>
            <div class="col-sm-3 text-left">{{$t("access.browse.header.name")}}</div>
            <div class="col-sm-1 text-center">{{$t("access.browse.header.ingestDate")}}</div>
            <div class="col-sm-2 text-center">{{$t("access.browse.header.holding")}}</div>
            <div class="col-sm-1 text-center">{{$t("access.browse.header.files")}}</div>
            <div class="col-sm-1 text-right">{{$t("access.browse.header.size")}}</div>
            <div class="col-sm-2 text-center">{{$t("access.browse.header.actions")}}</div>
        </div>

        <bucket-content-item  v-for="item in dataObjects" :item="item" :key="item.id" @onDelete="onDelete" @openObject="openObject"  @showPreview="showPreview"/>

        <Lightbox
            ref="lgbx"
            :visible="lbVisible"
            :imgs="previewImages"
            :fileNames="previewFileNames"
            :fileTypes="previewFileTypes"
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
            previewImages: [],
            previewFileNames: [],
            previewFileTypes: [],
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
            allFiles.map( async (file) => {
                let fileId = file.id;
                let fileType = file.mime_type;
                if (this.$refs.lgbx.isPlayable(fileType)) {
                    this.previewImages.push( '/api/v1/media/dips/'+dip.id+'/previews/files/'+fileId );
                } else {
                    let image = (await axios.get('/api/v1/access/dips/'+dip.id+'/previews/files/'+fileId, { responseType: 'blob' }));
                    let reader = new FileReader();
                    reader.onload = e => this.previewImages.push( reader.result );
                    reader.readAsDataURL( image.data );
                }
                this.previewFileTypes.push( fileType );
                this.previewFileNames.push( file.filename.substring(37, file.filename.length-1) );
            });
        },
        hideLightBox: function( e ) {
            this.lbVisible = false;
            this.previewImages = [];
            this.previewFileNames = [];
            this.previewFileTypes = [];
        },
        pageNav: function ( adj ) {
            this.page += adj
            this.previewImages = [];
            this.previewFileNames = [];
            this.previewFileTypes = [];
            this.showPreview(this.previewDip);
        }
    },
}
</script>
