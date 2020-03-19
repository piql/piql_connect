<template>
    <div class="mb-2 mt-2">
        <div class="row">
            <div class="col-sm-1 text-left">
                <i class="fas fa-upload mr-3 titleIcon"></i>
            </div>
            <div class="col-sm-6 text-left">
                <h1>{{$t("upload.header")}}</h1>
            </div>
        </div>
        <div class="row mt-0 pt-0">
            <div class="col-sm-1"></div>
            <div class="col-sm-6 text-left ingressText">
                {{$t("upload.ingress")}}
            </div>
        </div>

        <div class="row form-group mt-2 mb-2">
            <div class="col-md-2 pl-0 ml-0" :title="$t('upload.addFileButtonToolTip')">
                <label for="uploadbutton" class="col-form-label-sm">&nbsp;</label>
                <FileInputComponent
                    id="uploadbutton"
                    :multiple="compoundModeEnabled"
                    :uploader="uploader"
                    :disabled="fileInputDisabled">
                    <slot name="inputbuttontext" ><span class="pl-3 pr-3"><i class="fas fa-plus mr-3"></i>{{$t("upload.addFileButton")}}</span></slot>
                </FileInputComponent>
            </div>

            <div v-show="compoundModeEnabled" class="col-md-2 text-left" >
                <label for="bagname" class="col-form-label-sm">{{$t("upload.sipName")}}</label>
                <input id="bagName" v-model="bagName" ref="bagName"
                    type="text" class="pl-3 noTextTransform form-control"
                    :title="$t('upload.requiredName')"
                    style="border-radius: 0.5rem" @input="setBagName" onclick="select()"
                    required="true" pattern='^((?![:\\<>"/?*|]).){3,}$'>
            </div>

            <div v-if="customerSelectsArchives" class="col-md-2" :title="$t('upload.archiveToolTip')">
                <archive-picker v-bind:label="$t('Archive')" :archives="archives" :initialSelection="selectedArchive" @selectionChanged="changedArchive"></archive-picker>
            </div>
            <div v-else="customerSelectsArchives" title="The archive you ingest to" class="col-md-2">
                <label class="col-form-label-sm">{{$t('Archive')}}</label>
                <div class="pl-0 pr-0 form-control align-middle text-center">{{singleArchiveTitle}}</div>
            </div>

            <div v-if="customerSelectsHoldings" class="col-md-2" :title="$t('upload.holdingToolTip')">
                <holding-picker v-bind:label="$t('Holdings')" :holdings="holdings" :initialSelection="selectedHoldingTitle" @selectionChanged="changedHolding"></holding-picker>
            </div>
            <div v-else="customerSelectsHoldings" class="col-md-2">
            </div>

            <div v-if="hasFailedUploads" class="col-md-2 pr-0">
                <label for="processButton" class="col-form-label-sm">&nbsp;</label>
                <button class="btn btn-sm btn-link" @click="retryAll" href="#" data-toggle="tooltip" :title="$t('upload.resumeAll')"><i class="fas fa-redo topIcon text-center mr-2"></i></button>
            </div>
            <div v-else="hasFailedUploads" class="col-md-2 text-left pr-0 align-middle">
                <label for="fileNameFilter" class="col-form-label-sm">{{$t("upload.fileNameFilter")}}</label>
                <input id="fileNameFilter" v-model="fileNameFilter">
            </div>


            <div v-show="compoundModeEnabled" class="col-md-2 text-center pr-0">
                <label for="processButton" class="col-form-label-sm">&nbsp;</label>
                <button v-if="processDisabled" disabled title="Start the ingest process" id="processButton" class="btn w-100 m-0 p-2">{{$t('upload.processButton')}}</button>
                <button v-else="processDisabled" title="Start the ingest process" id="processButton" class="btn w-100 m-0 p-2"  v-on:click="commitBagToProcessing">{{$t('upload.processButton')}}</button>
            </div>
        </div>

        <div class="row plistHeader"> <div class="col-sm-1 text-center">
            </div>
            <div class="col-md-6 col-sm-5 col-xs-3 text-left align-self-center">
                {{$t('upload.fileName')}}
            </div>
            <div class="col-xs-2 col-sm-2 text-center align-self-center">
                {{$t('upload.fileSize')}}
            </div>
            <div class="col-xs-2 col-sm-3 text-center align-self-center">
                {{$t('upload.fileActions')}}
            </div>
        </div>

        <UploadFileItem v-for="(file,index) in sortedFilesUploading" v-bind:file="file" :key="file.id"
            @metadataClicked="metadataClicked" @removeClicked="removeClicked"
            @retryClicked="retryClicked" @removeFailedClicked="removeFailedClicked"
            v-if="index >= pageFrom-1 && index <= pageTo-1 " />
        <div class="row thumbnailList invisible" v-for="pad in pagerPad"></div>
        <div class="row text-center pagerRow">
            <div class="col">
                <Pager :meta="filesUploadingMeta" @updatePage="updatePage" v-if="totalFilesUploading > 0" />
            </div>
        </div>
    </div>
</template>

<script>

import FineUploader from 'vue-fineuploader';
import FineUploaderTraditional from 'fine-uploader-wrappers'
import axios from 'axios';
import JQuery from 'jquery';
import moment from 'moment';
let $ = JQuery;
import selectpicker from 'bootstrap-select';
import filesize from 'filesize';

export default {
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
            archives: [],
            selectedArchive: {},
            singleArchiveTitle: "",
            holdings: [],
            selectedHoldingTitle: "",
            currentPage: 1,
            pageSize: 4,
            pageFrom: 1,
            pageTo: 4,
            fileNameFilter: ""
        };
    },

    components: {
        FineUploader
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
            let valid = /^((?![:\\<>"/?*|]).){3,}$/g;
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
            return Math.ceil( this.totalFilesSorted / this.pageSize );
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
            return this.holdings.length !== 0;
        },
        customerSelectsArchives: function() {
            return this.archives.length > 1;
        },

    },

    watch: {
        fileNameFilter: function(filter) {
            let pattern = '('+filter.split(' ').join('|').concat(')'); /* Simple fuzzy matching */
            let matcher = new RegExp(pattern, "i");
            this.filesUploading = this.filesUploading.map( (file) => {
                file.isHidden = !matcher.test( file.filename )
                return file;
            } );
        }
    },

    methods: {
        onClick(url) {
            window.location = url;
        },
        metadataClicked( e ) {
            let fileId = e.uploadedFileId;
            let bagId = e.uploadedToBagId;
            if( fileId && bagId ){
                window.location = '/ingest/tasks/'+bagId+'/metadata/'+fileId+'/edit_ingest';
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
        updatePage( page ) {
            let prevCurrentPage = this.currentPage;
            this.currentPage = page.page;
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
        async setupHoldings(archiveId, initialHolding) {
            await axios.get('/api/v1/planning/archives/'+archiveId+'/holdings').then( (response) => {
                this.holdings = response.data.data;
                if( !this.customerSelectsHoldings ) {
                    return;
                }
                let defaultHolding = this.holdings.length ? this.holdings[0].title : "";
                Vue.nextTick( () => {
                    $('#holdingPicker').selectpicker('refresh');
                });
                Vue.nextTick( () => {
                    $('#holdingPicker').selectpicker('val', initialHolding || defaultHolding);
                });

            });
        },
        async changedArchive(archiveId) {
            this.selectedArchive = archiveId;
            if( this.compoundModeEnabled) {
                await this.setupHoldings(archiveId);
                axios.patch("/api/v1/ingest/bags/"+this.bag.id, {
                    archive_uuid: archiveId,
                    holding_name: this.selectedHoldingTitle
                }).then( (response) => {
                    this.bag = response.data.data;
                });
            }
        },
        async changedHolding(holdingTitle) {
            let selectedArchive  = this.selectedArchive;
            this.selectedHoldingTitle = holdingTitle;
            if( this.compoundModeEnabled ) {
                let bag = this.bag;
                Vue.nextTick( async () => {
                    bag = await axios.patch("/api/v1/ingest/bags/"+bag.id, {
                        archive_uuid: selectedArchive,
                        holding_name: holdingTitle
                    }).data;
                });
            }
        },
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
    },
    async mounted() {
        this.currentPage = 1;
        this.pageFrom = 1;
        this.pageTo = this.pageSize;
        await axios.get("/api/v1/planning/archives").then( (response) => {
            this.archives = response.data.data;
            this.selectedArchive = this.archives[0].uuid;
            this.singleArchiveTitle = this.archives[0].title;
        });

        this.userId = (await axios.get("/api/v1/system/currentUser")).data;
        this.userSettings  = (await axios.get("/api/v1/system/currentUserSettings")).data;

        if( this.compoundModeEnabled ) {

            this.bag = (await axios.get("/api/v1/ingest/bags/latest")).data.data;
            this.bagName = this.bag.name;

            if( this.customerSelectsArchives ) {
                Vue.nextTick( () => { $('#archivePicker').selectpicker('val', this.selectedArchive);});
            }

            if(this.bag !== undefined && this.bag.status === "open") {
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
        } else if (this.customerSelectsArchive ) {
            Vue.nextTick( () => {
                $('#archivePicker').selectpicker('val', this.selectedArchive);
                this.setupHoldings( this.selectedArchive );
            });
        }
    }
}

</script>
