<template>
    <div>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>{{$t('ingest.offlineStorage.jobName')}}</th>
                    <th>{{$t('ingest.offlineStorage.numberOfAips')}}</th>
                    <th>{{$t('ingest.offlineStorage.size')}}</th>
                    <th>{{$t('ingest.offlineStorage.filled')}}</th>
                    <th width="30%">{{$t('ingest.offlineStorage.actions')}}</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in items" :key="item.id">
                    <td>
                        <input v-model="item.name" ref="bucketName"
                       type="text" class="pl-3 noTextTransform form-control"
                       :title="$t('upload.requiredName')"
                       @input="setBucketName(item)"
                       required pattern='^((?![:\\<>"/?*|]).){3,64}$'>
                    </td>
                    <td title="Number of archival packages in this piqlFilm">
                        {{item.archive_objects}}
                    </td>
                    <td title="Total file size of all archival packages in this piqlFilm">
                        {{ item.size | prettyBytes }}
                    </td>
                    <td>
                        <div v-if="item.size !== undefined" class="progress bucket-progress bg-fill">
                            <div class="progress-bar bg-brand" role="progressbar" aria-valuenow="usage" aria-valuemin="0" aria-valuemax="100" :style="usagePercentage(item)">
                                <div class="fg-white bucket-text d-flex position-absolute w-50" style="margin-left:10vh" >{{usageLabel(item)}}%</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="row">
                            <div class="col-md-6">
                                <a v-if="actionIcons.list" class="m-auto" @click.once="onListClick(item.id)" data-toggle="tooltip" title="Show all files"><i class="fas fa-list actionIcon text-center hover-hand"></i></a>
                                <a v-if="actionIcons.metadata" class="m-auto" @click.once="onTagsClick(item.id)" data-toggle="tooltip" title="Edit metadata"><i class="fas fa-tags actionIcon text-center hover-hand"></i></a>
                                <a v-if="actionIcons.config" class="m-auto" @click.once="onCogClick(item.id)" data-toggle="tooltip" title="Content options"><i class="fas fa-cog actionIcon text-center hover-hand"></i></a>
                                <a v-if="actionIcons.delete" class="m-auto" @click.once="onDelete(item)" data-toggle="tooltip" title="Delete files"><i class="fas fa-trash-alt actionIcon text-center hover-hand"></i></a>
                            </div>
                            <div class="col-md-6">
                                <span v-if="actionIcons.defaultAction">
                                    <button v-if="processDisabled(item)" disabled title="Store on piqlFilm" id="processButton" class="btn form-control-btn piqlIt">&nbsp;</button>
                                    <button v-else title="Store on piqlFilm" id="processButton" class="btn form-control-btn piqlIt"  v-on:click="piqlIt(item)">&nbsp;</button>
                                </span>
                            </div>
                        </div>
                    </td>

                </tr>
            </tbody>
        </table>

    </div>

</template>

<script>
import axios from "axios"
export default {
    props: {
            items: Array,
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
    data(){
        return{
            item: null,
            bucketName: "",
        }
    },
    methods: {
        onCogClick(id) {
            this.$router.push({ name:'ingest.offline.buckets.config', params: { bucketId: id } });
        },
        onTagsClick(id) {
            this.$router.push({ name:'ingest.offline.buckets.metadata', params: { bucketId: id } });
        },
        onListClick(id){
            this.$router.push({ name:'ingest.offline.buckets.dips', params: { bucketId: id } });
        },
        onDelete(item) {
            let name = item.name;
            this.delete(
                this.jobListUrl+"/buckets/"+item.id
            ).then( (response) => {
                this.$emit('onDelete', item );
            }).catch( (exception) => {
                    this.errorToast(
                        this.$t('ingest.offlineStorage.package.toast.delete.failed.title'),
                        this.$t('ingest.offlineStorage.package.toast.delete.failed.message'),
                        { 'FILENAME': name }
                    );
            });
        },
        async piqlIt(item) {
            this.$emit('piqlIt', item );
        },
        async setBucketName(item) {

            let currentId = item.id;
            let jobName = item.name;
            let job = null;
            await axios.patch(this.jobListUrl+"/buckets/"+currentId, {
                'name': jobName
            }).then( (result) => {
                job = result.data;
            });
            return job;
        },
        usage(item) {
            if((item.bucket_size === undefined) || item.bucket_size === 0) {
                return 0;
            }
            return 100 * item.size / item.bucket_size;
        },
        formatNumber(value) {
            let valueArr = value.toString().split('\.');
            return valueArr[0] + (valueArr[1] != undefined ? '.' + valueArr[1].substring(0,1) : '');
        },
        usageLabel(item) {
            return this.formatNumber(this.usage(item));
        },
        usagePercentage(item) {
            return {'width': `${this.usage(item)}%` };
        },
        processDisabled(item) {
            return this.invalidBucketName(item) | this.numberOfFiles(item) === 0;
        },
        invalidBucketName(item) {
            let valid = /^((?![:\\<>"/?*|]).){3,64}$/g;
            return !item.name.match(valid);
        },
        numberOfFiles(item) {
            return item.aips_count;
        },
    },
    async mounted(){

    }

}
</script>
