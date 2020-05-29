<template> 
    <div class="row plist mb-0 pb-0">
        <div class="col-7 text-left text-truncate align-self-center">
            <div v-if="isUploading" class="progress upload-progress bg-fill">
                <div class="progress-bar bg-brand text-left" role="progressbar" v-bind:style="progressBarStyle" v-bind:aria-valuenow="progressPercentage" aria-valuemin="0" aria-valuemax="100">
                    <span class="upload-text">{{file.filename}}</span>
                </div>
            </div>
            <div v-else>
                <span class="d-inline" tabindex="0" data-toggle="tooltip" :title="file.filename">
                    <div class="text-left">
                        {{file.filename}}
                    </div>
                </span>
            </div>
        </div>
        <div class="col-2 text-right text-truncate align-self-center">
            {{humanReadableFileSize}}
        </div>
        <div class="col-3 text-center align-self-center">
            <span v-if="file.isComplete">
                <a @click="metadataClicked (file)" data-toggle="tooltip" title="Edit metadata"><i class="fas fa-tags actionIcon text-center mr-2"></i></a>
                <a @click="removeClicked (file)" data-toggle="tooltip" :title="$t('upload.remove')"><i class="fas fa-trash-alt actionIcon text-center ml-2"></i></a>
            </span>
            <span v-if="file.isFailed">
                <a @click="retryClicked (file)" data-toggle="tooltip" :title="$t('upload.resumeOne')"><i class="fas fa-redo-alt actionIcon text-center mr-2"></i></a>
                <a @click="removeFailedClicked (file)" data-toggle="tooltip" :title="$t('upload.remove')"><i class="fas fa-trash-alt actionIcon text-center ml-2"></i></a>
            </span>
        </div>
    </div>
</template>

<script>
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
                return this.isUploading ? filesize(this.file.uploadedFileSize, {round: 0}) + " / " + filesize(this.file.fileSize, {round: 0})
                    : filesize(this.file.fileSize, {round: 0});
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
<style scoped>

</style>>
