<template>
        <div class="row plist">
            <div class="col-sm-2 text-center">
                <img class="thumbnailImage" v-bind:src="thumbnailImage">
            </div>
            <div class="col-sm-1"></div>
            <div class="col text-center align-self-center text-truncate">
                {{fileName}}
            </div>
            <div class="col-sm-1 d-inline-flex align-self-center">
                <a @click="addFileToRetrieval"><i class="fas fa-file-export actionIcon"></i></a>
            </div>
            <div class="col-sm-1">
            </div>
        </div>
</template>

<script>
    import axios from 'axios';
    export default {
        async mounted() {
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
                aipItem: Object
            };
        },
        methods: {
          addFileToRetrieval: function(){
            this.$emit('addFileToRetrieval', this.aipItem);
          }
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
