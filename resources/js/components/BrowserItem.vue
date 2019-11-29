<template>
    <div class="row plist">
            <div class="col-sm-1 text-center align-self-center collapse">
              <input type="checkbox" class="checkbox collapse" id="browserList" >
            </div>
            <div class="col-sm-3 text-center">
                <img v-bind:src="thumbnailImage">
            </div>
            <div class="col-sm-2 align-self-center text-center">
                {{item.storage_properties.bag.name}}
            </div>
            <div class="col-sm-2 align-self-center text-center">
                {{dateFormat(item.created_at)}}
            </div>
            <div class="col-sm-1 align-self-center text-center">
                {{item.storage_properties.holding_name}}
            </div>
            <div class="col-sm-1 align-self-center text-center">
                {{item.storage_properties.bag.fileCount}}
            </div>
            <div class="col-sm-1 align-self-center text-center">
                <a v-bind:href="downloadUrl" data-toggle="tooltip" title="Download file"><i class="fas fa-file-download titleIcon text-center"></i></a>
            </div>
            <div class="col-sm-1 align-self-center text-center">
                <a @click="open" href="#" data-toggle="tooltip" title="Access contents" ><i class="fas fa-folder-open titleIcon"></i></a>
            </div>
            <div class="col-sm-1 align-self-center text-center">
                <a @click="preview" href="#" data-toggle="tooltip" title="Preview image"><i class="fas fa-eye titleIcon"></i></a>
            </div>
        </div>
</template>

<script>
    import moment from 'moment';
    import axios from 'axios';
    export default {
        async mounted() {
						let response = await axios.get('/api/v1/access/dips/'+this.item.id+'/thumbnails', { responseType: 'arraybuffer' });
						this.thumbnailImage = `data:${response.headers['content-type']};base64,${btoa(String.fromCharCode(...new Uint8Array(response.data)))}`;
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
						}
        },
        methods: {
            dateFormat: function(item){
                return moment(item.created_at).format('L');
            },
            open: function(){
                this.$emit('openObject', this.item.id);
            },
            preview: function(){
                this.$emit('openObject', this.item.id);
            },
            download: function(){
                this.$emit('openObject', this.item.id);
            },



        },
        computed: {
            downloadUrl: function(){
                return "/api/v1/ingest/aips/"+this.item.id+"/download";

            },
/*
               return axios.get('/api/v1/access/dips/'+this.item.id+"/thumbnails")
                   .then( (response ) => {
                       let img = btoa(
                           new UInt8Array( response.data ).recude( data, byte) => data
                   }
*/
        }

    }
</script>
