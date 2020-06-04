<template>
    <div class="row thumbnailList">
        <div class="col-sm-2 text-center align-self-center">
            <img class="thumbnailImage" v-bind:src="thumbnailImage">
        </div>
        <div class="col-sm-3 text-truncate align-self-center text-left">
            {{item.storage_properties.bag.name}}
        </div>
        <div class="col-sm-1 p-0 text-truncate align-self-center text-center">
            {{formatShortDate(item.archived_at)}}
        </div>
        <div class="col-sm-2 text-truncate align-self-center text-center">
            {{item.storage_properties.holding_name}}
        </div>
        <div class="col-sm-1 align-self-center text-center">
            {{fileCount}}
        </div>
        <div class="col-sm-3 d-inline-block align-self-center text-center">
            <button class="btn-tiny m-auto" @click="openObject" data-toggle="tooltip" title="Access contents"> <i class="fas fa-folder-open actionIcon"></i> </button>
            <button class="btn-tiny m-auto" @click="preview" href="" data-toggle="tooltip" title="Preview image"><i class="fas fa-eye actionIcon"></i></button>
            <button class="btn-tiny m-auto" @click="removeAip" href="" data-toggle="tooltip" title="Remove AIP from bucket"><i class="fas fas fa-trash-alt actionIcon"></i></button>
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
            thumbnailImage: "",
        }
    },
    methods: {
        preview: function(){
            this.$emit('showPreview', this.item);
        },
        removeAip: function() {
            let name = this.item.storage_properties.bag.name;
            this.delete(
                '/api/v1/ingest/offline_storage/pending/jobs/'+this.$route.params.bucketId+"/dips/"+this.item.id
            ).then( (response) => {
                this.$emit('onDelete', this.item );
            }).catch( (exception) => {
                this.errorToast(
                    this.$t('ingest.offlineStorage.package.list.toast.delete.failed.title'),
                    this.$t('ingest.offlineStorage.package.list.toast.delete.failed.message'),
                    { 'FILENAME': name }
                );
            });
        },
        openObject: function() {
            this.$emit('openObject', this.item.id);
        }
    },
    computed: {
        fileCount: function(){
            return this.item.storage_properties.bag.fileCount;
        }
    }

}
</script>
