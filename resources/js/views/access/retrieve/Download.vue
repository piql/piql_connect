<template>
    <div class="w-100">

        <page-heading icon="fa-file-download" :title="$t('access.retrieve.downloadable')"
            :ingress="$t('access.retrieve.downloadable.ingress')" />

        <div class="row plistHeader" v-show="hasFiles" v-if="done === false">
            <div class="col-sm-8">{{$t('access.ready.aipName')}}</div>
            <div class="col-sm-2 text-center">{{$t('access.download.download')}}</div>
            <div class="col-sm-2 text-center">{{$t('access.download.takeOffline')}}</div>
        </div>

            <ReadyForDownloadFile v-for="file in files" v-bind:item="file" v-bind:key="file.id" @takeOffline="takeOffline"/>

        <div v-if="idle" class="mt-5 text-center"><h3>{{$t('access.retrieved.idle')}}</h3></div>
        <div v-if="done" class="mt-5 text-center"><h3>{{$t('access.retrieved.noItems')}}</h3></div>

    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            pollProcessingHandle: null,
            currentDownloads: [], 
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
          axios.get("/api/v1/storage/retrievals/ready").then( async (retr) => {
            that.started = true;
            let currentRetrievals = retr.data;
            let retr_id = currentRetrievals[0].id; 
            let bagId = currentRetrievals[0].source_files[0].bag_id;
            let bag = (await axios.get("/api/v1/ingest/bags/"+bagId)).data.data;
            bag.retr_id = retr_id;
            that.allFiles.push(bag);
          });
      },
      takeOffline: async function(id) {
        console.log("taking offline: "+id);
        (await axios.post("/api/v1/storage/retrievals/"+id+"/take-offline")).data.data;
        this.allFiles = [];
      },

      startPollProcessing () {
          this.pollProcessingHandle = setInterval(async () => {
              this.updateFiles();
            }, 2000);
        }
    },
    created() {
        this.currentFiles = [{}];
    },
    beforeDestroy(){
        clearInterval(this.pollProcessingHandle);
    },
  async mounted() {
      this.updateFiles();
    },
}
</script>
