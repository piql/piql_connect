<template>
    <div class="row thumbnailList">
            <div class="col-sm-2 text-center align-self-center">
                <img class="thumbnailImage" v-bind:src="thumbnailImage">
            </div>
            <div class="col-sm-3 text-truncate align-self-center text-center">
                {{item.storage_properties.bag.name}}
            </div>
            <div class="col-sm-1 p-0 text-truncate align-self-center text-center">
                {{dateFormat(item.archived_at)}}
            </div>
            <div class="col-sm-2 text-truncate align-self-center text-center">
                {{item.storage_properties.holding_name}}
            </div>
            <div class="col-sm-1 align-self-center text-center">
                {{fileCount}}
            </div>
             <div class="col-sm-3 d-inline-flex align-self-center">
                <a class="m-auto" @click="open" href="#" data-toggle="tooltip" title="Access contents" ><i class="fas fa-folder-open actionIcon"></i></a>
                <a v-if="isPreparingDownload" class="m-auto" href="#" data-toggle="tooltip" title="Download file"><i class="fa fa-spinner fa-spin actionIcon text-center"></i></a>
                <a v-else="isPreparingDownload" class="m-auto" @click="download" href="#" data-toggle="tooltip" title="Download file"><i class="fas fa-file-download actionIcon text-center"></i></a>
                <a class="m-auto" @click="preview" href="#" data-toggle="tooltip" title="Preview image"><i class="fas fa-eye actionIcon"></i></a>
              </div>

        </div>
</template>

<script>
    import moment from 'moment';
    import axios from 'axios';
    export default {
        async mounted() {
            this.isPreparingDownload = false;
          axios.get('/api/v1/access/dips/'+this.item.id+'/thumbnails', { responseType: 'blob' }).then ( async (thumbnail) => {
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
                isPreparingDownload: false,
						}
        },
        methods: {
            dateFormat: function(timestamp) {
                return moment(timestamp).format('L');
            },
            open: function(){
                this.$emit('openObject', this.item.id);
            },
            preview: function(){
                this.$emit('showPreview', this.item);
            },
            async download(){
                this.isPreparingDownload = true;
                let filename = `${this.item.storage_properties.bag.name}.tar`; // (await axios.get('/api/v1/access/aips/dips/'+this.item.id+'/filename')).data;
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
