<template>
    <div>
        <div class="row plist thumbnailList">
            <div class="col-2 text-center">
                <img class="thumbnailImage cursorPointer" v-bind:src="thumbnailImage" @click="preview">
            </div>
            <div class="col-5 text-left align-self-center text-truncate">
                {{fileName}}
            </div>
            <div class="col-2 text-right align-self-center text-truncate">
               {{aipItem.size | prettyBytes}}
            </div>
            <div class="col-2 d-inline text-center align-self-center">
                <a class="m-auto" @click.once="showMetadata" data-toggle="tooltip" title="Edit metadata"><i class="fas fa-tags actionIcon text-center"></i></a>
                <button class="btn-tiny m-auto" @click="preview" href="" data-toggle="tooltip" title="Preview image"><i class="fas fa-eye actionIcon"></i></button>
            </div>
            <div class="col-sm-1"></div>
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
                this.fileType = this.aipItem.mime_type;
            });

            axios.get('/api/v1/access/dips/'+this.dipId+'/thumbnails/files/'+this.item.id, { responseType: 'blob' }).then( (thumbnail) => {
                let reader = new FileReader();
                reader.onload = e => this.thumbnailImage = reader.result;
                reader.readAsDataURL( thumbnail.data );
            });
        },
        props: {
            item: Object,
        },

        data() {
            return {
                fileName: "",
                fileType: "",
                thumbnailImage: "",
                aipItem: Object,
            };
        },
        methods: {
	          preview: function(){
                this.$emit('showPreview', this.dipId, this.item.id, this.fileName, this.fileType);
            },
            showMetadata() {
                this.$emit('showMetadata', this.aipItem.id );
            }
        },
        computed: {
            dipId: function() {
                return this.item.storable_id;
            }
        }

    }
</script>
