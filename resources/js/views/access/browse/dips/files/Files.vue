<template>
    <div class="w-100">

        <page-heading icon="fa-hdd" :title="$t('access.browse.content')" :ingress="$t('access.browse.content.ingress')" />

        <browser-file-filters :singleCollectionTitle="$t('Your collection')" :subTitle="dipName"/>

        <list-header
            :colA="previewCol"
            :colB="filenameCol"
            :colC="sizeCol"
            :colD="actionsCol"
            @btnDClicked="close"
        />

        <browser-file-item v-for="item in dipFiles" :item="item" :key="item.id" @showPreview="showPreview"/>

        <div class="row text-center pagerRow">
            <div class="col">
                <Pager :meta='meta' :height='height' />
            </div>
        </div>

        <Lightbox
            ref="lgbx"
            :visible="lbVisible"
            :imgs="previewImages"
            :fileNames="previewFileNames"
            :fileTypes="previewFileTypes"
            :index="index"
            :hide="hideLightBox"
        />

    </div>
</template>

<script>
import axios from 'axios';
import Lightbox from '@components/lightbox';
export default {
    components: {
        Lightbox
    },

    watch: {
        '$route': 'dispatchRouting'
    },

    data () {
        return {
            dipFiles: [],
            dipId: 0,
            meta: null,
            prevRoute: null,
            lbVisible: false,
            index: 0,
            dip: null,
            previewFileNames: [],
            previewFileTypes: [],
            previewImages: []
        }
    },

    props: {
        height: {
            type: Number,
            default: 0
        }
    },

    computed: {
        previewCol: function() {
            return {
                css: "col-sm-2",
                slot: this.$t('access.browse.header.preview')
            };
        },
        filenameCol: function() {
            return {
                css: "col-sm-6 text-left",
                slot: this.$t('access.browse.header.files')
            };
        },
        sizeCol: function() {
            return {
                css: "col-sm-2 text-left",
                slot: this.$t('access.browse.header.size')
            };
        },
        actionsCol: function() {
            return {
                css: "col-sm-2",
                slot: this.$t('access.browse.header.actions')
            };
        },
        apiQueryString: function() {
            let query = this.$route.query;
            let page = parseInt(query.page);
            let filter = query.search ? "&search=" + query.search : "";
            return "?" + (page && page > 1 ? "page=" + page : "") + filter;
        },
        dipName: function () {
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

    mounted () {
        let params = this.$route.params;
        this.dipId = params.dipId;
        this.refreshFiles( this.dipId, this.apiQueryString );
    },
    beforeRouteEnter: function( to, from, next ) {
        /* Store the originating route, so that a close returns to
         * the page that was open before we entered the Dip.
         * If the link was opened directly, return to 'access.browse'.
         */

        next( self => {
            self.prevRoute = from.name ? from : { name: 'access.browse' };
        });
    },

    methods: {
        dispatchRouting() {
            this.refreshFiles( this.dipId, this.apiQueryString );
        },
        refreshFiles() {
            let dipId = this.dipId;
            let apiQueryString = this.apiQueryString;
            axios.get(`/api/v1/access/dips/${dipId}/files${apiQueryString}`).then( async ( dipFilesResponse ) =>  {
                this.dipFiles = dipFilesResponse.data.data;
                this.meta = dipFilesResponse.data.meta;
            });
        },
        close() {
            this.$router.push(this.prevRoute);
        },
        async showPreview ( dip, fileId, fileName, fileType ) {
            this.lbVisible = true;
            if (this.$refs.lgbx.isPlayable(fileType)) {
                this.previewImages.push( '/api/v1/media/dips/'+dip+'/previews/files/'+fileId );
            } else {
                let image = (await axios.get('/api/v1/access/dips/'+dip+'/previews/files/'+fileId, { responseType: 'blob' }));
                let reader = new FileReader();
                reader.onload = e => this.previewImages.push( reader.result );
                reader.readAsDataURL( image.data );
            }
            this.previewFileNames.push( fileName );
            this.previewFileTypes.push( fileType );
        },
        hideLightBox: function( e ) {
            this.lbVisible = false;
            this.previewImages = [];
            this.previewFileNames = [];
            this.previewFileTypes = [];
        },
    },
};
</script>
