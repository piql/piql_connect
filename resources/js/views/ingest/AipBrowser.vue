<template>
    <div class="w-100">
        <page-heading icon="fa-hdd" :title="$t('ingest.offlineStorage.package.list.title')" :ingress="$t('ingest.offlineStorage.package.list.ingress')" />
        <div class="row plistHeader text-truncate text-center mt-2">
          <div class="col-sm-2">{{$t("access.browse.header.preview")}}</div>
            <div class="col-sm-7 text-left">{{$t('access.browse.header.files')}}</div>
            <div class="col-sm-2 text-center">{{$t('access.browse.header.actions')}}</div>
            <div class="col-sm-1">
                <button class="btn btn-tiny" :title="$t('access.browse.archive.closeButtonTitle')"
                    @click="close"><i class="fas fa-backspace"></i>
                </button>
            </div>
        </div>

        <aip-browser-file-item v-for="item in dipFiles" :item="item" :key="item.id"/>

        <Pager :meta='meta' :height='height' />

    </div>
</template>

<script>
export default {
    data () {
        return {
            dipFiles: [],
            dipId: 0,
            meta: null
        }
    },

    props: {
        height: {
            type: Number,
            default: 0
        }
    },

    mounted () {
        let params = this.$route.params;
        let dipId = params.dipId;
        this.openDip( dipId );
    },

    methods: {
        openDip( dipId ) {
            axios.get("/api/v1/access/dips/"+dipId+"/files").then( async ( dipFilesResponse ) =>  {
                this.dipFiles = dipFilesResponse.data.data;
                this.meta = dipFilesResponse.data.meta;
                this.dipId = dipId;
            });
        },
        close() {
            this.$router.go(-1);
        }
    }
};
</script>
