<template>
    <div class="w-100">

        <page-heading icon="fa-file-export" :title="$t('access.retrieve.readyToRetrieve')"
            :ingress="$t('access.ready.ingress')" />
            <breadcumb/>

        <div class="row plistHeader" v-show="hasFiles">
            <div class="col-sm-6">{{$t('access.ready.aipName')}}</div>
            <div class="col-sm-2 text-center">{{$t('access.ready.ingestDate')}}</div>
            <div class="col-sm-2 text-right">{{$t('access.ready.filesize')}}</div>
            <div class="col-sm-1"></div>
            <div class="col-sm-1 text-center">{{$t('access.ready.remove')}}</div>
        </div>

        <ReadyToRetrieveFile v-for="file in files" v-bind:item="file" v-bind:key="file.id" @removeFromRetrieval="removeFromRetrieval"/>

        <div class="row mt-5" v-show="hasFiles">
            <div class="col-sm text-center">
                <button @click="requestRetrieval" class="btn btn-lg w-50">Request Retrieval</button>
            </div>
        </div>

        <div v-if="noFiles" class="mt-5"><h3>{{$t('access.ready.noItems')}}</h3></div>

    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            currentRetrieval: {},
            allFiles: []
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
            return this.hasFiles ? this.allFiles.slice(0,6) : [];
        },
    },
    methods: {
        updateFiles: async function() {
            this.currentRetrieval = (await axios.get("/api/v1/storage/retrievals/latest")).data.data;
            let id = this.currentRetrieval.id;
            this.allFiles = (await axios.get("/api/v1/storage/retrievals/"+id+"/files")).data.data;
        },
        requestRetrieval: async function() {
            this.currentRetrieval = (await axios.post("/api/v1/storage/retrievals/"+this.currentRetrieval.id+"/close")).data.data;
            window.location.href="/access/retrieve/retrieving";
        },
        removeFromRetrieval: async function() {
        }
    },
    created() {
    },
    beforeDestroy(){
    },

    async mounted() {
        this.allFiles = [{}];
        this.updateFiles();
    },
}
</script>
