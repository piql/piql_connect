<template>
        <div class="row plist">
            <div class="col-sm-1">
                <input type="checkbox" class="checkbox" id="browserList" v-if="false">
            </div>
            <div class="col-sm-9">
                {{item.filename}}
            </div>
            <div class="col-sm-2">
                <a v-bind:href="downloadUrl"><i class="fas fa-file-download titleIcon"></i></a>
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
        },
        computed: {
            downloadUrl: function(){
                return "/api/v1/ingest/files/"+this.item.id+"/download";

            }
        }

    }
</script>