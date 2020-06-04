<template>
    <div>
        <div class="row plist thumbnailList">
            <!--
            <div class="col-sm-3" title="Title of your piqlFilm">
                <input value="" :placeholder="item.name" v-model="item.name" type="text" maxlength="40"
                       class="noTextTransform form-control pl-3 bucket-text fg-black" @input="setJobName" onclick="select()">
            </div>
            -->
            <div class="col-3 text-left" >
                <input v-model="item.name" ref="bucketName"
                       type="text" class="pl-3 noTextTransform form-control"
                       :title="$t('upload.requiredName')"
                       @input="setBucketName"
                       required pattern='^((?![:\\<>"/?*|]).){3,64}$'>
            </div>

            <div class="col-sm-1 text-right" title="Number of archival packages in this piqlFilm">
                {{item.archive_objects}}
            </div>

            <div class="col-sm-2 text-nowrap text-right" title="Total file size of all archival packages in this piqlFilm">
                {{ fileSize }}
            </div>

            <div class="col-sm">
                <div v-if="fileSize !== '---'" class="progress bucket-progress bg-fill">
                    <div class="progress-bar bg-brand" role="progressbar" aria-valuenow="usage" aria-valuemin="0" aria-valuemax="100" :style="usagePercentage">
                        <div class="fg-white bucket-text justify-content-center d-flex position-absolute w-50" >{{usage}}%</div>
                    </div>
                </div>
            </div>

            <div class="col-2 d-inline text-right">
                <a v-if="actionIcons.list" class="m-auto" @click.once="onListClick" data-toggle="tooltip" title="Show all files"><i class="fas fa-list actionIcon text-center hover-hand"></i></a>
                <a v-if="actionIcons.metadata" class="m-auto" @click.once="onTagsClick" data-toggle="tooltip" title="Edit metadata"><i class="fas fa-tags actionIcon text-center hover-hand"></i></a>
                <a v-if="actionIcons.config" class="m-auto" @click.once="onCogClick" data-toggle="tooltip" title="Content options"><i class="fas fa-cog actionIcon text-center hover-hand"></i></a>
                <a v-if="actionIcons.delete" class="m-auto" @click.once="onDelete" data-toggle="tooltip" title="Delete files"><i class="fas fa-trash-alt actionIcon text-center hover-hand"></i></a>
            </div>
            <div class="col-2 d-inline text-left">
                <span v-if="actionIcons.defaultAction">
                    <button v-if="processDisabled" disabled title="Store on piqlFilm" id="processButton" class="btn form-control-btn piqlIt w-10">&nbsp;</button>
                    <button v-else="processDisabled" title="Store on piqlFilm" id="processButton" class="btn form-control-btn piqlIt w-10"  v-on:click="piqlIt">&nbsp;</button>
                </span>
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
                this.$router.push({ name:'ingest.offline.buckets.config', params: { bucketId: this.item.id } });
            },
            onTagsClick() {
                this.$router.push({ name:'ingest.offline.buckets.metadata', params: { bucketId: this.item.id } });
            },
            onListClick(){
                this.$router.push({ name:'ingest.offline.buckets.dips', params: { bucketId: this.item.id } });
            },
            onDelete() {
                let name = this.item.name;
                this.delete(
                    this.jobListUrl+"/jobs/"+this.item.id
                ).then( (response) => {
                    this.$emit('onDelete', this.item );
                }).catch( (exception) => {
                        this.errorToast(
                            this.$t('ingest.offlineStorage.package.toast.delete.failed.title'),
                            this.$t('ingest.offlineStorage.package.toast.delete.failed.message'),
                            { 'FILENAME': name }
                        );
                });
            },
            async piqlIt(e) {
                this.$emit('piqlIt', this.item );
            },
            async setBucketName() {
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
            },
            processDisabled: function() {
                return this.invalidBucketName | this.numberOfFiles === 0;
            },
            invalidBucketName: function() {
                let valid = /^((?![:\\<>"/?*|]).){3,64}$/g;
                return !this.item.name.match(valid);
            },
            numberOfFiles: function() {
                return this.item.aips_count;
            },
        },
        data() {
            return {
                bucketName: "",
            };
        },

    }
</script>
