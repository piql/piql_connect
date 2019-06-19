<template>
    <div>
        <div>Uploading to bag <span style="font-weight: bold; color: darkorange; font-family: agenda; border-bottom: 1px dotted darkgray;">{{bag.name}}</span>.</div>
        <FineUploader 
            :uploader="uploader"
            @submit="addFileToQueue">
            <div class="dropFiles">&nbsp;</div>
        </FineUploader>
    </div>
</template>

<script language="text/babel">
import FineUploaderTraditional from 'fine-uploader-wrappers'
import FineUploader from 'vue-fineuploader';

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
                    onComplete: (id, name, response) => { 
                        axios.post('/api/v1/ingest/fileUploaded', {
                            'fileName' : name,
                            'result' : response,
                        });
                        console.log("Upload of " + name + " with id" + id + " completed.");
                        console.log(response);

                    }
                }
            },
        });
        return {
            uploader: uploader,
            bag: {},
            files: {}
        };
    },

    components: { 
        FineUploader
    },

    methods: {
        addFileToQueue(payload) {
        },
    },
    async mounted() {
        let that = this;
        console.log('Uploader component mounted.');
        await axios.get("/api/v1/system/currentBag")
            .then( (response) => { that.bag = response.data } )
            .then( async () => {
                console.log("getting files from bag "+that.bag.id");
                let files = (axios.get("/api/v1/ingest/bags/"+that.bag.id+"/files")).data;
            });
            
    },
    props: {
        button: Object
    }

};
</script>
