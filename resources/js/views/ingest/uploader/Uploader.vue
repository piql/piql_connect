<template>
    <div class="w-100">
        <page-heading icon="fa-tachometer-alt" :title="$t('upload.title')" :ingress="$t('upload.ingress')" />

        <form v-on:submit.prevent>
        <div class="row form-group mt-2 mb-2">
            <div v-show="compoundModeEnabled" class="col-2 text-left" >
                <label for="bagname" class="col-form-label-sm">{{$t("upload.sipName")}}</label>
                <input id="bagName" v-model="bagName" ref="bagName"
                    type="text" class="pl-3 noTextTransform form-control"
                    :title="$t('upload.requiredName')"
                    @input="setBagName"
                    required pattern='^((?![:\\<>"/?*|]).){3,64}$'>
            </div>

            <div class="col-2" :title="$t('upload.archiveToolTip')">
                <archive-picker v-bind:label="$t('Archive')"></archive-picker>
            </div>

            <div class="col-2" :title="$t('upload.holdingToolTip')">
                <holding-picker v-bind:label="$t('Holdings')" :useWildCard="false" ></holding-picker>
            </div>

            <div v-if="hasFailedUploads" class="col-2 text-center">
                <label for="processButton" class="col-form-label-sm">&nbsp;</label>
                <button class="btn form-control-btn btn-link" @click="retryAll" data-toggle="tooltip" :title="$t('upload.resumeAll')"><i class="fas fa-redo topIcon text-center mr-2"></i></button>
            </div>
            <div v-else="hasFailedUploads" class="col-2 text-left align-middle form-group">
                <label for="fileNameFilter" class="col-form-label-sm">{{$t("upload.fileNameFilter")}}</label>
                <input class="form-control" id="fileNameFilter" v-model="fileNameFilter">
            </div>


            <div v-show="compoundModeEnabled" class="col-2">
                <label for="processButton" class="col-form-label-sm">&nbsp;</label>
                <button v-if="processDisabled" disabled title="Start the ingest process" id="processButton" class="btn form-control-btn w-100">{{$t('upload.processButton')}}</button>
                <button v-else="processDisabled" title="Start the ingest process" id="processButton" class="btn form-control-btn w-100"  v-on:click="commitBagToProcessing">{{$t('upload.processButton')}}</button>
            </div>
        </div>

        <div class="card" :title="$t('upload.addFileButtonToolTip')">
            <div class="card-body">
                <Dropzone
                    class="dropzone is-6 has-text-centered"
                    :uploader="uploader" style="margin-right: 0px;">
                        <file-input multiple
                            accept='image/*'
                            :uploader="uploader">
                            <p class="dz-text"><i class="fas fa-cloud-upload-alt"></i> {{$t("upload.addFileButton")}}</p>
                        </file-input>
                </Dropzone>
            </div>
        </div>

        <div class="row plistHeader">
            <div class="col-7 text-left">
                {{$t('upload.fileName')}}
            </div>
            <div class="col-2 text-right">
                {{$t('upload.fileSize')}}
            </div>
            <div class="col-3 text-center">
                {{$t('upload.fileActions')}}
            </div>
        </div>

        <UploadFileItem v-for="(file,index) in sortedFilesUploading" v-bind:file="file" :key="file.id"
            @metadataClicked="metadataClicked" @removeClicked="removeClicked"
            @retryClicked="retryClicked" @removeFailedClicked="removeFailedClicked"
            v-if="index >= pageFrom-1 && index <= pageTo-1 "
            class="mr-1 ml-1"/>
        <div class="row plist invisible" v-for="pad in pagerPad"></div>
        <div class="row text-center pagerRow">
            <div class="col">
                <Pager :meta="filesUploadingMeta" :height="height" v-if="totalFilesUploading > 0" />
            </div>
        </div>
    </form>
    </div>
</template>

<script>
import RouterTools from '@mixins/RouterTools.js';
import DeferUpdate from '@mixins/DeferUpdate.js';
import FineUploader from 'vue-fineuploader';
import FineUploaderTraditional from 'fine-uploader-wrappers'
import Dropzone from 'vue-fineuploader/dropzone';
import axios from 'axios';
import JQuery from 'jquery';
let $ = JQuery;
import filesize from 'filesize';

export default {
    mixins: [ RouterTools, DeferUpdate ],
    data() {
        const uploader = new FineUploaderTraditional({
            options: {
                request: {
                    endpoint: '/api/v1/ingest/upload',
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
                deleteFile: {
                    enabled: true,
                    endpoint: '/api/v1/ingest/file',
                    customHeaders: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                },
                chunking: {
                    enabled: true,
                    partSize: 1024*768,
                    mandatory: true,
                    concurrent: {
                        enabled: false
                    },
                },
                resume: {
                    enabled: true
                },
                retry: {
                    enableAuto: false, /* this didn't work very well, so we have our own logic for it */
                },
                callbacks: {
                    onValidate: (id, name) => {
                    },
                    onSubmit: (id, name) => {
                        let isDuplicate = this.filesUploading.findIndex( (file) => file.filename == name ) != -1;
                        if( isDuplicate ){
                            this.errorToast(
                                this.$t('upload.toasts.uploadDuplicate.title'),
                                this.$t('upload.toasts.uploadDuplicate.message'),
                                { 'FILENAME': name },
                                0
                            );
                            cancel(id);
                        }

                        this.filesUploading.unshift({
                            'id': id,
                            'filename': name,
                            'progressBarStyle': "width: 0%",
                            'progressPercentage': 0,
                            'uploadedFileId': '',
                            'fileSize': 0,
                            'uploadedFileSize': 0,
                            'isUploading': false,
                            'isFailed': false,
                            'isComplete': false,
                            'retryCount' : 0,
                            'isHidden': false
                        });
                    },
                    onProgress: (id, name, uploadedBytes, totalBytes) => {
                        let progress = Math.round( uploadedBytes * ( 100.0 / totalBytes ) );
                        let filesIndex = this.filesUploading.findIndex( (file) => file.id == id );
                        this.filesUploading[filesIndex].isUploading = true;
                        this.filesUploading[filesIndex].fileSize = totalBytes;
                        this.filesUploading[filesIndex].uploadedFileSize = uploadedBytes;
                        this.filesUploading[filesIndex].progressBarStyle = {'width': `${progress}%` };
                        this.filesUploading[filesIndex].progressPercentage = progress;
                        this.refreshSession(); //Needed if paginate-navigated away from the first page
                    },
                    onComplete: async (id, name, response, xhr, something) => {
                        if( response.success == false ){
                            return;
                        }
                        let filesIndex = this.filesUploading.findIndex( (file) => file.id == id );
                        if( filesIndex === null || !this.filesUploading[filesIndex] || this.filesUploading[filesIndex].isFailed ) {
                            return;
                        }
                        this.filesUploading[filesIndex].isUploading = false;
                        this.filesUploading[filesIndex].isComplete = true;
                        let fileSize = this.uploader.methods.getSize(id);
                        this.filesUploading[filesIndex].fileSize = fileSize;
                        this.infoToast(
                            this.$t('upload.toasts.uploadComplete.title'),
                            this.$t('upload.toasts.uploadComplete.message'),
                            {'FILENAME': name }
                        );

                        if( this.compoundModeEnabled ) {
                            let uploadToBagId = this.bag.id;
                            axios.post('/api/v1/ingest/fileUploaded', {
                                'fileName' : name,
                                'result' : response,
                                'bagId' : uploadToBagId,
                                'fileSize': fileSize
                            }).then( async ( file ) => {
                                this.filesUploading[filesIndex].uploadedFileId = file.data.data.id;
                                this.filesUploading[filesIndex].uploadedToBagId = file.data.data.bag_id;

                                if( this.bag.id == uploadToBagId ){ //???
                                    await axios.get("/api/v1/ingest/bags/"+uploadToBagId+"/files").then( (files) => {
                                        this.files = files.data.data;
                                    });
                                }
                            });
                        } else {
                            axios.post("/api/v1/ingest/files/bag", {
                                'fileName' : name,
                                'result' : response,
                                'fileSize': fileSize
                            });
                        }

                    },
                    onError: async (id, name, errorReason, xhr ) => {
                        let filesIndex = this.filesUploading.findIndex( (file) => file.id == id );
                        if( filesIndex === null) {
                            return;
                        }

                        this.filesUploading[filesIndex].retryCount++;
                        if( this.filesUploading[filesIndex].retryCount < this.maxAutoRetries ){
                            this.filesUploading[filesIndex].isUploading = true;
                            this.filesUploading[filesIndex].isFailed = false;
                            let fileId = id;
                            setTimeout( ()  => {
                                this.uploader.methods.retry(fileId);
                            }, this.sleepRetryGracetimeMs );
                        }
                        else {
                            this.filesUploading[filesIndex].retryCount = 0;
                            this.filesUploading[filesIndex].isUploading = false;
                            this.filesUploading[filesIndex].isFailed = true;
                            this.errorToast(
                                this.$t('upload.toasts.uploadFailed.title'),
                                this.$t('upload.toasts.uploadFailed.message'),
                                { 'FILENAME': name }
                            );
                        }
                    }
                }
            }
        });
        return {
            uploader: uploader,
            bag: {},
            bagName: "",
            files: {},
            filesUploading: [],
            userId: '',
            userSettings: {
                workflow: {
                }
            },
            fileInputDisabled: false,
            singleArchiveTitle: "",
            selectedArchive: "",
            selectedHolding: "",
            currentPage: 1,
            pageSize: 4,
            pageFrom: 1,
            pageTo: 4,
            fileNameFilter: ""
        };
    },

    components: {
        FineUploader,
        Dropzone
    },

    computed: {
        sortedFilesUploading() {
            return this.filesUploading
                .filter( f => !f.isHidden )
                .sort( (a,b)  => Number( b.isFailed ) - Number( a.isFailed ) )
                .sort( (a,b)  => Number( b.isUploading) - Number( a.isUploading ) );
        },
        processDisabled: function() {
            return this.invalidBagName | this.numberOfFiles === 0 | this.hasIncompleteFiles;
        },
        invalidBagName: function() {
            let valid = /^((?![:\\<>"/?*|]).){3,64}$/g;
            return !this.bagName.match(valid);
        },
        numberOfFiles: function() {
            return this.files.length;
        },
        hasIncompleteFiles() {
            return this.filesUploading.some( f => !f.isComplete );
        },
        hasFailedUploads() {
            return this.filesUploading.some( f => f.isFailed );
        },
        compoundModeEnabled: function() {
            let compoundSetting = this.userSettings.workflow.ingestCompoundModeEnabled;
            /* Compound must explicitly be set to false to be disabled */
            return this.userSettings.workflow.ingestCompoundModeEnabled !== undefined ? this.userSettings.workflow.ingestCompoundModeEnabled : true;
        },
        uploadInProgress: function() {
            return this.filesUploading.find( (file) => file.isUploading === true ) || false;
        },
        totalFilesUploading: function() {
            return this.filesUploading.length;
        },
        totalFilesSorted: function() {
            return this.sortedFilesUploading.length;
        },
        pageLast: function() {
            let pageLast = Math.ceil( this.totalFilesSorted / this.pageSize );
            return pageLast;
        },
        pagePrev: function() {
            return this.currentPage < 2 ? null : this.currentPage - 1;
        },
        pageNext: function() {
            return this.currentPage == this.pageLast ? null : this.currentPage + 1;
        },
        pagerPad: function() {
            let entriesOnLastPage = this.totalFilesUploading % this.pageSize;
            let padEntries = entriesOnLastPage > 0 ? ( this.pageSize - entriesOnLastPage ) : 0;
            return this.currentPage != this.pageLast ? 0 : padEntries;
        },
        filesUploadingMeta: function() {
            return {
                'current_page': this.currentPage,
                'prev': this.pagePrev,
                'next': this.pageNext,
                'last_page': this.pageLast,
                'from': this.pageFrom,
                'to': this.pageTo,
                'total': this.totalFilesSorted
            }
        },
        customerSelectsHoldings: function() {
            return !!this.holdings;
        },
        customerSelectsArchives: function() {
            return !!this.archives;
        },
    },

    watch: {
        '$route': 'dispatchRouting',
        fileNameFilter: function(filter) {
            this.updateQueryParams({ page: null });
            let pattern = '('+filter.split(' ').join('|').concat(')'); /* Simple fuzzy matching */
            let matcher = new RegExp(pattern, "i");
            this.filesUploading = this.filesUploading.map( (file) => {
                file.isHidden = !matcher.test( file.filename )
                return file;
            } );
        }
    },

    methods: {
        metadataClicked( e ) {
            let fileId = e.uploadedFileId;
            let bagId = e.uploadedToBagId;
            if( fileId && bagId ){
                this.$router.push({ name:'ingest.uploader.metadata', params: { bagId: bagId, fileId: fileId } });
            }
        },
        async removeClicked( e ) {
            let fileId = e.uploadedFileId;
            let bagId = e.uploadedToBagId;
            axios.delete("/api/v1/ingest/bags/"+bagId+"/files/"+fileId).then ( async (response) => {
                this.files = (await axios.get('/api/v1/ingest/bags/' + this.bag.id + '/files')).data.data;
                this.filesUploading = this.filesUploading.filter( (file) => file.uploadedFileId !== fileId );
            });
        },
        async removeFailedClicked( e ) {
            let fileId = e.id;
            this.uploader.methods.deleteFile( fileId );
            this.filesUploading = this.filesUploading.filter( (file) => file.id !== fileId );
        },
        retryClicked( e ) {
            let fileId = e.id;
            let filesIndex = this.filesUploading.findIndex( (file) => file.id == fileId );
            this.filesUploading[filesIndex].isFailed = false;
            this.filesUploading[filesIndex].isUploading = true;
            this.uploader.methods.retry( fileId );
        },
        retryAll( e ) {
            this.filesUploading
                .filter( (file) => file.isFailed == true )
                .map( (f) => {
                    let filesIndex = this.filesUploading.findIndex( (file) => file.id == f.id );
                    this.filesUploading[filesIndex].isFailed = false;
                    this.filesUploading[filesIndex].isUploading = true;
                    this.uploader.methods.retry( f.id );
                });
        },
        addFileToQueue(payload) {
        },
        humanReadableFileSize( fileSizeInBytes ){
            return isNaN( fileSizeInBytes )  ? "" : filesize( fileSizeInBytes );
        },
        dispatchRouting() {
            let query = this.$route.query;
            let queryArchive = query.archive;
            let queryHolding = query.holding;
            if( queryArchive != this.selectedArchive || queryHolding != this.selectedHolding  ) {
                this.changedArchive( queryArchive, queryHolding );
            }
            let prevCurrentPage = this.currentPage;
            this.currentPage = parseInt(query.page ?? "1");
            this.pageFrom = 1 + (this.currentPage-1) * this.pageSize;
            this.pageTo = this.pageFrom+this.pageSize-1;
        },
        async commitBagToProcessing(e) {
            if(this.processDisabled){
                return;
            }
            this.fileInputDisabled = true;

            let bagName = this.bagName;
            await this.doProcessing( this.bag.id );

            this.infoToast(
                this.$t('upload.toasts.sentToProcessing.title'),
                this.$t('upload.toasts.sentToProcessing.message'),
                {'BAGNAME': bagName }
            );

            this.bag = await this.createBag("", this.userId, this.selectedArchive, this.selectedHoldingTitle );

            this.fileInputDisabled = false;
        },
        async doProcessing( bagId ) {
            let committed = (await axios.post("/api/v1/ingest/bags/"+bagId+"/commit")).data.data;
            this.uploader.methods.reset();
            this.bagName = "";
            this.files = [];
            this.filesUploading = [];
        },
        async setBagName() {
            if( this.invalidBagName ){
                return;
            }

            let currentBagId = this.bag.id;
            axios.patch("/api/v1/ingest/bags/"+currentBagId, {
                'name': this.bagName
            });
        },
        async createBag( bagName, userId, selectedArchive, selectedHoldingTitle ) {
            let createdBag = (await axios.post("/api/v1/ingest/bags/", {
                name: bagName,
                owner: userId,
                archive_uuid: selectedArchive,
                holding_name: selectedHoldingTitle
            })).data.data;
            this.files = [];
            this.filesUploading = [];
            return createdBag;
        },
        async changedArchive(archiveId, holdingTitle) {
            if( !archiveId || !holdingTitle )
                return;
            this.selectedArchive = archiveId;
            this.selectedHolding = holdingTitle;
            if( this.bag && this.bag.id && this.compoundModeEnabled) {
                axios.patch("/api/v1/ingest/bags/"+this.bag.id, {
                    archive_uuid: archiveId,
                    holding_name: holdingTitle
                }).then( (response) => {
                    this.bag = response.data.data;
                });
            }
        },
        activeUploads() {
            return this.filesUploading.filter( (file) => file.isUploading ).length > 0;
        }
    },
    props: {
        retryGracetimeMs: {
            type: Number,
            default: 1000
        },
        maxAutoRetries: {
            type: Number,
            default: 5
        },
        height: {
            type: Number,
            default: 0
        }
    },
    async mounted() {
        let queryPage = parseInt(this.$route.query.page ?? "1");
        this.currentPage = queryPage;
        this.pageFrom = 1;
        this.pageTo = this.pageSize;
        this.dispatchRouting();
        this.userId = (await axios.get("/api/v1/system/currentUser")).data;
        this.userSettings  = (await axios.get("/api/v1/system/currentUserSettings")).data;

        if( this.compoundModeEnabled ) {

            this.bag = (await axios.get("/api/v1/ingest/bags/latest")).data.data;
            this.bagName = this.bag.name;
            let archive = this.bag.archive_uuid;
            let holding = this.bag.holding_name;
            this.updateQueryParams({ archive, holding });

            if(!!this.bag & this.bag.status === "open") {
                this.files = (await axios.get('/api/v1/ingest/bags/' + this.bag.id + '/files')).data.data;
                this.filesUploading = this.files.map( (file, index) => ({
                    id: index+100000,  /*A large enough number to avoid collisions with id's provided by FineUploader */
                    filename: file.filename,
                    progressBarStyle: "width: 0%",
                    progressPercentage: "0",
                    uploadedFileId: file.id,
                    uploadedToBagId: file.bag_id,
                    fileSize: file.filesize,
                    uploadedFileSize: file.filesize,
                    isUploading: false,
                    isComplete: true
                }) );
            }
            else {
                this.bag = (await this.createBag( "", this.userId, this.selectedArchive, this.selectedHoldingTitle ));
            }
        }
    },

    async beforeRouteLeave (to, from, next) {
        if (this.activeUploads()) {
            alert(this.$t('upload.interruptDialogTitle'));
            next(false);
            return;
        }
        next();
    }
}

</script>
