<template>
    <div class="w-100">
        <page-heading icon="fa-tags" :title="$t('access.metadata.file.title')" :ingress="$t('access.metadata.file.ingress')" />
        <breadcumb :subTitle="dipName"/>
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
        dipId: function () {
            return this.$route.params.dipId;
        },
        fileId: function () {
            return this.$route.params.showFileId;
        },
        dipName: function () {
            if (!this.file && this.dipId > 0) {
                axios.get(`/api/v1/access/dips/${this.dipId}/files/` + this.fileId).then( async ( dipResponse ) =>  {
                    this.file = dipResponse.data;
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
