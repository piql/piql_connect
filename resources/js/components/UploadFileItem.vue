<template> 
    <div class="row plist mb-0 pb-0">
        <div class="pl-5 col-md-7 col-sm-5 col-xs-3 text-left text-truncate align-self-center">
            <div v-if="isUploading" class="progress">
                <div class="progress-bar bg-signal text-left" role="progressbar" v-bind:style="progressBarStyle" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                    {{file.filename}}
                </div>
            </div>
            <div v-else>
              <div class="text-left">
                {{file.filename}}
              </div>
            </div>
        </div>
        <div class="col-xs-2 col-sm-2 text-center text-truncate align-self-center">
            {{humanReadableFileSize}}
        </div>
        <div class="col-xs-2 col-sm-3 text-center align-self-center">
            <span v-if="! file.isUploading">
                <a @click="metadataClicked (file)" href="#" data-toggle="tooltip" title="Edit metadata"><i class="fas fa-tags actionIcon text-center mr-2"></i></a>
                <a @click="removeClicked (file)" href="#" data-toggle="tooltip" title="Remove file"><i class="fas fa-trash-alt actionIcon text-center ml-2"></i></a>
            </span>
        </div>
    </div>
</template>

<script>
    import moment from 'moment';
    import axios from 'axios';
    import filesize from 'filesize';
    export default {
        async mounted() {
        },
        props: {
            file: Object,
        },

        data() {
            return {
           };
        },
        methods: {
            dateFormat: function(item){
                return moment(item.created_at).format('L');
            },
            removeClicked: function( file ) {
              this.$emit("removeClicked", file );
            },
          metadataClicked: function (file ) {
              this.$emit("metadataClicked", file );
            },
        },
        computed: {
            humanReadableFileSize(){
                return this.isUploading ? filesize(this.file.uploadedFileSize) + " / " + filesize(this.file.fileSize)
                    : filesize(this.file.fileSize);
            },
            progressBarStyle() {
                return this.file.progressBarStyle;
            },
            isUploading() {
                return this.file.isUploading;
            },
        }

    }
</script>
