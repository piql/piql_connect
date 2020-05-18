<template>
    <div>
        <div class="row plist d-flex bg-white pt-3 pb-3 align-items-center bucket-text text-center">
            <div class="col-sm-3" title="Title of your piqlFilm">
                <input value="" :placeholder="item.name" v-model="item.name" type="text" maxlength="40"
                       class="noTextTransform form-control pl-3 bucket-text fg-black" @input="setJobName" onclick="select()">
            </div>

            <div class="col-sm-1 text-center" title="Number of archival packages in this piqlFilm">
                {{item.archive_objects}}
            </div>

            <div class="col-sm-2 text-nowrap" title="Total file size of all archival packages in this piqlFilm">
                {{ fileSize }}
            </div>

            <div class="col-sm-2">
                <div v-if="fileSize !== '---'" class="progress bucket-progress bg-fill">
                    <div class="progress-bar bg-brand" role="progressbar" aria-valuenow="usage" aria-valuemin="0" aria-valuemax="100" :style="usagePercentage">
                        <div class="fg-white bucket-text justify-content-center d-flex position-absolute w-50" >{{usage}}%</div>
                    </div>
                </div>
            </div>

            <div class="col listActionItems d-flex right">
                <div v-if="actionIcons.list" class="pl-1 actionIcon" title="Show all files">
                    <i class="fas fa-list cursor-pointer hover-hand"  @click="onListClick()"></i>
                </div>
                <div v-if="actionIcons.metadata" class="pl-1 actionIcon" title="Edit metadata">
                    <i class="fas fa-tags hover-hand" style="font-size: 21px;" @click="onTagsClick()"></i>
                </div>
                <div v-if="actionIcons.config" class="pl-1 actionIcon" title="Content options">
                    <i class="fas fa-cog hover-hand" @click="onCogClick()"></i>
                </div>
                <div v-if="actionIcons.delete" class="pl-1 actionIcon" title="Delete files">
                    <i class="fas fa-trash-alt mr-2 hover-hand"></i>
                </div>
                <div v-if="actionIcons.defaultAction" class="pl-3 pr-3" title="Store on piqlFilm" >
                    <button class="btn w-100 piqlIt" v-on:click="piqlIt">&nbsp;</button>
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
                this.$router.push({ name:'ingest.offline_storage.bucket_config', params: { bucketId: this.item.id } });
            },
            onTagsClick() {
                this.$router.push({ name:'ingest.offline_storage.bucket_metadata', params: { fileId: this.item.id } });
            },
            onListClick(){
                this.$router.push({ name:'ingest.offline_storage.bucket_content', params: { bucketId: this.item.id } });
            },
            async piqlIt(e) {
                this.$emit('piqlIt', this.item );
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
                if((this.item.bucket_size === undefined) || this.item.bucket_size === 0) {
                    return 0;
                }
                let val = (100 * this.item.size / (this.item.bucket_size)).toFixed(0)
                return val > 100 ? 100 : val;
            },
            usagePercentage: function() {
                return {'width': `${this.usage}%` };
            }
        },
        data() {
            return {
            };
        },

    }
</script>
