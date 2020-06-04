<template>
    <div>
        <div class="row plist thumbnailList">
            <div class="col-2 text-center">
                <img class="thumbnailImage" v-bind:src="thumbnailImage">
            </div>
            <div class="col-7 text-left align-self-center text-truncate">
                {{fileName}}
            </div>
            <div class="col-2 d-inline text-center align-self-center">
                <a class="m-auto" @click.once="showMetadata" data-toggle="tooltip" title="Edit metadata"><i class="fas fa-tags actionIcon text-center"></i></a>
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
                thumbnailImage: "",
                aipItem: Object,
            };
        },
        methods: {
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
