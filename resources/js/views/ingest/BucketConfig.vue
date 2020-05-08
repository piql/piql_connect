<template>
    <div class="w-100">
        <page-heading icon="fa-list-ul" :title="$t('ingest.offlineStorage.contentOptions.header')" :ingress="$t('ingest.offlineStorage.contentOptions.ingress')" />

        <div class="contentContainer">
            <div class="container-fluid">
                <div class="row plistHeader">
                    <div class="col-sm-4">{{$t('ingest.offlineStorage.jobName')}}</div>
                    <div class="col-sm-2 pr-3">{{$t('ingest.offlineStorage.numberOfAips')}}</div>
                    <div class="col-sm-2 pr-3">{{$t('ingest.offlineStorage.size')}}</div>
                    <div class="col-sm-4">{{$t("ingest.offlineStorage.actions")}}</div>
                </div>
                <Task v-if="item != null" :item="item" :jobListUrl="jobListUrl" :actionIcons="actionIcons" @piqlIt="piqlIt"/>
            </div>

            <div class="list">
                <table>
                    <tbody>
                    <tr class="contentOptionsRow">
                        <td colspan="5">
                            <label for="outputMatching">Output matching</label>
                            <textarea id="outputMatching"></textarea><br>
                            <label for="layout">Layout</label>
                            <textarea id="layout"></textarea><br>
                            <label for="reelDef">Reel definition</label>
                            <textarea id="reelDef"></textarea><br>
                            <br>
                            <span class="contentOptionsUploadTag">CLIENT LOGO START</span>
                            <input type="file" name="file-1[]" id="file-1" class="inputfile inputfile-1" data-multiple-caption="{count} files selected" multiple="">
                            <label class="filelabel" for="file-1">
                                <span>CHOOSE FILE</span></label>

                            <br>

                            <span class="contentOptionsUploadTag">CLIENT REEL DESCRIPTION START</span>
                            <input type="file" name="file-2[]" id="file-2" class="inputfile inputfile-2" data-multiple-caption="{count} files selected" multiple="">
                            <label class="filelabel" for="file-2">
                                <span>CHOOSE FILE</span></label>

                            <br>

                            <span class="contentOptionsUploadTag">JPG FORMAT</span>
                            <input type="file" name="file-3[]" id="file-3" class="inputfile inputfile-3" data-multiple-caption="{count} files selected" multiple="">
                            <label class="filelabel" for="file-3">
                                <span>CHOOSE FILE</span></label>  <br>

                            <div style="width: 100%; text-align: right;">
                                <input type="submit" class="inputSubmitCancel" value="Cancel">
                                <input type="submit" class="inputSubmitSave" value="Save">
                            </div>
                        </td>
                    </tr>
                    </tbody></table>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        data() {
            return {
                result: null
            }
        },
        props: {
            actionIcons: {
                type: Object,
                default: function () { return { 'list': false, 'config': false, 'delete': false, 'defaultAction': true}; }
            },
            baseUrl: {
                type: String,
                default: "/api/v1/ingest/offline_storage/pending/jobs"
            },
            jobListUrl: {
                type: String,
                default: "/api/v1/ingest/offline_storage/pending"
            }
        },
        computed: {
            url() { return this.baseUrl + '/' + this.$route.params.bucketId; },
            success() { return this.result ? ( this.result.status === 200 ) : false; },
            item() { return this.success ? this.result.data.data : null; },
        },
        async mounted() {
            this.update();
        },
        methods: {
            async piqlIt( job ) {
                let result = (await axios.patch(this.jobListUrl+"/jobs/"+job.id, {
                    'status': 'ingesting'
                }));
                if(result.data.status == 'ingesting') {
                    this.modal = false; //????
                }
                this.infoToast(
                    this.$t('ingest.offlineStorage.toasts.piqled.title'),
                    this.$t('ingest.offlineStorage.toasts.piqled.message'),
                    {'PACKAGENAME': job.name }
                );
                this.update();
            },
            async update() {
                this.result = await axios.get( this.url );
            },
        },
    }

</script>

