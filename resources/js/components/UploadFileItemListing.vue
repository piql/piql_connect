<template>
  <div>
      <table class="table table-hover">
          <thead>
              <tr>
                  <th><i class="fas fa-trash-alt actionIcon text-center ml-2 cursorPointer" @click="batchRemove"></i> {{$t('upload.fileName')}}</th>
                  <th>{{$t('upload.fileSize')}}</th>
                  <th>{{$t('upload.fileActions')}}</th>
              </tr>
          </thead>
          <tbody>
               <tr v-for="(file,idx) in displayedfiles" :key="file.id">
                        <td>
                            <div v-if="file.isUploading" class="progress upload-progress bg-fill">
                                <div class="progress-bar bg-brand text-left" role="progressbar" v-bind:style="file.progressBarStyle" v-bind:aria-valuenow="file.progressPercentage" aria-valuemin="0" aria-valuemax="100">
                                    <span class="upload-text">{{file.filename}}</span>
                                </div>
                            </div>
                            <div v-else>
                                <span class="d-inline" tabindex="0" data-toggle="tooltip" :title="file.filename">
                                    <div class="text-left">
                                        <label><input type="checkbox" class="fileSel" :value="idx"/> {{file.filename}}</label>
                                    </div>
                                </span>
                            </div>
                    </td>
                    <td>
                        {{Math.ceil(file.fileSize/1000)}} Kb
                    </td>
                    <td>
                        <span v-if="file.isComplete">
                            <a @click="metadataClicked (file)" data-toggle="tooltip" title="Edit metadata"><i class="fas fa-tags actionIcon text-center mr-2 cursorPointer"></i></a>
                            <a @click="removeClicked (file)" data-toggle="tooltip" :title="$t('upload.remove')"><i class="fas fa-trash-alt actionIcon text-center ml-2 cursorPointer"></i></a>
                        </span>
                        <span v-if="file.isFailed">
                            <a @click="retryClicked (file)" data-toggle="tooltip" :title="$t('upload.resumeOne')"><i class="fas fa-redo-alt actionIcon text-center mr-2 cursorPointer"></i></a>
                            <a @click="removeFailedClicked (file)" data-toggle="tooltip" :title="$t('upload.remove')"><i class="fas fa-trash-alt actionIcon text-center ml-2 cursorPointer"></i></a>
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="col contentCenter">
            <Pager :meta="meta" :height="20" v-if="totalFilesUploading > 0" visiblePageSelectors="10" />
        </div>
    </div>
</template>

<script>
import Vue from 'vue';
import filesize from 'filesize';
import VuejsDialog from 'vuejs-dialog';
import 'vuejs-dialog/dist/vuejs-dialog.min.css';
Vue.use(VuejsDialog);
export default {
    props:{
        sortedFilesUploading: Array,
        filesUploadingMeta: Object
    },
    data(){
        return {
            file: null,
            perPage: 8,
            pages:[],
            page: 1
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
        batchRemove: function() {
            let fileSelArr = this.$el.querySelectorAll('.fileSel');
            let fileToRemoveArr = [];
            for (let i=0;i<fileSelArr.length;i++) {
                if (fileSelArr[i].checked) {
                    fileToRemoveArr[fileToRemoveArr.length] = this.displayedfiles[i];
                }
            }
            let options = {
                okText: this.$t('OK'),
                cancelText: this.$t('Cancel')
            };
            if (fileToRemoveArr.length > 0) {
                this.$dialog
                    .confirm(this.$t('upload.remove.batch.question', { fileCount: fileToRemoveArr.length}), options)
                    .then(remove => {
                        for (let i=0;i<fileToRemoveArr.length;i++) {
                            this.$emit("removeClicked", fileToRemoveArr[i] );
                        }
                    });
            } else {
                this.$dialog.alert(this.$t('upload.remove.batch.noFiles'), options);
            }
        },
        removeClicked: function( file ) {
            let options = {
                okText: this.$t('OK'),
                cancelText: this.$t('Cancel')
            };
            this.$dialog
                .confirm(this.$t('upload.remove.question'), options)
                .then(remove => {
                    this.$emit("removeClicked", file );
                });
        },
        metadataClicked: function (file ) {
            this.$emit("metadataClicked", file );
        },
        retryClicked: function( file ) {
            this.$emit("retryClicked", file );
        },
        removeFailedClicked: function( file ) {
            let options = {
                okText: this.$t('OK'),
                cancelText: this.$t('Cancel')
            };
            this.$dialog
                .confirm(this.$t('upload.remove') + '?', options)
                .then(remove => {
                    this.$emit("removeFailedClicked", file );
                });
        },
        humanReadableFileSize(){
            return Math.ceil(this.file.fileSize / 1000);
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
        setPages () {
            let numberOfPages = Math.ceil(this.sortedFilesUploading.length / this.perPage);
            for (let index = 1; index <= numberOfPages; index++) {
                this.pages.push(index);
            }
        },
        paginate (files) {
            let urlPage = this.$route.query.page;
            let page = urlPage >= 1 ? urlPage : 1;
            let perPage = this.perPage;
            let from = (page * perPage) - perPage;
            let to = (page * perPage);
            return  files.slice(from, to);
        }
    },
    computed: {
        displayedfiles () {
            return this.paginate(this.sortedFilesUploading);
        },
        totalFilesUploading: function() {
            return this.sortedFilesUploading.length;
        },
        meta: function() {
            return this.filesUploadingMeta;
        }
    },
    watch: {
        sortedFilesUploading () {
            this.setPages();
        }
    }
}
</script>

<style>
    a.page-link {
        display: inline-block;
    }
    a.page-link {
        font-size: 20px;
        color: #cc5d33;
        font-weight: 500;
    }
    .offset{
        width: 500px !important;
        margin: 20px auto;  
    }
    .contentCenter {
        text-align: center;
    }
    .dg-btn--ok {
        border-color: #cc5d33;
        color: #cc5d33;
    }
    .dg-btn--cancel {
        border-color: #cc5d33;
        color: #ffffff;
        background-color: #cc5d33;
    }
</style>