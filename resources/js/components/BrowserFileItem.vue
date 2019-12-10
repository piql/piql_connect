<template>
    <div>
        <div class="row plist thumbnailList">
            <div class="col-sm-3 text-center">
                <img class="thumbnailImage" v-bind:src="thumbnailImage">
            </div>
            <div class="col text-center align-self-center">
                {{item.filename}}
            </div>
            <div class="col-sm-1 ml-3 text-right align-self-center">
                <a @click.once="download" href="#" data-toggle="tooltip" title="Download normalized file"><i class="fas fa-file-download actionIcon text-center"></i></a>
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
            let thumbnail = await axios.get('/api/v1/access/dips/'+this.dipId+'/thumbnails/files/'+this.item.id, { responseType: 'blob' });
            let reader = new FileReader();
            reader.onload = e => this.thumbnailImage = reader.result;
            reader.readAsDataURL( thumbnail.data );
        },
        props: {
            item: Object,
            archive: String,
            holding: String,
        },

        data() {
            return {
                fileName: "",
                thumbnailImage: ""
            };
        },
        methods: {
            dateFormat: function(item){
                return moment(item.created_at).format('L');
            },
            async download(e) {
                e.stopImmediatePropagation();
                let item = this.item;
                let allFiles = ( await axios.get('/api/v1/access/dips/'+this.dipId+'/files') ).data.data;
                let fileId = 0;
                for ( var i in allFiles ) {
                    if ( allFiles[i].filename === this.item.filename ){
                        fileId = allFiles[i].id;
                    }
                }
                let response = await axios.get('/api/v1/access/dips/'+this.dipId+'/downloads/files/'+fileId, { responseType: 'blob' });
                let fileLink = document.createElement('a');
                fileLink.href = window.URL.createObjectURL(new Blob([response.data], {type: 'application/octet-stream'}));
                fileLink.download = this.item.filename;
                fileLink.click();
            },


        },
        computed: {
            downloadUrl: function(){
                return "/api/v1/ingest/files/"+this.item.id+"/download";

            },
            dipId: function() {
                return this.item.storable_id;
            }
        }

    }
</script>
