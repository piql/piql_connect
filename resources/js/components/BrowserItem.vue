<template>
    <div class="row thumbnailList">
        <div class="col-sm-2 text-center align-self-center">
            <img class="thumbnailImage fakeLink" v-bind:src="thumbnailImage" @click="preview">
        </div>
        <div class="col-sm-3 text-truncate align-self-center text-left">
            <router-link :to="{ name: 'access.browse.dips.files', params: { dipId: item.id } }" data-toggle="tooltip" title="Access contents" class="fakeLink">{{item.storage_properties.bag.name}}</router-link>
        </div>
        <div class="col-sm-1 text-truncate align-self-center text-center">
            {{this.item.storage_properties.bag.bagSize | prettyBytes}}
        </div>
        <div class="col-sm-1 p-0 text-truncate align-self-center text-center">
            {{formatShortDate(item.archived_at)}}
        </div>
        <div class="col-sm-1 p-0 text-truncate align-self-center text-center">
            {{formatShortDate(item.archived_at)}}
        </div>
        <div class="col-sm-1 align-self-center text-center">
            {{fileCount}}
        </div>
        <div class="col-sm-3 d-inline-block align-self-center text-center">
            <router-link :to="{ name: 'access.browse.dips.files', params: { dipId: item.id } }" data-toggle="tooltip" :title="$t('access.tip.accessContents')" class="openFiles"><i class="fas fa-folder-open actionIcon"></i></router-link>
            <a v-bind:class="{ collapse: !isPreparingDownload }" class="m-auto cursorPointer" href="" data-toggle="tooltip" :title="$t('access.tip.downloadFile')"><i class="fa fa-spinner fa-spin actionIcon"></i></a>
            <a v-bind:class="{ collapse: isPreparingDownload }" class="m-auto cursorPointer" @click="download" data-toggle="tooltip" :title="$t('access.tip.downloadFile')"><i class="fas fa-file-download actionIcon"></i></a>

            <button class="btn-tiny m-auto previewButton" @click="preview" data-toggle="tooltip" :title="$t('access.tip.previewImage')"><i class="fas fa-eye actionIcon"></i></button>
        </div>

    </div>
</template>

<script>
import axios from 'axios';
export default {
    async mounted() {
        this.isPreparingDownload = false;
        this.thumbnailCancelTokenSource = axios.CancelToken.source();
        axios.get('/api/v1/access/dips/'+this.item.id+'/thumbnails', { responseType: 'blob', cancelToken: this.thumbnailCancelTokenSource.token }).then ( async (thumbnail) => {
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
    },
    props: {
        item: Object,
        archive: String,
        holding: String,
    },

    data() {
        return {
            fileName: "",
            thumbnailImage: "",
            isPreparingDownload: false,
            thumbnailCancelTokenSource: null
        }
    },
    methods: {
        open: function(){
            this.$emit('openObject', this.item.id);
        },
        preview: function(){
            this.$emit('showPreview', this.item);
        },
        async download(){
            this.isPreparingDownload = true;
            let filename = `${this.item.storage_properties.bag.name}.tar`;
            let response = await axios.get('/api/v1/access/aips/dips/'+this.item.id+'/download', { responseType: 'blob' });
            let fileUrl = window.URL.createObjectURL(new Blob([response.data]));
            let fileLink = document.createElement('a');
            fileLink.href = fileUrl;
            fileLink.setAttribute('download', filename);
            document.body.appendChild(fileLink);
            this.isPreparingDownload = false;
            await fileLink.click();
            document.body.removeChild(fileLink);
        },

    },
    computed: {
        downloadUrl: function(){
            return "/api/v1/access/aips/"+this.item.id+"/download";
        },
        fileCount: function(){
            return this.item.storage_properties.bag.fileCount;
        }
    }

}
</script>
<style scoped>
    .fakeLink {
        cursor: pointer;
        color: #808080;
    }
</style>
