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

        <div class="w-100 bucketConfigForm">
            <form v-on:submit.prevent>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <b><i class="far fa-image"></i> {{$t('ingest.offlineStorage.config.imageUploadTitle')}}</b>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    {{$t('ingest.offlineStorage.config.ingress')}}
                                </div>
                                <Dropzone
                                    class="dropzone is-6 has-text-centered"
                                    :multiple="true"
                                    :uploader="uploader" style="margin-right: 0px; width:99%; height:1s0vh;">
                                    <file-input multiple
                                        :uploader="uploader">
                                        <p class="dz-text"><i class="fas fa-cloud-upload-alt"></i> {{$t("upload.addFileButton")}}</p>
                                    </file-input>
                                </Dropzone>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <b><i class="fa fa-upload"></i> {{$t('ingest.offlineStorage.config.uploadedTitle')}}</b>
                            </div>
                            <div class="card-body">
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>

    </div>
</template>

<script>
    import FineUploader from 'vue-fineuploader';
    import FineUploaderTraditional from 'fine-uploader-wrappers'
    import Dropzone from 'vue-fineuploader/dropzone';
    import axios from 'axios';

    export default {
        components: {
            FineUploader,
            Dropzone
        },
        data() {
            const uploader = new FineUploaderTraditional({
                options: {
                    request: {
                        endpoint: '/api/v1/ingest/storage/offline/config/upload',
                        params: {
                            base_directory: 'completed',
                            sub_directory: null,
                            optimus_uploader_allowed_extensions: [],
                            optimus_uploader_size_limit: 0,
                            optimus_uploader_thumbnail_height: 100,
                            optimus_uploader_thumbnail_width: 100,
                        },
                        customHeaders: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    },
                    validation: {
                        allowedExtensions: ['pdf','jpg','jpeg','tiff','tif','gif','png']
                    },
                    callbacks: {
                        onError: (msg) => {
                            console.log("MSSSSSSSSSG: " + msg);
                            let options = {
                                okText: this.$t('OK')
                            };
                            this.$dialog.alert(this.$t('ingest.offlineStorage.config.alert.notAllowedExtensions'), options);
                        },
                        onComplete: complete => {
                            this.infoToast(
                                this.$t('ingest.offlineStorage.config.toast.fileUploaded.header'),
                                this.$t('ingest.offlineStorage.config.toast.fileUploaded.message')
                            );
                            setTimeout(
                                reload => {
                                    this.$router.go();
                                }, 2000
                            )
                        }
                    }
                },
            });
            return {
                uploader: uploader,
                files: [],
                result: null
            };
        },
        props: {
            actionIcons: {
                type: Object,
                default: function () { return { 'list': false, 'config': false, 'delete': false, 'defaultAction': true}; }
            },
            baseUrl: {
                type: String,
                default: "/api/v1/ingest/storage/offline/pending/buckets"
            },
            jobListUrl: {
                type: String,
                default: "/api/v1/ingest/storage/offline/pending"
            }
        },
        computed: {
            url() { return this.baseUrl + '/' + this.$route.params.bucketId; },
            success() { return this.result ? ( this.result.status === 200 ) : false; },
            item() { return this.success ? this.result.data.data : null; },
        },
        async mounted() {
            this.update();
            this.loadFiles();
        },
        methods: {
            async loadFiles() {
                await axios.get("/api/v1/ingest/storage/offline/config/showFiles").then( (result) => {
                    this.files = result.data;
                });
            },
            async piqlIt( job ) {
                let result = (await axios.patch(this.jobListUrl+"/buckets/"+job.id, {
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

<style scoped>
.bucketConfigForm {
    margin-top: 2em;
}
</style>