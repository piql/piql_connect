<template>
  <div>
      <table class="table table-hover">
          <thead>
              <tr>
                  <th>{{$t('upload.fileName')}}</th>
                  <th>{{$t('upload.fileSize')}}</th>
                  <th>{{$t('upload.fileActions')}}</th>
              </tr>
          </thead>
          <tbody>
               <tr v-for="file in displayedfiles" :key="file.id">
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
                            {{Math.ceil(file.fileSize/1000)}} Kb
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
      <div class="row text-center pagerRow">
          <div class="col">
              <nav aria-label="pages" class="d-inline-flex">
                <ul class="pagination justify-content-center">
                    <li class="page-item">
                        <a class="page-link" v-if="page != 1" @click="page--"> <i class="fas fa-angle-left"></i> </a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" v-for="pageNumber in pages.slice(page-1, page+5)" :key="pageNumber" @click="page = pageNumber"> {{pageNumber}} </a>
                    </li>
                    <li class="page-item">
                        <a @click="page++" v-if="page < pages.length" class="page-link"> <i class="fas fa-angle-right"></i> </a>
                    </li>
                </ul>
            </nav>
              
          </div>
        </div>
      
      
  </div>
</template>

<script>
import filesize from 'filesize';
export default {
    props:{
        sortedFilesUploading: Array
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
                let page = this.page;
                let perPage = this.perPage;
                let from = (page * perPage) - perPage;
                let to = (page * perPage);
                return  files.slice(from, to);
            }
        },
        computed: {
            displayedfiles () {
                return this.paginate(this.sortedFilesUploading);
            }
        },
        watch: {
            sortedFilesUploading () {
                this.setPages();
            }
        }
    

}
</script>

<style scoped>
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

    /* table tr{
    height: 60px;
    } */
</style>