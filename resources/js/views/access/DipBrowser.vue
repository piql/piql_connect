<template>
    <div class="w-100">

        <page-heading icon="fa-hdd" :title="$t('access.browse')" :ingress="$t('access.browse.ingress')" />

        <list-header
            :colA="previewCol"
            :colB="filenameCol"
            :colC="actionsCol"
            :colD="closeBtnCol"
            @btnDClicked="close"
        />

        <browser-file-item v-for="item in dipFiles" :item="item" :key="item.id" @showPreview="showPreview"/>

				<div class="row text-center pagerRow">
						<div class="col">
								<Pager :meta='meta' :height='height' />
						</div>
				</div>

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
                css: "col-sm-7 text-left",
                slot: this.$t('access.browse.header.files')
            };
        },
        actionsCol: function() {
            return {
                css: "col-sm-2",
                slot: this.$t('access.browse.header.actions')
            };
        },
        closeBtnCol: function() {
            return {
                css: "col-sm-1",
                slot: "<button class='btn btn-tiny' :title=${this.$t('access.browse.archive.closeButtonTitle')}\"><i class='fas fa-backspace'></i></button>"
            }
        },
        apiQueryString: function() {
            let query = this.$route.query;
            let page = parseInt(query.page);
            return page && page > 0 ? `?page=${page}` : "";
        },

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
        async showPreview ( dip, fileId ) {
            console.log("dip: " +dip);
            console.log("fileId: " +fileId);
            this.lbVisible = true;
            let image = (await axios.get('/api/v1/access/dips/'+dip+'/previews/files/'+fileId, { responseType: 'blob' }));
            let reader = new FileReader();
            reader.onload = e => this.previewImages.push( reader.result );
            reader.readAsDataURL( image.data );
        },
        hideLightBox: function( e ) {
            this.lbVisible = false;
            this.previewImages = [];
        }
    }
};
</script>
