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
                                    {{$t('ingest.offlineStorage.config.ingress')}}:<br/>
                                    {{ allowedExt }}
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
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>{{$t('upload.fileName')}}</th>
                                            <th>{{$t('upload.fileSize')}}</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="file in uploadingFiles" v-if="file.pc < 100">
                                            <td>
                                                <div class="progress upload-progress bg-fill">
                                                    <div class="progress-bar bg-brand text-left" role="progressbar" :style="{ width: file.pc + '%' }" v-bind:aria-valuenow="file.pc" aria-valuemin="0" aria-valuemax="100">
                                                        <span class="upload-text">{{file.name}} / {{ file.pc }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr v-for="file in files" :key="file.id">
                                            <td>
                                                {{ file.name }}
                                            </td>
                                            <td>
                                                {{ file.size | prettyBytes }}
                                            </td>
                                            <td>
                                                <a @click="showPreview (file)" data-toggle="tooltip" :title="$t('access.browse.header.preview')"><i class="fas fa-eye actionIcon text-center mr-2 cursorPointer"></i></a>
                                                <a @click="remove (file)" data-toggle="tooltip" :title="$t('upload.remove')"><i class="fas fa-trash-alt actionIcon text-center ml-2 cursorPointer"></i></a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row mt-3 d-flex flex-row-reverse">
                            <button class="btn btn-ln btn-default pr-5 pl-5 col-2" @click="$router.go(-1)">{{$t('OK')}}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <Lightbox
            ref="lgbx"
            :visible="lbVisible"
            :imgs="previewImages"
            :fileNames="previewFileNames"
            :fileTypes="previewFileTypes"
            :index="index"
            :hide="hideLightBox"
        />
    </div>
</template>

<script>
    import FineUploader from 'vue-fineuploader';
    import FineUploaderTraditional from 'fine-uploader-wrappers'
    import Dropzone from 'vue-fineuploader/dropzone';
    import axios from 'axios';

    import VuejsDialog from 'vuejs-dialog';
    import Lightbox from '@components/lightbox';
    Vue.use(VuejsDialog);
    const ALLOWED_EXT = ['pdf','png','jpg','jpeg','tiff','tif','gif'];
    export default {
        components: {
            FineUploader,
            Dropzone,
            Lightbox
        },
        data() {
            return {
                uploader: {},
                files: [],
                result: null,
                lbVisible: false,
                index: 0,
                jobId: 0,
                previewFileNames: [],
                previewFileTypes: [],
                previewImages: [],
                allowedExt: null,
                uploadingFiles: []
            }
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
            let token = (await this.$auth.getTokenSilently());
            const Authorization = `Bearer ${token}`;
            this.uploader = new FineUploaderTraditional({
                options: {
                    request: {
                        endpoint: '/api/v1/ingest/storage/offline/files/upload',
                        params: {
                            base_directory: 'bucketImg',
                            sub_directory: null,
                            optimus_uploader_allowed_extensions: [],
                            optimus_uploader_size_limit: 0,
                            optimus_uploader_thumbnail_height: 100,
                            optimus_uploader_thumbnail_width: 100,
                            qqchunksize: 1024 * 768,
                        },
                        customHeaders: {
                            Authorization
                        }
                    },
                    validation: {
                        allowedExtensions: ALLOWED_EXT
                    },
                    chunking: {
                        enabled: true,
                        partSize: 1024*768,
                        mandatory: true,
                        concurrent: {
                            enabled: false
                        },
                    },
                    callbacks: {
                        onError: (msg) => {
                            let options = {
                                okText: this.$t('OK')
                            };
                            this.$dialog.alert(this.$t('ingest.offlineStorage.config.alert.notAllowedExtensions'), options);
                        },
                        onComplete: async (id, name, response, xhr) => {
                            if( response.success == false ){
                                return;
                            }
                            axios.post('/api/v1/ingest/storage/offline/' + this.$route.params.bucketId + '/config/upload', {
                                'fileName' : name,
                                'result' : response
                            }).then( async ( file ) => {
                                this.infoToast(
                                    this.$t('ingest.offlineStorage.config.toast.fileUploaded.header'),
                                    this.$t('ingest.offlineStorage.config.toast.fileUploaded.message')
                                );
                                this.loadFiles();
                            });
                        },
                        onSubmit: (id, name) => {
                            this.uploadingFiles.unshift({id: id, name: name, pc: 0});
                        },
                        onProgress: (id, name, uploadedBytes, totalBytes) => {
                            let idx = this.uploadingFiles.findIndex( (file) => file.id == id );
                            this.uploadingFiles[idx].pc = Math.round(uploadedBytes * 100 / totalBytes);
                        },
                    }
                },
            });

            this.jobId = this.$route.params.bucketId;
            this.update();
            this.loadFiles();
            this.loadAllowedExt();
        },
        methods: {
            loadAllowedExt() {
                this.allowedExt = ALLOWED_EXT.join(', ');
             },
            async loadFiles() {
                await axios.get("/api/v1/ingest/storage/offline/" + this.jobId + "/config/showFiles/").then( (result) => {
                    this.files = result.data;
                });
            },
            async piqlIt( job ) {
                let result = (await axios.patch(this.jobListUrl+"/buckets/"+job.id, {
                    'status': 'commit'
                }));
                if(result.data.status == 'transferring') {
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
            async remove(file) {
                let options = {
                    okText: this.$t('OK'),
                    cancelText: this.$t('Cancel')
                };
                this.$dialog
                    .confirm(this.$t('ingest.offlineStorage.config.removeQuestion'), options)
                    .then(remove => {
                        this.doRemove(file);
                });
            },
            async doRemove(file) {
                    await axios.post("/api/v1/ingest/storage/offline/" + this.jobId + "/config/removeFile/" + file.name).then( (result) => {
                        this.loadFiles();
                    });
            },
            async showPreview(file) {
                this.lbVisible = true;
                let image = (await axios.get("/api/v1/ingest/storage/offline/" + this.jobId + "/config/showFile/" + file.name, { responseType: 'blob' }));
                let reader = new FileReader();
                reader.onload = e => this.previewImages.push( reader.result );
                reader.readAsDataURL( image.data );
                this.previewFileNames.push( file.name );
                this.previewFileTypes.push( file.type );
            },
            hideLightBox: function( e ) {
                this.lbVisible = false;
                this.previewImages = [];
                this.previewFileNames = [];
                this.previewFileTypes = [];
            },
        },
    }

</script>

<style scoped>
.bucketConfigForm {
    margin-top: 2em;
}
</style>