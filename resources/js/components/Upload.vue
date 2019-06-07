<template>
    <div>
        <FineUploader :button="button"
                      :uploader="uploader"
                      @submit="addFileToQueue">
            <div id="browse" class="dropFiles"></div>
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
                    endpoint: '/api/v1/ingest/upload'
                },
                callbacks: {
                    onComplete: (id, name, response) => { console.log("Completed") }
                }
            },
        });
        return {
            button: '.browse',
            uploader
        };
    },

    components: { FineUploader },

    methods: {
        addFileToQueue(payload) {
        },
    },
    mounted() {
        console.log('Uploader component mounted.');
    }
};
</script>
