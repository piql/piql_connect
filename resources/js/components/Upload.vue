<template>
    <div class="mb-2 mt-2">
        <div class="row">
            <div class="col-sm-1 text-right">
                <i class="fas fa-upload mr-3 titleIcon"></i>
            </div>
            <div class="col-sm-6 text-left">
                <h1>{{$t("upload.header")}}</h1>
            </div>
        </div>
        <div class="row mt-0 pt-0">
            <div class="col-sm-1"></div>
            <div class="col-sm-6 text-left" style="font-size: 0.7rem">
                    Upload files to your archives here by clicking the 'Add file' button.
            </div>
        </div>

        <div class="row form-group mt-2 mb-2">
            <div class="col-md-2 pl-0 ml-0" title="Upload files">
                <label for="uploadbutton" class="col-form-label-sm">&nbsp;</label>
                <FileInputComponent
                    id="uploadbutton"
                    :multiple="compoundModeEnabled"
                    :uploader="uploader"
                    :disabled="fileInputDisabled">
                    <slot name="inputbuttontext" ><span class="pl-3 pr-3"><i class="fas fa-plus mr-3"></i>{{$t("upload.addFileToUpload")}}</span></slot>
                </FileInputComponent>
            </div>

            <div v-show="compoundModeEnabled" class="col-md-2 text-left" title="Give packages memorable names by giving them titles (optional)">
                <label for="bagname" class="col-form-label-sm">Package title</label>
                <input id="bagname" value="" :placeholder="bag.name" v-model="bag.name" type="text" class="pl-3 noTextTransform form-control" style="border-radius: 0.5rem" @input="setBagName" onclick="select()">
            </div>

            <div class="col-md-2" title="Select the archive to ingest to">
                <archive-picker label="Archives" :archives="archives" :initialSelection="selectedArchive" @selectionChanged="changedArchive"></archive-picker>
            </div>

            <div class="col-md-2" title="Select a holding from where you can access your data later">
                <holding-picker label="Holdings" :holdings="holdings" :initialSelection="selectedHoldingTitle" @selectionChanged="changedHolding"></holding-picker>
            </div>

            <div class="col-md-2">
            </div>

            <div v-show="compoundModeEnabled" class="col-md-2 text-center pr-0">
                <label for="processButton" class="col-form-label-sm">&nbsp;</label>
                <button title="Start the ingest process" id="processButton" class="btn w-100 m-0 p-2" v-bind:class="[{ disabled : processDisabled  }]" v-on:click="commitBagToProcessing">{{$t('upload.processButton')}}</button>
            </div>
        </div>

        <div class="row plistHeader"> <div class="col-sm-1 text-center">
            </div>
            <div class="col-md-6 col-sm-5 col-xs-3 text-left align-self-center">
                Filename
            </div>
            <div class="col-xs-2 col-sm-2 text-center align-self-center">
                File size
            </div>
            <div class="col-xs-2 col-sm-3 text-center align-self-center">
                Actions
            </div>
        </div>

        <UploadFileItem v-for="(file,index) in filesUploading" v-bind:file="file" :key="file.id"
            @metadataClicked="metadataClicked" @removeClicked="removeClicked"
            v-if="index >= pageFrom-1 && index <= pageTo-1 " />
        <div class="row thumbnailList invisible" v-for="pad in pagerPad"></div>
        <div class="row text-center pagerRow">
            <div class="col">
                <Pager :meta="filesUploadingMeta" @updatePage="updatePage" v-if="totalFilesUploading > 0" />
            </div>
        </div>
    </div>
</template>

<script language="text/babel">
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
    chunking: {
    enabled: true,
    partSize: 1024*768,
    mandatory: true,
    concurrent: {
    enabled: false
    },
    },
    callbacks: {
    onValidate: (id, name) => {
    this.processDisabled = true;
    },
    onSubmit: (id, name) => {
    this.filesUploading.unshift({
    'id': id,
    'filename': name,
    'progressBarStyle': "width: 0%",
    'uploadedFileId': '',
    'fileSize': 0,
    'uploadedFileSize': 0,
    'isUploading': false
    });
    },
    onProgress: (id, name, uploadedBytes, totalBytes) => {
    let progress = Math.round( uploadedBytes * ( 100.0 / totalBytes ) );
    let filesIndex = this.filesUploading.findIndex( (file) => file.id == id );
    this.filesUploading[filesIndex].isUploading = true;
    this.filesUploading[filesIndex].fileSize = totalBytes;
    this.filesUploading[filesIndex].uploadedFileSize = uploadedBytes;
    this.filesUploading[filesIndex].progressBarStyle = {'width': `${progress}%` };
    },
    onComplete: async (id, name, response, xhr, something) => {
    let filesIndex = this.filesUploading.findIndex( (file) => file.id == id );
    this.filesUploading[filesIndex].isUploading = false;
    let fileSize = this.uploader.methods.getSize(id);
    this.filesUploading[filesIndex].fileSize = fileSize;

    if( this.compoundModeEnabled ) {
    this.processDisabled = false;
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
    })
    } else {
    axios.post("/api/v1/ingest/files/bag", {
      'fileName' : name,
      'result' : response,
      'fileSize': fileSize
      });
      }
    }
    }}});
    return {
      uploader: uploader,
      bag: {},
      files: {},
      filesUploading: [],
      userId: '',
      userSettings: {
        workflow: {
        }
      },
      processDisabled: true,
      fileInputDisabled: false,
      archives: [],
      selectedArchive: {},
      holdings: [],
      selectedHoldingTitle: {},
      currentPage: 1,
      pageSize: 4,
      pageFrom: 1,
      pageTo: 4
      };
    },

    components: {
      FineUploader
    },

    computed: {
        numberOfFiles: function() {
            return this.files.length;
        },
        compoundModeEnabled: function() {
            return this.userSettings.workflow.ingestCompoundModeEnabled;
        },
        uploadInProgress: function() {
            return this.filesUploading.length > 0;
        },
        totalFilesUploading: function() {
            return this.filesUploading.length;
        },
        pageLast: function() {
            return Math.ceil( this.totalFilesUploading / this.pageSize );
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
                'total': this.totalFilesUploading
            }
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
            this.processDisabled = true;
            this.fileInputDisabled = true;

            await this.doProcessing( this.bag.id );
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
            let currentBagId = this.bag.id;
            let bagName = this.bag.name;
            if( bagName.length == 0 ) {
                bagName = moment().format("YYYYMMDDX").substring(0,14);
            }
            axios.patch("/api/v1/ingest/bags/"+currentBagId, {
                'name': bagName
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
    async mounted() {
        this.currentPage = 1;
        this.pageFrom = 1;
        this.pageTo = this.pageSize;
        await axios.get("/api/v1/planning/archives").then( (response) => {
            this.archives = response.data.data;
            this.selectedArchive = this.archives[0].uuid;
        });

    this.userId = (await axios.get("/api/v1/system/currentUser")).data;
    this.userSettings  = (await axios.get("/api/v1/system/currentUserSettings")).data;

    if( this.compoundModeEnabled ) {

    this.bag = (await axios.get("/api/v1/ingest/bags/latest")).data.data;
    Vue.nextTick( () => { $('#archivePicker').selectpicker('val', this.selectedArchive);});

    if(this.bag !== undefined && this.bag.status === "open") {
        this.files = (await axios.get('/api/v1/ingest/bags/' + this.bag.id + '/files')).data.data;
        this.filesUploading = this.files.map( (file, index) => ({
            id: index+100000,  /*A large enough number to avoid collisions with id's provided by FineUploader */
            filename: file.filename,
            progressBarStyle: "width: 0%",
            uploadedFileId: file.id,
            uploadedToBagId: file.bag_id,
            fileSize: file.filesize,
            uploadedFileSize: file.filesize,
            isUploading: false
            }) );
        }
        else {
            this.bag = (await this.createBag( "", this.userId, this.selectedArchive, this.selectedHoldingTitle ));
        }
        if(this.filesUploading) {
            this.processDisabled = false;
        }
        } else {
            Vue.nextTick( () => {
                $('#archivePicker').selectpicker('val', this.selectedArchive);
                this.setupHoldings( this.selectedArchive );
            });
        }
        }
}

</script>
