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
              <tr v-for="(file,index) in sortedFilesUploading" :key="file.id" v-if="index >= pageFrom-1 && index <= pageTo-1 ">
                <td>
                    <div v-if="isUploading(file)" class="progress upload-progress bg-fill">
                        <div class="progress-bar bg-brand text-left" role="progressbar" v-bind:style="progressBarStyle(file)" v-bind:aria-valuenow="progressPercentage(file)" aria-valuemin="0" aria-valuemax="100">
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
                    {{humanReadableFileSize(file)}}
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
            humanReadableFileSize(file){
                return this.isUploading ? filesize(file.uploadedFileSize, {round: 0}) + " / " + filesize(file.fileSize, {round: 0})
                    : filesize(file.fileSize, {round: 0});
            },
            progressBarStyle(file) {
                return file.progressBarStyle;
            },
            progressPercentage(file) {
                return file.progressPercentage;
            },
            isUploading(file) {
                return file.isUploading;
            },
        }
    

}
</script>

<style>

</style>