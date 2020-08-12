<template>
    <div class="row thumbnailList">
        <div class="col-sm-2 text-center align-self-center">
            <img class="thumbnailImage" v-bind:src="thumbnailImage">
        </div>
        <div class="col-sm-3 text-truncate align-self-center">
            {{item.storage_properties.bag.name}}
        </div>
        <div class="col-sm-1 p-0 text-truncate align-self-center text-center">
            {{ formatShortDate( item.storage_properties.ingest_time ) }}
        </div>
        <div class="col-sm-2 text-truncate align-self-center text-center">
            {{item.storage_properties.holding_name}}
        </div>
        <div class="col-sm-1 align-self-center text-center">
            {{fileCount}}
        </div>
        <div class="col-sm-3 d-inline-flex align-self-center">
            <a class="ml-5 mr-2" @click="addObjectToRetrieval"><i class="fas fa-file-export actionIcon"></i></a>
            <a class="ml-4" @click="open"><i class="fas fa-folder actionIcon"></i></a>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    export default {
        async mounted() {
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
                thumbnailImage: ""
            };
        },
        methods: {
            addObjectToRetrieval: function() {
               this.$emit('addObjectToRetrieval', this.item); 
            },
            open: function() {
                this.$emit('openObject', this.item.id);
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

