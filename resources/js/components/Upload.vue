<template>
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <em class="mb-3 mt-2">
                    {{ $t('upload.ingress') }}<br/>
                    {{ $t('upload.ingress2') }}<br/>
                    {{ $t('upload.ingress3') }}
                </em>
            </div>
        </div>
        <div class="row mb-5">
            <div class="col-12">
                <Gallery
                    :uploader="uploader"
                    :fileInputDisabled="fileInputDisabled"
                    @submit="addFileToQueue"
                    ref="gallery">
                </Gallery>
            </div>
        </div>

        <div class="card upload-widget-back w-95 p-2 pt-3 pb-4">
            <div class="row">
                <div class="col-sm-3 mr-3">{{ $t('upload.sipName') }}</div>
                <div class="col-sm-3 mr-3">Archive</div>
                <div class="col-sm-2 mr-0">{{ $t('upload.files') }}</div>
                <div class="col-sm-2 listActionItems mr-3"></div>
                <div class="col-sm-2"></div>
            </div>
            <form v-on:submit.prevent="">
                <div class="row w-90">
                    <div class="col-sm-3 mr-3">
                        <input value="" :placeholder="bag.name" v-model="bag.name" type="text" class="noTextTransform form-control pl-3" @input="setBagName" onclick="select()">
                    </div>
                    <div class="col-sm-3 mr-5">
                      <holding-picker :holdings="archives" :initialSelectedArchive="selectedArchive" :selected="changedArchive"></holding-picker>
                   </div>
                    <div class="col-sm-1 card p-2 pr-4 mr-3 w-auto" style="text-align: right; max-height: 3rem;">
                        {{ numberOfFiles || 0}}
                    </div>
                    <div class="col-sm-2 listActionItems mr-3" style="text-align: center">
                        <i class="fas fa-list-ul p-2 mr-4 hover-hand" @click="onClick('/ingest/tasks/'+bag.id)"></i>
                        <i class="fas fa-trash-alt p-2 hover-hand"></i>
                    </div>
                    <div class="col-sm-2 text-center">
                        <button class="btn btn-primary btn-lg w-75 mr-2" v-bind:class="[{ disabled : processDisabled  }]" v-on:click="commitBagToProcessing">{{$t('upload.processButton')}}</button>
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
                    onComplete: (id, name, response, xhr, something) => {
                        this.processDisabled = false;
                        let uploadToBagId = this.bag.id;
                        let fileSize = this.uploader.methods.getSize(id);
                        axios.post('/api/v1/ingest/fileUploaded', {
                            'fileName' : name,
                            'result' : response,
                            'bagId' : uploadToBagId,
                            'fileSize': fileSize
                        }).then( () => {
                            if( this.bag.id == uploadToBagId ){
                                axios.get("/api/v1/ingest/bags/"+uploadToBagId+"/files").then( (files) => {
                                    this.files = files.data;
                                });
                            }
                        });
                    }
                }}});
        return {
            uploader: uploader,
            bag: {},
            files: {},
            userId: '',
            processDisabled: true,
            fileInputDisabled: false,
            selectedArchive: 1,
            archives: [],
        };
    },

    components: {
        FineUploader
    },

    computed: {
        numberOfFiles: function() {
            return this.files.length;
        },
    },

    methods: {
        addFileToQueue(payload) {
        },
        async commitBagToProcessing(e) {
            if(this.processDisabled){
                return;
            }
            this.processDisabled = true;
            this.fileInputDisabled = true;

            let committed = (await axios.post("/api/v1/ingest/bags/"+this.bag.id+"/commit")).data;
            this.$refs.gallery.clearDropzone();
            this.uploader.methods.reset();
            this.bagName = "";
            this.bag = await this.createBag("", this.userId);
            this.files = [];
            this.fileInputDisabled = false;
            this.$refs.gallery.ondrop = null;
        },
        async setBagName() {
            let currentBagId = this.bag.id;
            let bagName = this.bag.name;
            let bag = null;
            axios.patch("/api/v1/ingest/bags/"+currentBagId, {
                'bagName': bagName
            }).then( (result) => {
                bag = result.data;
            });
            return bag;
        },
        async createBag(bagName, userId) {
            let createdBag = (await axios.post("/api/v1/ingest/bags/", {
                name: bagName,
                userId: userId,
            })).data;
            return createdBag;
        },
        changedArchive(archiveId) {
            console.log("archive is now: "+archiveId);
            this.selectedArchive = archiveId;
        }
    },
    async mounted() {
        axios.get("/api/v1/planning/holdings").then( (response) => {
            this.archives = response.data.data;
            this.selectedArchive = 2;
            Vue.nextTick( () => {
                $('#holdingPicker').selectpicker();
            });
        });

        this.userId = (await axios.get("/api/v1/system/currentUser")).data;
        this.bag = (await axios.get("/api/v1/ingest/bags/latest")).data;

        if(this.bag !== undefined && this.bag.status === "open")
        {
            this.files = (await axios.get('/api/v1/ingest/bags/' + this.bag.id + '/files')).data;
        }
        else
        {
            this.bag = (await this.createBag("", this.userId));
        }

        if(this.files)
        {
            this.processDisabled = false;
        }

    },
    props: {
        button: Object
    }

};
</script>
