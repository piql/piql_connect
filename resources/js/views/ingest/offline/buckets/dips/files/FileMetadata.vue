<template>
    <div class="w-100">
        <page-heading icon="fa-tags" :title="$t('ingest.metadata.file.title')" :ingress="$t('ingest.metadata.file.ingress')" />
        <breadcumb :subTitle="fileName"/>
        <dublin-core baseUrl="/api/v1/access/files" :readOnly="true"> </dublin-core>
    </div>
</template>

<script>
import axios from 'axios';
export default {
    data () {
        return {
            file: null,
        }
    },
    computed: {
        fileId: function () {
            return this.$route.params.fileId;
        },
        fileName: function () {
            if (!this.file && this.fileId > 0) {
                axios.get(`/api/v1/access/dips/files/` + this.fileId).then( async ( resp ) =>  {
                    this.file = resp.data;
                });
            }
            if (this.file) {
                let fileArr = this.file.filename.split('-');
                return fileArr[fileArr.length-1];
            }
            return "";
        }
    }
}
</script>
