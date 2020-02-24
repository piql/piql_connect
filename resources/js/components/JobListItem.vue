<template>
    <div v-if="modal">
            <div class="row plist">
                <div class="col-sm-4 mt-1 form-group" title="Title of your piqlFilm">
                  <input value="" :placeholder="item.name" v-model="item.name" type="text"
                    class="noTextTransform form-control pl-3" @input="setJobName" onclick="select()">
                </div>

                <div class="col-sm-2 pt-sm-3 text-center" title="Number of archival packages in this piqlFilm">
                    {{item.archive_objects}}
                </div>

                <div class="col-sm-2 d-flex flex-row justify-content-start" title="Total file size of all archival packages in this piqlFilm">
                    <div class="pr-3 pt-sm-3 text-right">
                        {{ fileSize }}
                    </div>
                    <div v-if="fileSize !== '---'" class="w-50 pt-sm-3">
                        <div class="progress-bar">
                            <div class="bar positive" :style="'width: ' + usage + '%'">
                                <span>{{usage}}%</span>
                            </div>
                            <div class="bar negative"  :style="'width: ' + (100-usage) + '%'">
                                <span>{{usage}}%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 listActionItems d-flex flex-row justify-content-end pt-1" >
                    <div v-if="actionIcons.list" class="actionIcon mr-3" title="Show all files">
                        <i class="fas fa-list cursor-pointer hover-hand"  @click="onListClick()"></i>
                    </div>
                    <div v-if="actionIcons.metadata" class="actionIcon mr-3" title="Edit metadata">
                        <i class="fas fa-tags hover-hand" style="font-size: 21px;" @click="onTagsClick()"></i>
                    </div>
                    <div v-if="actionIcons.config" class="actionIcon mr-3" title="Content options">
                        <i class="fas fa-cog hover-hand" @click="onCogClick()"></i>
                    </div>
                    <div v-if="actionIcons.delete" class="actionIcon mr-5" title="Delete files">
                        <i class="fas fa-trash-alt mr-2 hover-hand"></i>
                    </div>
                    <div v-if="actionIcons.defaultAction" class="pt-sm-1" title="Store on piqlFilm" >
                      <button class="btn btn-lg piqlIt w-100" v-on:click="piqlIt">&nbsp;</button>
                    </div>
                </div>

            </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    async mounted() {
    },

    props: {
        item: Object,
        jobListUrl: {
            type: String,
            default: ""
        },
        actionIcons: {
            "list": { default: false },
            "metadata": { default: false },
            "config": { default: false },
            "delete": { default: false },
            "defaultAction": { default: false },
        },
    },

    methods: {
        onCogClick() {
            window.location = "/ingest/offline_storage/"+this.item.id+"/configuration";
        },
        onTagsClick() {
            window.location = "/ingest/offline_storage/"+this.item.id+"/metadata";
        },
        onListClick(){
            window.location = '/ingest/offline_storage/'+this.item.id;
        },
        async piqlIt(e) {
            e.preventDefault(); //????

            let job = (await axios.patch(this.jobListUrl+"/jobs/"+this.item.id, {
                'status': 'ingesting'
            })).data;
            if(job.status == 'ingesting')
                this.modal = false;
        },
        async setJobName() {
            let currentId = this.item.id;
            let jobName = this.item.name;
            let job = null;
            await axios.patch(this.jobListUrl+"/jobs/"+currentId, {
                'name': jobName
            }).then( (result) => {
                job = result.data;
            });
            return job;
        },
        getFileSizeIEC(bytes) {
            let value = 0;
            let exp = 0;
            if (bytes) {
                exp = Math.floor(Math.log(bytes) / Math.log(1024));
                value = (bytes / Math.pow(1024, exp)).toFixed(2);
            }
            return value + " " + (exp ? 'KMGTPEZY'[exp - 1] + 'iB' : 'Bytes')
        },
        getFileSizeSI(bytes) {
            let value = 0;
            let exp = 0;
            if (bytes) {
                exp = Math.floor(Math.log(bytes) / Math.log(1000));
                value = (bytes / Math.pow(1000, exp)).toFixed(2);
            }
            return value + " " + (exp ? 'KMGTPEZY'[exp - 1] + 'B' : 'Bytes')
        },
    },
    computed: {
        fileSize: function() {
            if(this.item.size !== undefined)
                return this.getFileSizeSI(this.item.size);
            else
                return "---";
        },
        usage: function() {
            return (100*this.item.size/(120*1000*1000*1000)).toFixed(0);
        },
    },
    data() {
        return {
            modal: true,
        };
    },

}
</script>
