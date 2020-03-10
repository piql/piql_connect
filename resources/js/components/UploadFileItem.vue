<template> 
    <div class="row plist mb-0 pb-0">
        <div class="pl-3 col-md-7 col-sm-5 col-xs-3 text-left text-truncate align-self-center">
            <div v-if="isUploading" class="progress upload-progress bg-fill">
                <div class="progress-bar bg-brand text-left" role="progressbar" v-bind:style="progressBarStyle" v-bind:aria-valuenow="progressPercentage" aria-valuemin="0" aria-valuemax="100">
                    <span class="upload-text pl-2">{{file.filename}}</span>
                </div>
            </div>
            <div v-else>
              <div class="pl-2 text-left">
                {{file.filename}}
              </div>
            </div>
        </div>
        <div class="col-xs-2 col-sm-2 text-center text-truncate align-self-center">
            {{humanReadableFileSize}}
        </div>
        <div class="col-xs-2 col-sm-3 text-center align-self-center">
            <span v-if="! file.isFailed">
                <span v-if="! file.isUploading">
                    <a @click="metadataClicked (file)" href="#" data-toggle="tooltip" title="Edit metadata"><i class="fas fa-tags actionIcon text-center mr-2"></i></a>
                    <a @click="removeClicked (file)" href="#" data-toggle="tooltip" title="Remove file"><i class="fas fa-trash-alt actionIcon text-center ml-2"></i></a>
                </span>
            </span>
            <span v-else>
                <a @click="retryClicked (file)" href="#" data-toggle="tooltip" title="Retry upload"><i class="fas fa-redo-alt actionIcon text-center mr-2"></i></a>
                <a @click="removeFailedClicked (file)" href="#" data-toggle="tooltip" title="Remove file"><i class="fas fa-trash-alt actionIcon text-center ml-2"></i></a>
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
            retryClicked: function( file ) {
                this.$emit("retryClicked", file );
            },
            removeFailedClicked: function( file ) {
                this.$emit("removeFailedClicked", file );
            }
        },
        computed: {
            humanReadableFileSize(){
                return this.isUploading ? filesize(this.file.uploadedFileSize) + " / " + filesize(this.file.fileSize)
                    : filesize(this.file.fileSize);
            },
            progressBarStyle() {
                return this.file.progressBarStyle;
            },
            progressPercentage() {
                return this.file.progressPercentage;
            },
            isUploading() {
                return this.file.isUploading;
            },
        }

    }
</script>
