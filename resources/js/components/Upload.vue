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
                <div class="col-sm-2 mr-3">Fonds</div>
                <div class="col-sm-1 mr-3">{{ $t('upload.files') }}</div>
                <div class="col-sm-2 listActionItems mr-3"></div>
                <div class="col-sm-3"></div>
            </div>

            <form v-on:submit.prevent="">
                <div class="row w-90">
                    <div class="col-sm-3 mr-3">
                        <input value="" :placeholder="bag.name" v-model="bag.name" type="text" class="noTextTransform form-control pl-3" @input="bagnameUpdate" onclick="select()">
                    </div>
                    <div class="col-sm-2 mr-3">
                        <select name="Fonds" class="form-control selectpicker">
                            <option value="Documents">Documents</option>
                            <option value="Video">Video</option>
                            <option value="Sound">Sound</option>
                        </select>
                    </div>
                    <div class="col-sm-1 card p-2 pr-4 mr-3" style="text-align: right; max-height: 3rem;">
                        {{ this.numberOfFiles}}
                    </div>
                    <div class="col-sm-2 listActionItems mr-3" style="text-align: center">
                        <i class="fas fa-list-ul p-2 mr-4 hover-hand" @click="onClick('/ingest/tasks/'+bag.id)"></i>
                        <i class="fas fa-trash-alt p-2 hover-hand"></i>
                    </div>
                    <div class="col-sm-3 text-center">
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
                    onComplete: (id, name, response) => {
                        this.processDisabled = false;
                        let uploadToBagId = this.bag.id;
                        axios.post('/api/v1/ingest/fileUploaded', {
                            'fileName' : name,
                            'result' : response,
                            'bagId' : uploadToBagId,
                        }).then( () => {
                            if( this.bag.id == uploadToBagId ){
                                axios.get("/api/v1/ingest/bags/"+uploadToBagId+"/files").then( (files) => {
                                    this.files = files.data;
                                    this.numberOfFiles = files.data.length;
                                });
                            }
                        });
                    }
                }}});
        return {
            uploader: uploader,
            bag: {},
            numberOfFiles: 0,
            files: {},
            userId: '',
            processDisabled: true,
            fileInputDisabled: false,
        };
    },

    components: {
        FineUploader
    },

    methods: {
        bagnameUpdate()
        {
            console.log(this.bag.name);
        },
        onClick(url) {
            let updatedBag = this.setBagName(this.bag.name);
            if(updatedBag != null){
                this.bag = updatedBag;
            }
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

            let updatedBag = await this.setBagName(this.bag.name);
            if(updatedBag != null){
                this.bag = updatedBag;
            }

            let committed = (await axios.post("/api/v1/ingest/bags/"+this.bag.id+"/commit")).data;
            this.$refs.gallery.clearDropzone();
            this.uploader.methods.reset();
            this.bag = await this.createBag("", this.userId);
            this.fileInputDisabled = false;
            this.$refs.gallery.ondrop = null;
            this.numberOfFiles = 0;
        },
        async setBagName(bagName) {
            let currentBagId = this.bag.id;
            let bag = null;
            console.log("updating name of bag with id "+currentBagId+" to "+bagName);
            axios.patch("/api/v1/ingest/bags/"+currentBagId, {
                'bagName': bagName
            }).then( (result) => {
                console.log("updated bag: ");
                console.log(result.data);
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
    },
    async mounted() {
        console.log('Uploader component mounted.');
        this.numberOfFiles = 0;
        this.userId = (await axios.get("/api/v1/system/currentUser")).data;
        this.bag = await this.createBag("", this.userId);
        if(this.bag.files)
        {
            console.log('Files in bag '+this.bag.files);
            this.processDisabled = false;
            this.numberOfFiles = this.bag.files;
        }
    },
    props: {
        button: Object
    }

};
</script>
