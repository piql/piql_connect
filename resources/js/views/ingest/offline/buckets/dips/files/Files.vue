<template>
    <div class="w-100">
        <page-heading icon="fa-hdd" :title="$t('ingest.offlineStorage.package.list.title')" :ingress="$t('ingest.offlineStorage.package.list.ingress')" />
        <breadcumb :subTitle="subTitle"/>
        <div class="row plistHeader text-truncate text-center mt-2">
          <div class="col-sm-2">{{$t("access.browse.header.preview")}}</div>
            <div class="col-sm-7 text-left">{{$t('access.browse.header.files')}}</div>
            <div class="col-sm-2 text-center">{{$t('access.browse.header.actions')}}</div>
        </div>

        <aip-browser-file-item v-for="item in dipFiles" :item="item" :key="item.id" @showMetadata="showMetadata" @showPreview="showPreview" />

        <Pager :meta='meta' :height='height' />

        <Lightbox
            ref="lgbx"
            :visible="lbVisible"
            :imgs="previewImages"
            :fileNames="previewFileNames"
            :index="index"
            :hide="hideLightBox"
        />

    </div>
</template>

<script>
import Lightbox from '@components/lightbox';
export default {
    components: {
        Lightbox
    },
    data () {
        return {
            dipFiles: [],
            meta: null,
            lbVisible: false,
            index: 0,
            imgLength: 0,
            previewDip: {},
            previewImages: [],
            previewFileNames: [],
            dip: null,
        }
    },

    props: {
        height: {
            type: Number,
            default: 0
        }

    },

    mounted () {
        this.openDip( this.dipId );
    },

    computed: {
        bucketId: function() {
            return this.$route.params.bucketId;
        },
        dipId: function() {
            return this.$route.params.dipId;
        },
	subTitle: function() {
            if (!this.dip && this.dipId > 0) {
                axios.get(`/api/v1/access/dips/${this.dipId}`).then( async ( dipResponse ) =>  {
                    this.dip = dipResponse.data;
                });
            }
            if (this.dip) {
                let nameArr = this.dip.storage_path.split('/');
                nameArr = nameArr[nameArr.length-1].split('-');
                return nameArr[0];
            }
            return "";
        }
    },

    methods: {
        showMetadata( fileId ) {
            this.$router.push({ name:'ingest.offline.buckets.dips.files.metadata',
                params: { bucketId: this.bucketId, dipId: this.dipId, fileId }
            });
        },
        openDip( dipId ) {
            axios.get("/api/v1/access/dips/"+dipId+"/files").then( async ( dipFilesResponse ) =>  {
                this.dipFiles = dipFilesResponse.data.data;
                this.meta = dipFilesResponse.data.meta;
            });
        },
        close() {
            this.$router.go(-1);
        },
        async showPreview ( dip, fileId, fileName ) {
            this.lbVisible = true;
            if (this.$refs.lgbx.isPlayable(fileName)) {
                this.previewImages.push( '/api/v1/media/dips/'+dip+'/previews/files/'+fileId );
            } else {
                let image = (await axios.get('/api/v1/access/dips/'+dip+'/previews/files/'+fileId, { responseType: 'blob' }));
                let reader = new FileReader();
                reader.onload = e => this.previewImages.push( reader.result );
                reader.readAsDataURL( image.data );
            }
            this.previewFileNames.push( fileName );
        },
        hideLightBox: function( e ) {
            this.lbVisible = false;
            this.previewImages = [];
            this.previewFileNames = [];
        }
    }
};
</script>
