<template>
    <div class="w-100">

        <page-heading icon="fa-list-ul" :title="$t('ingest.offlineStorage.contentOptions.header')" :ingress="$t('ingest.offlineStorage.contentOptions.ingress')" />

        <div class="row plistHeader">
            <div class="col-sm-4">{{$t('ingest.offlineStorage.jobName')}}</div>
            <div class="col-sm-2 pr-3">{{$t('ingest.offlineStorage.numberOfAips')}}</div>
            <div class="col-sm-2 pr-3">{{$t('ingest.offlineStorage.size')}}</div>
            <div class="col-sm-4">{{$t("ingest.offlineStorage.actions")}}</div>
        </div>

        <Task v-if="item != null" :item="item" :jobListUrl="jobListUrl" :actionIcons="actionIcons" @piqlIt="piqlIt"/>

        <div class="row mt-4">
            <div class="col">
                <label class="align-top" for="outputMatching">{{$t('ingest.offlineStorage.contentOptions.outputMatching.label')}}</label>
                <textarea class="contentOptionsTextinput" id="outputMatching"></textarea>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col">
                <label class="align-top" for="layout">{{$t('ingest.offlineStorage.contentOptions.layout.label')}}</label>
                <textarea class="contentOptionsTextinput" id="layout"></textarea>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col">
                <label class="align-top" for="reelDef">{{$t('ingest.offlineStorage.contentOptions.reelDefinition.label')}}</label>
                <textarea class="contentOptionsTextinput" id="reelDef"></textarea>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col">
                <span class="contentOptionsUploadTag">{{$t('ingest.offlineStorage.contentOptions.clientLogo.title')}}</span>
                <input type="file" name="file-1[]" id="file-1" class="inputfile inputfile-1" data-multiple-caption="{count} files selected" multiple="">
                <label for="file-1">
                    <button title="Choose file" id="chooseFileButton1" class="btn form-control-btn w-70">{{$t('ingest.offlineStorage.contentOptions.clientLogo.buttonText')}}</button>
                </label>
            </div>
        </div>

        <div class="row mt-1">
            <div class="col">
                <span class="contentOptionsUploadTag">{{$t('ingest.offlineStorage.contentOptions.reelDescription.title')}}</span>
                <input type="file" name="file-1[]" id="file-1" class="inputfile inputfile-1" data-multiple-caption="{count} files selected" multiple="">
                <label for="file-1">
                    <button title="Choose file" id="chooseFileButton2" class="btn form-control-btn w-70">{{$t('ingest.offlineStorage.contentOptions.reelDescription.buttonText')}}</button>
                </label>
            </div>
        </div>

        <div class="row mt-3 d-flex flex-row-reverse">
            <button class="btn btn-ln btn-default pr-5 pl-5 col-2" @click="$router.go(-1)">{{$t('OK')}}</button>
            <button class="btn btn pr-5 pl-5 col-2 mr-2" @click="$router.go(-1)">{{$t('Cancel')}}</button>
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

