<template>
    <div>
        <div class="row plist thumbnailList">
            <div class="col-2 text-center">
                <img class="thumbnailImage cursorPointer" v-bind:src="thumbnailImage" @click="preview">
            </div>
            <div class="col-6 text-left align-self-center text-truncate">
                {{fileName}}
            </div>
            <div class="col-2 text-left align-self-center text-truncate">
                {{item.size | prettyBytes}}
            </div>
            <div class="col-2 d-inline text-center align-self-center">
                <a class="m-auto cursorPointer" @click.once="showMetadata" data-toggle="tooltip" :title="$t('access.tip.editMetadata')"><i class="fas fa-tags actionIcon text-center"></i></a>
                <a v-if="isPreparingDownload" class="m-auto cursorPointer" data-toggle="tooltip" :title="$t('access.tip.downloadFile')"><i class="fa fa-spinner fa-spin actionIcon text-center"></i></a>
                <a v-else class="m-auto cursorPointer downloadFile" @click.once="download" data-toggle="tooltip" :title="$t('access.tip.downloadFile')"><i class="fas fa-file-download actionIcon text-center"></i></a>
                <button class="btn-tiny m-auto previewButton" @click="preview" data-toggle="tooltip" :title="$t('access.tip.previewImage')"><i class="fas fa-eye actionIcon"></i></button>
            </div>
            <div class="col-sm-1"></div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    export default {
        async mounted() {
            this.isPreparingDownload = false;
            this.aipFileCancelTokenSource = axios.CancelToken.source();
            axios.get('/api/v1/access/dips/'+this.dipId+'/aipfile/'+this.item.id, { cancelToken: this.aipFileCancelTokenSource.token }).then( (result) => {
                this.aipItem = result.data.data[0];
                this.fileName = this.aipItem.filename;
                this.fileType = this.aipItem.mime_type;
            }).catch(function(exception) {
                if (!axios.isCancel(exception)) {
                    throw(exception);
                }
            });

            this.thumbnailCancelTokenSource = axios.CancelToken.source();
            axios.get('/api/v1/access/dips/'+this.dipId+'/thumbnails/files/'+this.item.id, { responseType: 'blob', cancelToken: this.thumbnailCancelTokenSource.token }).then( (thumbnail) => {
                let reader = new FileReader();
                reader.onload = e => this.thumbnailImage = reader.result;
                reader.readAsDataURL( thumbnail.data );
            }).catch(function(exception) {
                if (!axios.isCancel(exception)) {
                    throw(exception);
                }
            });
        },
        async beforeDestroy() {
            if (this.thumbnailCancelTokenSource) {
                this.thumbnailCancelTokenSource.cancel('Thumbnail request was cancelled');
            }
            if (this.aipFileCancelTokenSource) {
                this.aipFileCancelTokenSource.cancel('AIP file request was cancelled');
            }
        },
        props: {
            item: Object,
            archive: String,
            holding: String,
        },

        data() {
            return {
                fileName: "",
                fileType: "",
                thumbnailImage: "",
                aipItem: Object,
                isPreparingDownload: false,
                thumbnailCancelTokenSource: null,
                aipFileCancelTokenSource: null
            };
        },
        methods: {
            async download(e) {
                e.stopImmediatePropagation();
                this.isPreparingDownload = true;
                let response = await axios.get('/api/v1/access/aips/' + this.aipItem.storable_id + '/file/' + this.aipItem.id + '/download', { responseType: 'blob' });
                let fileLink = document.createElement('a');
                fileLink.href = window.URL.createObjectURL(new Blob([response.data], {type: 'application/octet-stream'}));
                fileLink.download = this.aipItem.filename;
                this.isPreparingDownload = false;
                fileLink.click();
            },
            showMetadata() {
                this.$router.push({ name:'access.browse.dips.files.metadata', params: { dipId: this.dipId, fileId: this.aipItem.id, showFileId: this.item.id } });
            },
          preview: function(){
                this.$emit('showPreview', this.item.storable_id, this.item.id, this.fileName, this.fileType);
            }
        },
        computed: {
            dipId: function() {
                return this.item.storable_id;
            }
        }

    }
</script>
<style scoped>
    .fakeLink {
        cursor: pointer;
    }
</style>
