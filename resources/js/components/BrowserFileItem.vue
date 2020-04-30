<template>
    <div>
        <div class="row plist thumbnailList">
            <div class="col-sm-2 text-center">
                <img class="thumbnailImage" v-bind:src="thumbnailImage">
            </div>
            <div class="col-sm-1"></div>
            <div class="col text-left align-self-center text-truncate">
                {{fileName}}
            </div>
            <div class="col-sm-2 d-inline-flex align-self-center">
                <a class="m-auto" @click.once="showMetadata" href="#" data-toggle="tooltip" title="Edit metadata"><i class="fas fa-tags actionIcon text-center"></i></a>
                <a v-if="isPreparingDownload" class="m-auto" href="#" data-toggle="tooltip" title="Download file"><i class="fa fa-spinner fa-spin actionIcon text-center"></i></a>
                <a v-else="isPreparingDownload" class="m-auto" @click.once="download" href="#" data-toggle="tooltip" title="Download file"><i class="fas fa-file-download actionIcon text-center"></i></a>
            </div>
            <div class="col-sm-1"></div>
        </div>
    </div>
</template>

<script>
    import moment from 'moment';
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
            dateFormat: function(item){
                return moment(item.created_at).format('L');
            },
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
                window.location = "/access/browse/files/"+this.aipItem.id+"/metadata";
            }
        },
        computed: {
            dipId: function() {
                return this.item.storable_id;
            }
        }

    }
</script>
