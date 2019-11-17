<template>
    <div class="container-fluid">
        <div class="row mb-4 w-auto">
            <div class="col-6">
                <em class="mb-3 mt-2">
                    {{ $t('upload.ingress') }}
                </em>
            </div>
        </div>
        <div class="row mb-5">
            <div class="col-sm-12">
                <Gallery
                    :uploader="uploader"
                    :fileInputDisabled="fileInputDisabled"
                    @submit="addFileToQueue"
                    ref="gallery">
                </Gallery>
            </div>
        </div>

        <div class="card upload-widget-back w-auto p-2 pt-3 pb-4 w-95">
            <div class="row mb-2">
                <div v-if="compoundModeEnabled" class="col-sm-2 mr-1 ml-1">{{ $t('upload.sipName') }}</div>
                <div class="col-sm-2 mr-1">{{ $t('Archive') }}</div>
                <div class="col-sm-2 mr-1">{{ $t('Holding') }}</div>
                <div v-if="compoundModeEnabled" class="col-sm-2 mr-1">{{ $t('upload.files') }}</div>
                <div v-if="compoundModeEnabled" class="col-sm-1 listActionItems mr-3"></div>
                <div v-if="compoundModeEnabled" class="col-sm-2"></div>
            </div>
            <form v-on:submit.prevent="">
                <div class="row">
                    <div v-if="compoundModeEnabled" class="col-sm-2 mr-2">
                        <input value="" :placeholder="bag.name" v-model="bag.name" type="text" class="noTextTransform form-control pl-3" @input="setBagName" onclick="select()">
                    </div>
                    <div class="col-sm-2 mr-2">
                      <archive-picker :archives="archives" :initialSelection="selectedArchive" @selectionChanged="changedArchive"></archive-picker>
                   </div>
                    <div class="col-sm-2 mr-2">
                      <holding-picker :holdings="holdings" :initialSelection="selectedHoldingTitle" @selectionChanged="changedHolding"></holding-picker>
                   </div>

                    <div v-if="compoundModeEnabled" class="col-sm-1 card p-2 pr-4 mr-2" style="text-align: right; max-height: 3rem;">
                        {{ numberOfFiles || 0}}
                    </div>
                    <div v-if="compoundModeEnabled" class="col-sm-2 listActionItems mr-2" style="text-align: center">
                        <i class="fas fa-list-ul p-2 mr-4 hover-hand" @click="onClick('/ingest/tasks/'+bag.id)"></i>
                        <i class="fas fa-trash-alt p-2 hover-hand"></i>
                    </div>
                    <div v-if="compoundModeEnabled" class="col-sm-2 text-center">
                        <button class="btn btn-primary btn-lg mr-2 w-100" v-bind:class="[{ disabled : processDisabled  }]" v-on:click="commitBagToProcessing">{{$t('upload.processButton')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script language="text/babel">
import FineUploaderTraditional from 'fine-uploader-wrappers'
import FineUploader from 'vue-fineuploader';
import axios from 'axios';
import JQuery from 'jquery';
let $ = JQuery;
import selectpicker from 'bootstrap-select';

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
                    partSize: 1000000,
                    mandatory: true,
                    concurrent: {
                        enabled: false
                    },
                },
                callbacks: {
                    onValidate: (id, name) => {
                        this.processDisabled = true;
                    },
                    onComplete: async (id, name, response, xhr, something) => {
                        let fileSize = this.uploader.methods.getSize(id);
                        if( this.compoundModeEnabled ) {
                            this.processDisabled = false;
                            let uploadToBagId = this.bag.id;
                            axios.post('/api/v1/ingest/fileUploaded', {
                                'fileName' : name,
                                'result' : response,
                                'bagId' : uploadToBagId,
                                'fileSize': fileSize
                            }).then( async () => {
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
            selectedHoldingTitle: {}
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
    },

    methods: {
        onClick(url) {
          window.location = url;
        },
        addFileToQueue(payload) {
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
            this.$refs.gallery.ondrop = null;
        },
        async doProcessing( bagId ) {
            let committed = (await axios.post("/api/v1/ingest/bags/"+bagId+"/commit")).data.data;
            this.$refs.gallery.clearDropzone();
            this.uploader.methods.reset();
            this.bagName = "";
            this.files = [];
        },
        async setBagName() {
            let currentBagId = this.bag.id;
            let bagName = this.bag.name;
            axios.patch("/api/v1/ingest/bags/"+currentBagId, {
                'name': bagName
            }).then( (result) => {
                this.bag = result.data.data;
            });
            return this.bag;
        },
        async createBag( bagName, userId, selectedArchive, selectedHoldingTitle ) {
            let createdBag = (await axios.post("/api/v1/ingest/bags/", {
                name: bagName,
                owner: userId,
                archive_uuid: selectedArchive,
                holding_name: selectedHoldingTitle
            })).data.data;
            return createdBag;
        },
        async setupHoldings(archiveId, initialHolding) {
            await axios.get('/api/v1/planning/archives/'+archiveId+'/holdings').then( (response) => {
              this.holdings = response.data.data;
                let defaultHolding = this.holdings[0].title;
                Vue.nextTick( () => { $('#holdingPicker').selectpicker('val', initialHolding || defaultHolding);});
            });
        },
        async changedArchive(archiveId) {
            this.selectedArchive = archiveId;
            if( this.compoundModeEnabled) {
                await this.setupHoldings(archiveId, this.bag.holding_name);
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
        await axios.get("/api/v1/planning/archives").then( (response) => {
            this.archives = response.data.data;
            this.selectedArchive = this.archives[0].uuid;
        });

        this.userId = (await axios.get("/api/v1/system/currentUser")).data;
        this.userSettings  = (await axios.get("/api/v1/system/currentUserSettings")).data;

        if( this.compoundModeEnabled ) {

            this.bag = (await axios.get("/api/v1/ingest/bags/latest")).data.data;
            Vue.nextTick( () => { $('#archivePicker').selectpicker('val', this.selectedArchive);});

            if(this.bag !== undefined && this.bag.status === "open")
            {
                this.files = (await axios.get('/api/v1/ingest/bags/' + this.bag.id + '/files')).data.data;
            }
            else
            {
                this.bag = (await this.createBag( "", this.userId, this.selectedArchive, this.selectedHoldingTitle ));
            }
            if(this.files)
            {
                this.processDisabled = false;
            }
        } else {
            Vue.nextTick( () => {
                $('#archivePicker').selectpicker('val', this.selectedArchive);
                this.setupHoldings( this.selectedArchive );
            });
        }
    },
    props: {
        button: Object,
    }

};
</script>
