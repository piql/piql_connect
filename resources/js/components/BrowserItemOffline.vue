<template>
    <div>
        <div class="row thumbnailList">
            <div class="col-sm-2 text-center align-self-center">
                <img class="thumbnailImage" v-bind:src="thumbnailImage">
            </div>
            <div class="col-sm-3 text-truncate align-self-center">
                {{item.title}}
            </div>
            <div class="col-sm-1 text-truncate align-self-center">
                {{item.size_on_disk | prettyBytes}}
            </div>
            <div class="col-sm-2 p-0 text-truncate align-self-center text-center">
                {{ formatShortDate( item.archived_at ) }}
            </div>
            <div class="col-sm-1 text-truncate align-self-center text-center">
                {{item.bucket_name}}
            </div>
            <div class="col-sm-1 align-self-center text-center">
                {{item.file_count}}
            </div>
            <div class="col-sm-2 align-self-center text-center">
                <a class="ml-2 mr-2" @click="addObjectToRetrieval" :title="$t('access.retrieve.addObjectToRetrieval')" ><i class="fas fa-file-export actionIcon"></i></a>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';
    export default {
        async mounted() {
          axios.get('/api/v1/access/dips/'+this.item.dipId+'/thumbnails', { responseType: 'blob' }).then ( async (thumbnail) => {
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
								thumbnailImage: ""
            };
        },
        methods: {
            addObjectToRetrieval: function() {
               this.$emit('addObjectToRetrieval', this.item); 
            },
        },
    }
</script>

