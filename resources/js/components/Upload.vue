<template>
    <div>
        <FineUploader :button="button"
                      :uploader="uploader"
                      @submit="addFileToQueue">
            <div id="browse" class="dropFiles">&nbsp;</div>
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
                    onComplete: (id, name, response) => { console.log("Upload of " + name + " completed") }
                }
            },
        });
        return {
            button: '.browse',
            uploader
        };
    },

    components: { 
        FineUploader,
    },

    methods: {
        addFileToQueue(payload) {
        },
    },
    mounted() {
        console.log('Uploader component mounted.');
    }
};
</script>
