<template>
    <div class="w-100">

        <page-heading icon="fa-spinner" :title="$t('access.retrieve.nowRetrieving')"
            :ingress="$t('access.retrieve.nowRetrieving.ingress')" />
            <breadcumb/>

        <div class="row plistHeader" v-show="hasFiles" v-if="done === false">
            <div class="col-sm-6">{{$t('access.ready.aipName')}}</div>
            <div class="col-sm-2 text-center">{{$t('access.ready.ingestDate')}}</div>
            <div class="col-sm-2 text-right">{{$t('access.ready.filesize')}}</div>
            <div class="col-sm-2"></div>
        </div>

        <span v-if="done === false">
            <NowRetrievingFile v-for="file in files" v-bind:item="file" v-bind:key="file.id"/>
        </span>

        <div v-if="idle" class="mt-5"><h3>{{$t('access.retrieved.idle')}}</h3></div>
        <div v-if="done" class="mt-5"><h3>{{$t('access.retrieved.noItems')}}</h3></div>


    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            pollProcessingHandle: null,
            currentRetrievals: [], 
            allFiles: [],
            itemsLoaded: false,
            done: false,
            started: false, 
            firstRunComplete: false,
        }
    },

    computed: {
        hasFiles: function() {
            return this.allFiles.length > 0;
        },
        noFiles: function() {
            return !this.hasFiles;
        },
        files: function(){
            return this.hasFiles ? this.allFiles.slice(0,7) : [];
        },
        idle: function() {
            return !this.firstRunComplete && this.noFiles && this.started;
        }

    },

    methods: {
      updateFiles: async function() {
          let that = this;
          let tempFiles = [];
          return axios.get("/api/v1/storage/retrievals/retrieving").then( async (retr) => {
              if(that.started == true && retr.data.data.length == 0) {
                  that.done = true;
                  return;
              }

              that.started = true;
              this.currentRetrievals = retr.data.data;
              this.currentRetrievals.map( async (cr) => {
                  let result = (await axios.get("/api/v1/storage/retrievals/"+cr.id+"/files")).data.data;
                  result.map( (file) => {
                      tempFiles.push(file);
                  });
                  if(tempFiles.length != that.files.length){
                      that.allFiles = tempFiles;
                  }
              });
              this.firstRunComplete = true;
          });
      },

      requestRetrieval: async function() {
        this.currentRetrievals = (await axios.post("/api/v1/storage/retrievals/"+this.currentRetrieval.id+"/ready")).data.data;
      },

      startPollProcessing () {
          this.pollProcessingHandle = setInterval(async () => {
              this.updateFiles();
            }, 2000);
        }
    },
    created() {
        this.currentFiles = [{}];
        this.startPollProcessing();
    },
    beforeDestroy(){
        clearInterval(this.pollProcessingHandle);
    },
    async mounted() {
    },
}
</script>
