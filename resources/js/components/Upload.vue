<template>
    <div>
        <Gallery
            :uploader="uploader"
            @submit="addFileToQueue">
        </Gallery>
        <br/>
        <div class="row">
            <div class="col">
                <div>Active Ingest Session: <span style="font-weight: bold; color: darkorange; font-family: agenda; border-bottom: 1px dotted darkgray;">{{bag.name}}</span>.
                </div>
            </div>
            <div class="col-2">
                <button class="btn btn-primary btn-lg" v-on:click="commitBagToProcessing">Process</button>
            </div>
        </div>

        <div class="row" style="height: 10px;"></div> 

        <Bags :items="bags" @selectActiveBag="selectActiveBag"></Bags>

    </div>
</template>

<script language="text/babel">
import FineUploaderTraditional from 'fine-uploader-wrappers'
import FineUploader from 'vue-fineuploader';

export default {
    data() {
        let self = this;
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
                    onComplete: (id, name, response) => {
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
            bags: {},
            files: {}
        };
    },

    components: { 
        FineUploader
    },

    methods: {
        addFileToQueue(payload) {
        },
        commitBagToProcessing() {
            let bagId = this.bag.id;
            axios.post("/api/v1/ingest/bags/"+bagId+"/preCommit").then( async () => {
                this.bags = (await axios.get("/api/v1/ingest/bags")).data;
                this.bag = this.bags[0] || {};
            });

            axios.post("/api/v1/ingest/bags/"+bagId+"/commit").then( async (response) => {
                console.log("Bag "+bagId+" committed!");
            });
        },
        selectActiveBag(bagIdToActivate){
            console.log("Activating bag with id "+bagIdToActivate);
            axios.get('/api/v1/ingest/bags/'+bagIdToActivate).then( (response) =>{
                this.bag = response.data;
            });
        },
    
    },
    async mounted() {
        let self = this;
        console.log('Uploader component mounted.');
        this.bags = (await axios.get("/api/v1/ingest/bags")).data; 
        await axios.get("/api/v1/system/currentBag")
            .then( (response) => { self.bag = response.data } )
            .then( async () => {
                console.log("getting files from bag "+self.bag.id);
                let files = (axios.get("/api/v1/ingest/bags/"+self.bag.id+"/files")).data;
            });
            
    },
    props: {
        button: Object
    }

};
</script>
