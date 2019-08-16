<template>
    <div>
        <div class="row mb-5">
            <div class="col">
                <em class="mb-3">
                    {{ $t('upload.ingress') }}<br/>
                    {{ $t('upload.ingress2') }}<br/>
                    {{ $t('upload.ingress3') }}
                </em>
            </div>
        </div>

        <div class="row">
            <div class="col-11 m-2">
                <Gallery
                    :uploader="uploader"
                    :fileInputDisabled="fileInputDisabled"
                    @submit="addFileToQueue"
                    ref="gallery">
                </Gallery>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <br/>
            </div>
        </div>

        <div class="row plistHeader">
            <div class="col-4">Name</div>
            <div class="col-3">Fond</div>
            <div class="col-1">Files</div>
            <div class="col-1 listActionItems"></div>
            <div class="col-1"></div>
        </div>
        <div class="row plist ">
            <div class="col-4 pt-1">
                <form v-on:submit.prevent="">
                    <span class="noTextTransform"><input value="" :placeholder="bag.name" v-model="bag.name" type="text" class="form-control m-1" @input="bagnameUpdate"></span>
                </form>
            </div>
            <div class="col-3 pt-1">
                <form>
                    <select name="Fonds" class="select_piql">
                        <option value="Documents">Documents</option>
                        <option value="Video">Video</option>
                        <option value="Sound">Sound</option>
                    </select>
                </form>
            </div>
            <div class="col-1 pt-2">
                {{ bag.files }}
            </div>
            <div class="col-1 listActionItems pt-2">
                <i class="fas fa-list-ul" @click="onClick('/ingest/tasks/'+bag.id)"></i>&nbsp;
                <i class="fas fa-trash-alt"></i>
            </div>
            <div class="col-1 text-center">
                <button class="btn btn-primary btn-block w-75" v-bind:class="[{ disabled : processDisabled  }]" v-on:click="commitBagToProcessing">{{$t('upload.processButton')}}</button>
            </div>
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
                            console.log("Upload of " + name + " with id" + id + " completed.");
                            if( this.bag.id == uploadToBagId ){
                                this.files = axios.get("/api/v1/ingest/bags/"+uploadToBagId+"/files");
                            }
                        });
                    }
                }
            },
        });
        return {
            uploader: uploader,
            bag: {},
            files: {},
            userId: '',
            processDisabled: true,
            fileInputDisabled: false,
        };
    },

    components: {
//        FondSelect,
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
            //this.$refs.gallery.$refs.maybedropzone.$refs.dropzone.$refs.dropZone.ondrop = null
            this.$refs.gallery.ondrop = null;
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
        this.userId = (await axios.get("/api/v1/system/currentUser")).data;
        this.bag = await this.createBag("", this.userId);
        if(this.bag.files)
        {
            console.log('Files in bag '+this.bag.files);
            this.processDisabled = false;
        }
    },
    props: {
        button: Object
    }

};
</script>
<style>
    .select_piql {
        background-color: white;
        width: 150px;
        border: 0;
        height: 30px;
        border-radius: 2px;
        padding: 4px;
        box-sizing: content-box;
    }
</style>
