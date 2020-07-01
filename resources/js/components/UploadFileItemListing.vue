<template>
  <div>
      <table class="table table-sm table-hover table-striped table-bordered">
          <thead>
              <tr>
                  <th>{{$t('upload.fileName')}}</th>
                  <th>{{$t('upload.fileSize')}}</th>
                  <th>{{$t('upload.fileActions')}}</th>
              </tr>
          </thead>
          <tbody>
              <tr v-for="file in sortedFilesUploading" :key="file.id">
                <td>
                    <div v-if="file.isUploading" class="progress upload-progress bg-fill">
                        <div class="progress-bar bg-brand text-left" role="progressbar" v-bind:style="file.progressBarStyle" v-bind:aria-valuenow="file.progressPercentage" aria-valuemin="0" aria-valuemax="100">
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
                </td>
                <td>
                    {{file.humanReadableFileSize}}
                </td>
                <td>
                    <span v-if="file.isComplete">
                        <a @click="metadataClicked (file)" data-toggle="tooltip" title="Edit metadata"><i class="fas fa-tags actionIcon text-center mr-2"></i></a>
                        <a @click="removeClicked (file)" data-toggle="tooltip" :title="$t('upload.remove')"><i class="fas fa-trash-alt actionIcon text-center ml-2"></i></a>
                    </span>
                    <span v-if="file.isFailed">
                        <a @click="retryClicked (file)" data-toggle="tooltip" :title="$t('upload.resumeOne')"><i class="fas fa-redo-alt actionIcon text-center mr-2"></i></a>
                        <a @click="removeFailedClicked (file)" data-toggle="tooltip" :title="$t('upload.remove')"><i class="fas fa-trash-alt actionIcon text-center ml-2"></i></a>
                    </span>
                </td>

              </tr>

          </tbody>
      </table>
  </div>
</template>

<script>
import filesize from 'filesize';
export default {
    props:{
        sortedFilesUploading: Object,
        pageFrom: Number,
        pageTo: Number

    },
    data(){
        return {
            file: null,

        }

    },
    async mounted(){

        this.sortedFilesUploading.forEach(file => {
            this.file = file;
            
            file.humanSize = this.humanReadableFileSize;
            file.progressBarStyle = this.progressBarStyle;
            file.progressPercentage = this.progressPercentage;
            file.isUploading = this.isUploading;
            
        });

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
            },
            humanReadableFileSize(){
                return this.isUploading ? filesize(parseInt(this.file.uploadedFileSize), {round: 0}) + " / " + filesize(parseInt(this.file.fileSize), {round: 0})
                    : filesize(parseInt(this.file.fileSize), {round: 0});
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
        },
        computed: {
            
        }
    

}
</script>

<style>

</style>