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
                                   v-bind:readonly=readonly
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
                <button v-if="!readonly" class="btn btn p-3 mr-5 w-300" @click="$router.push({ name: 'ingest.upload' })">Cancel</button>
                <button v-if="!readonly" class="btn btn-ln btn-default w-300 p-3" @click="save()">Ok</button>
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
                    {"name" : "title",       "label" : "Title",       "type": "text"},
                    {"name" : "creator",     "label" : "Creator",     "type": "text"},
                    {"name" : "subject",     "label" : "Subject",     "type": "text"},
                    {"name" : "description", "label" : "Description", "type": "text"},
                    {"name" : "publisher",   "label" : "Publisher",   "type": "text"},
                ], [
                    {"name" : "contributor", "label" : "Contributor", "type": "text"},
                    {"name" : "date",        "label" : "Date",        "type": "text"},
                    {"name" : "type",        "label" : "Type",        "type": "text"},
                    {"name" : "format",      "label" : "Format",      "type": "text"},
                    {"name" : "identifier",  "label" : "Identifier",  "type": "text"},
                ], [
                    {"name" : "source",      "label" : "Source",      "type": "text"},
                    {"name" : "language",    "label" : "Language",    "type": "text"},
                    {"name" : "relation",    "label" : "Relation",    "type": "text"},
                    {"name" : "coverage",    "label" : "Coverage",    "type": "text"},
                    {"name" : "rights",      "label" : "Rights",      "type": "text"},
                ]
            ],
            metadataObject: {
                metadata: [{ dc : {} }]
            }
        }
    },
    methods: {
        getValue(key) {
            if((this.metadataObject.metadata.dc !== undefined) &&
                (key in this.metadataObject.metadata.dc))
                return this.metadataObject.metadata.dc[key];
            else
                return "";
        },
        setValue(key, value) {
            this.metadataObject.metadata.dc[key] = value;
        },
        async save() {
            if(!this.readonly) {
                // save errorToast handler just in case there is an error
                // because this object may not be present when the error occur
                let errorToast = this.errorToast;
                this.patch(
                    this.baseUrl + '/' + this.metadataObject.id,
                    this.metadataObject,
                ).catch(function (error) {
                    // todo: proper error handling and field input validation
                    let title = "Metadata error";
                    let message = (error.errors === undefined) ? "Saving matadata failed" : JSON.stringify(error.errors);
                    errorToast(title, message);
                });

                this.$router.push({name: 'ingest.upload'});
            }
        },
    },
    computed: {
        readonly: function() {
            return (this.$attrs.readOnly === undefined) ? false : this.$attrs.readOnly;
        },
        baseUrl: function() {
            return ((this.$attrs.url === undefined) ? '' : this.$attrs.url) + '/' + this.$route.params.fileId + '/metadata';
        }
    },
    async mounted() {
        let response = (await this.get(this.baseUrl)).data.data;
        if(response.length > 0) {
            this.metadataObject = response[0];
        } else {
            if(!this.readonly) {
                this.metadataObject = (await this.post(this.baseUrl)).data.data[0];
            }
        }
        // fixing the corner case where the controller returns an empty
        // array instead of an empty object
        // this should have been resoled in the controller
        if(Array.isArray(this.metadataObject.metadata.dc)) {
            this.metadataObject.metadata.dc = {};
        }
    }
}
</script>

<style scoped>

</style>
