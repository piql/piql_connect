<template>
        <div class="row plist">
            <div class="col-sm-1">
                <input type="checkbox" class="checkbox" id="browserList" v-if="false">
            </div>
            <div class="col-sm-9">
                {{item.filename}}
            </div>
            <div class="col-sm-2">
                <a href="#" @click="addFileToRetrieval"><i class="fas fa-file-export titleIcon"></i></a>
            </div>
        </div>
</template>

<script>
    import moment from 'moment';
    import axios from 'axios';
    export default {
        async mounted() {
        },
        props: {
            item: Object,
            archive: String,
            holding: String,
        },

        data() {
            return {
                fileName: "",
            };
        },
        methods: {
            dateFormat: function(item){
                return moment(item.created_at).format('L');
            },
          addFileToRetrieval: function(){
            console.log("browserfileitemoffline");
            this.$emit('addFileToRetrieval', this.item);
          }
        },
        computed: {
            downloadUrl: function(){
                return "/api/v1/ingest/files/"+this.item.id+"/download";

            }
        }

    }
</script>
