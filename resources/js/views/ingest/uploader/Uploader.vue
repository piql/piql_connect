<template>
    <div class="w-100">
        <page-heading icon="fa-upload" :title="$t('upload.title')" :ingress="$t('upload.ingress')" />

        <form v-on:submit.prevent>


        <div class="row">
            <div class="col-md-4">

                <div class="card" :title="$t('upload.addFileButtonToolTip')">
                    <div class="card-header">
                        <b><i class="fa fa-upload"></i> {{$t('upload.form.title')}}</b>
                    </div>
                    <div class="card-body">

                         <div class="form-group">
                             <div v-show="compoundModeEnabled" class="text-left" >
                                <label for="bagname" class="col-form-label-sm">{{$t("upload.sipName")}}</label>
                                <input id="bagName" v-model="bagName" ref="bagName"
                                    type="text" class="pl-3 noTextTransform form-control"
                                    :title="$t('upload.requiredName')"
                                    @input="setBagName"
                                    required pattern='^((?![:\\<>"/?*|]).){3,64}$'>
                            </div>
                         </div>
                         <div class="form-group">
                             <div :title="$t('upload.collectionToolTip')">
                                <collection-picker
                                :useWildCard="true"
                                :wildCardLabel='$t("nothingSelected")'
                                v-bind:label="$t('Collection')" :required="true"/>
                            </div>
                         </div>
                         <div class="form-group">
                             <div :title="$t('upload.holdingToolTip')">
                                <holding-picker v-bind:label="$t('Holdings')"
                                :wildCardLabel='$t("nothingSelected")'
                                :useWildCard="true" @selectedHolder="selectedHolder" :required="true"/>
                            </div>
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



                         <div class="form-group">
                             <div v-show="compoundModeEnabled">
                                <label for="processButton" class="col-form-label-sm">&nbsp;</label>
                                <button v-if="processDisabled" disabled title="Start the ingest process" id="processButton" class="btn form-control-btn w-100">{{$t('upload.processButton')}}</button>
                                <button v-else title="Start the ingest process" id="processButton" class="btn form-control-btn w-100"  v-on:click="commitBagToProcessing">{{$t('upload.processButton')}}</button>
                            </div>

                         </div>

                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <b><i class="fa fa-folder-open"></i>  {{$t('upload.fileListTitle')}} ({{ sortedFilesUploading.length }} {{$t('upload.fileListTitle.files')}}, {{totalSize | prettyBytes}})</b>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div v-if="hasFailedUploads" class="text-center">
                                        <label for="processButton" class="col-form-label-sm">&nbsp;</label>
                                        <button class="btn form-control-btn btn-link" @click="retryAll" data-toggle="tooltip" :title="$t('upload.resumeAll')"><i class="fas fa-redo topIcon text-center mr-2"></i></button>
                                    </div>
                                    <div v-else class="text-left align-right form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-filter"></i></span>
                                            </div>
                                            <input class="form-control" :placeholder="$t('upload.fileNameFilter')" id="fileNameFilter" v-model="fileNameFilter">
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <upload-file-item-listing :sortedFilesUploading="sortedFilesUploading"
                        @metadataClicked="metadataClicked" @removeClicked="removeClicked"
                        @retryClicked="retryClicked" @removeFailedClicked="removeFailedClicked" :filesUploadingMeta="filesUploadingMeta"/>

                    </div>
                </div>

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
import {mapActions} from 'vuex';
export default {
    mixins: [ RouterTools, DeferUpdate ],
    data() {
        const Authorization = `Bearer ${Vue.prototype.$keycloak.token}`;

        const uploader = new FineUploaderTraditional({
            options: {
                request: {
                    endpoint: '/api/v1/ingest/files/upload',
                    params: {
                        base_directory: 'completed',
                        sub_directory: null,
                        optimus_uploader_allowed_extensions: [],
                        optimus_uploader_size_limit: 0,
                        optimus_uploader_thumbnail_height: 100,
                        optimus_uploader_thumbnail_width: 100,
                    },
                    customHeaders: {
                        Authorization
                    }
                },
                deleteFile: {
                    enabled: true,
                    endpoint: '/api/v1/ingest/file',
                    customHeaders: {
                        Authorization
                    }
                },
                chunking: {
                    enabled: true,
                    partSize: 1024*1024*5,
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
                autoUpload: false,
                callbacks: {
                    onStatusChange: (id, oldStatus, newStatus) => {
                        if (newStatus == 'submitted') {
                            // Manually start upload with a delay to avoid flooding the server
                            // TODO The delay is strictly only needed when uploading many small files. Tweak for special cases.
                            let filesIndex = this.filesUploading.findIndex( (file) => file.id == id );
                            if( filesIndex == -1) {
                                console.error("Failed to find file when starting upload");
                                return;
                            }
                            let delay = this.filesUploading[filesIndex].uploadStartTime - (new Date().getTime());
                            setTimeout( ()  => {
                                // TODO: Find a better suited method than retry to start the upload
                                this.uploader.methods.retry(id);
                            }, delay );
                        }
                    },
                    onValidate: (id, name) => {
                    },
                    onSubmit: (id, name) => {
                        let isDuplicate = this.filesUploading.some( file => file.filename === name );
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
                            'isHidden': false,
                            'uploadStartTime': this.uploadStartTime()
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
                        if( filesIndex == -1 || !this.filesUploading[filesIndex] || this.filesUploading[filesIndex].isFailed ) {
                            console.error("Failed to find file on complete");
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
                            axios.post(`/api/v1/ingest/bags/${uploadToBagId}/files`, {
                                'fileName' : name,
                                'result' : response,
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
                        if( filesIndex == -1) {
                            console.error("Failed to find file on error");
                            return;
                        }

                        this.filesUploading[filesIndex].retryCount++;
                        if( this.filesUploading[filesIndex].retryCount < this.maxAutoRetries ){
                            this.filesUploading[filesIndex].isUploading = true;
                            this.filesUploading[filesIndex].isFailed = false;
                            let fileId = id;
                            setTimeout( ()  => {
                                this.uploader.methods.retry(fileId);
                            }, this.retryGracetimeMs );
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
            files: [],
            filesUploading: [],
            userId: '',
            userSettings: {
                workflow: {
                }
            },
            fileInputDisabled: false,
            singleCollectionTitle: "",
            selectedCollection: "",
            selectedHolding: "",
            currentPage: 1,
            pageSize: 8,
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
        authToken() {
            if( this.authMode == "CSRF" ) {
                return {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')};
            }
            return {'Authorization': `Bearer ${Vue.prototype.$keycloak.token}`};
        },
        sortedFilesUploading() {
            return this.filesUploading
                .filter( f => !f.isHidden )
                .sort( (a,b)  => Number( b.isFailed ) - Number( a.isFailed ) )
                .sort( (a,b)  => Number( b.isUploading) - Number( a.isUploading ) );
        },
        processDisabled: function() {
            return this.invalidBagName | this.numberOfFiles === 0 | this.hasIncompleteFiles | this.setCollection === null | this.getHolding == null;
        },
        setCollection: function (){
            return this.$route.query.collection;
        },
        getHolding: function (){
            return this.$route.query.holding;
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
            return true;
            /* Compound must explicitly be set to false to be disabled */
            /* TODO: If we are ito support this mode, we need to get this state from the token / store instead.
            let compoundSetting = this.userSettings.workflow.ingestCompoundModeEnabled;
            return this.userSettings.workflow.ingestCompoundModeEnabled !== undefined ? this.userSettings.workflow.ingestCompoundModeEnabled : true;
             */
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
        customerSelectsCollections: function() {
            return !!this.collections;
        },
        totalSize: function() {
            let size = 0;
            if (this.files != null) {
                this.files.forEach(file => {
                    size += file.filesize * 1;
                });
            }
            return size;
        }
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
        ...mapActions(['fetchUserSettings']),
        forceHolderReRender(){
            this.holderKey += 1;
        },
        loadNewHolders(){
            this.forceHolderReRender();
        },
        selectedHolder(holding){
            Vue.nextTick(() => {
                if( !holding ) {
                    this.updateQueryParams({ holding: null, page : null })
                } else {
                    this.updateQueryParams({ holding, page : null });
                }
            })
        },
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
            let queryCollection = query.collection;
            let queryHolding = query.holding;
            if( queryCollection != this.selectedCollection || queryHolding != this.selectedHolding  ) {
                this.changedCollection( queryCollection, queryHolding );
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

            this.bag = await this.createBag("", this.userId, this.selectedCollection, this.getHolding );

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
        async createBag( bagName, userId, selectedCollection, selectedHoldingUuid ) {
            let createdBag = (await axios.post("/api/v1/ingest/bags/", {
                name: bagName,
                owner: userId,
                collection_uuid: selectedCollection,
                holding_uuid: selectedHoldingUuid
            })).data.data;
            this.files = [];
            this.filesUploading = [];
            return createdBag;
        },
        async changedCollection(collectionId, holdingUuid) {
            if( !collectionId || !holdingUuid )
                return;
            this.selectedCollection = collectionId;
            this.selectedHolding = holdingUuid;
            if( this.bag && this.bag.id && this.compoundModeEnabled) {
                axios.patch("/api/v1/ingest/bags/"+this.bag.id, {
                    collection_uuid: collectionId,
                    holding_uuid: holdingUuid
                }).then( (response) => {
                    this.bag = response.data.data;
                });
            }
        },
        activeUploads() {
            return this.filesUploading.filter( (file) => file.isUploading ).length > 0;
        },
        uploadStartTime() {
            // Calculate the nearest time it's safe to start the next upload to avoid throttling issues.
            // If a previous upload was very recently started we add a delay, otherwise it can be started right away.
            return (this.filesUploading.length && (this.filesUploading[0].uploadStartTime + this.uploadDelayMs) > (new Date().getTime())) ?
                (this.filesUploading[0].uploadStartTime + this.uploadDelayMs) : (new Date().getTime());
        }
    },
    props: {
        authMode: { // Mode for authenticating to API
            type: String,
            default: "keycloak"
        },
        retryGracetimeMs: { // Delay before retrying a failed upload
            type: Number,
            default: 3000
        },
        uploadDelayMs: { // Delay before between file uploads
            type: Number,
            default: 250
        },
        maxAutoRetries: { // Maximum number of automatic upload retries before giving up
            type: Number,
            default: 5
        },
        height: {
            type: Number,
            default: 0
        }
    },
    async mounted() {
        this.fetchUserSettings().then(data => {
            this.pageSize = data.interface.tableRowCount;
        })
        let queryPage = parseInt(this.$route.query.page ?? "1");
        this.currentPage = queryPage;
        this.pageFrom = 1;
        this.pageTo = this.pageSize;
        this.dispatchRouting();
        this.userId = Vue.prototype.$keycloak.idTokenParsed.sub ?? "";
        if( !this.userId ) {
            console.error("No user found. Cannot continue!");
        }
        this.userSettings  = (await axios.get(`/api/v1/system/users/${this.userId}/preferences`)).data;

        if( this.compoundModeEnabled ) {

            this.bag = (await axios.get(`/api/v1/ingest/bags/latest?userId=${this.userId}`)).data.data;
            this.bagName = this.bag.name;
            let collection = this.bag.collection_uuid;
            let holding = this.bag.holding_uuid;
            this.updateQueryParams({ collection, holding });

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
                    isComplete: true,
                    uploadStartTime: (new Date().getTime())
                }) );
            }
            else {
                this.bag = (await this.createBag( "", this.userId, this.selectedCollection, this.selectedHolding ));
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
