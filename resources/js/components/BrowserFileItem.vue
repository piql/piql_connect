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
                {{fileSize}}
            </div>
            <div class="col-2 d-inline text-center align-self-center">
                <a class="m-auto" @click.once="showMetadata" data-toggle="tooltip" title="Edit metadata"><i class="fas fa-tags actionIcon text-center"></i></a>
                <a v-if="isPreparingDownload" class="m-auto" data-toggle="tooltip" title="Download file"><i class="fa fa-spinner fa-spin actionIcon text-center"></i></a>
                <a v-else class="m-auto" @click.once="download" data-toggle="tooltip" title="Download file"><i class="fas fa-file-download actionIcon text-center"></i></a>
                <button class="btn-tiny m-auto" @click="preview" data-toggle="tooltip" title="Preview image"><i class="fas fa-eye actionIcon"></i></button>
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
            axios.get( '/api/v1/access/dips/'+this.dipId+'/aipfile/'+this.item.id ).then( (result) => {
                this.aipItem = result.data.data[0];
                this.fileName = this.aipItem.filename;
            });

            axios.get('/api/v1/access/dips/'+this.dipId+'/thumbnails/files/'+this.item.id, { responseType: 'blob' }).then( (thumbnail) => {
                let reader = new FileReader();
                reader.onload = e => this.thumbnailImage = reader.result;
                reader.readAsDataURL( thumbnail.data );
            });
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
                aipItem: Object,
                isPreparingDownload: false
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
                this.$router.push({ name:'access.browse.dips.files.metadata', params: { dipId: this.dipId, fileId: this.aipItem.id } });
            },
	    preview: function(){
                this.$emit('showPreview', this.item.storable_id, this.item.id);
            }
        },
        computed: {
            dipId: function() {
                return this.item.storable_id;
            },
            fileSize: function(){
                let size = this.item.size/1024;
                let metric = "k";
                if (size > 1024) {
                    size /= 1024;
                    metric = "m";
                }
                if (size > 1024) {
                    size /= 1024;
                    metric = "g";
                }
                return Math.round(size) + " " + metric + "b";
            }
        }

    }
</script>
<style scoped>
    .fakeLink {
        cursor: pointer;
    }
</style>
