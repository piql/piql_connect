<template>
    <div class="container-fluid">
        <div class="row mb-5">
            <div class="col-sm-1 text-left">
                <i class="fas fa-tags mr-3 titleIcon"></i>
            </div>
            <div class="col-sm-6 text-left">
                <h1>{{$t("ingest.metadata.editFile")}}</h1>
            </div>
        </div>

        <form>
            <div class="row">
                <div v-for="scheme in schemes" class="col-sm-4">
                    <div v-for="schemeItem in scheme" class="row mb-2">
                        <div class="col-sm-7 form-group mb-2">
                            <label class="small" >{{schemeItem.label}}</label>
                            <input class="form-control input-sm" type="text"
                                   v-bind:id="schemeItem.name"
                                   v-bind:value="getValue(schemeItem.name)"
                                   @input="setValue(schemeItem.name, $event.target.value)">
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="row mt-5">
            <div class="col-sm-4"></div>
            <div class="col-sm-8">
                <button class="btn btn p-3 mr-5 w-300" @click="$router.push({ name: 'ingest.upload' })">Cancel</button>
                <button class="btn btn-ln btn-default w-300 p-3" @click="save()">Ok</button>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: "Metadata",
    data() {
        return {
            schemes: [
                [
                    {"name" : "dc:title",       "label" : "Title",       "type": "text"},
                    {"name" : "dc:creator",     "label" : "Creator",     "type": "text"},
                    {"name" : "dc:subject",     "label" : "Subject",     "type": "text"},
                    {"name" : "dc:description", "label" : "Description", "type": "text"},
                    {"name" : "dc:publisher",   "label" : "Publisher",   "type": "text"},
                ], [
                    {"name" : "dc:contributor", "label" : "Contributor", "type": "text"},
                    {"name" : "dc:date",        "label" : "Date",        "type": "text"},
                    {"name" : "dc:type",        "label" : "Type",        "type": "text"},
                    {"name" : "dc:format",      "label" : "Format",      "type": "text"},
                    {"name" : "dc:identifier",  "label" : "Identifier",  "type": "text"},
                ], [
                    {"name" : "dc:source",      "label" : "Source",      "type": "text"},
                    {"name" : "dc:language",    "label" : "Language",    "type": "text"},
                    {"name" : "dc:relation",    "label" : "Relation",    "type": "text"},
                    {"name" : "dc:coverage",    "label" : "Coverage",    "type": "text"},
                    {"name" : "dc:rights",      "label" : "Rights",      "type": "text"},
                ]
            ],
            metadataObject: {
                metadata: []
            }
        }
    },
    methods: {
        getValue(key) {
            if(('metadata' in this.metadataObject) && (key in this.metadataObject.metadata))
                return this.metadataObject.metadata[key];
            else
                return "";
        },
        setValue(key, value) {
            this.metadataObject.metadata[key] = value;
        },
        async save() {
            // save errorToast handler just in case there is an error
            // because this object may not be present when the error occur
            let errorToast = this.errorToast;
            this.patch(
                '/api/v1/ingest/files/' + this.$route.params.fileId + '/metadata/' + this.metadataObject.id,
                this.metadataObject,
            ).catch(function(error) {
                // todo: proper error handling and field input validation
                let title = "Metadata error";
                let message = (error.errors === undefined) ? "Saving matadata failed" : JSON.stringify(error.errors);
                errorToast( title, message );
            });

            this.$router.push({ name: 'ingest.upload' });
        }
    },
    async mounted() {
        let response = (await this.get('/api/v1/ingest/files/' + this.$route.params.fileId + '/metadata')).data.data;
        if(response.length > 0) {
            this.metadataObject = response[0];
        } else {
            this.metadataObject = (await this.post('/api/v1/ingest/files/' + this.$route.params.fileId + '/metadata')).data.data[0];
        }
    }
}
</script>

<style scoped>

</style>
