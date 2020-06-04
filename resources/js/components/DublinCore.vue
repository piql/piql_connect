<template>
    <div class="w-100">
        <form>
            <div class="row">
                <div v-for="scheme in schemes" class="col-sm-4">
                    <div v-for="schemeItem in scheme" class="row mb-2">
                        <div class="col-sm-7 form-group mb-2">
                            <label class="small" >{{schemeItem.label}}</label>
                            <input class="form-control input-sm" type="text"
                                v-bind:readOnly=readOnly
                                v-bind:id="schemeItem.name"
                                v-bind:value="getValue(schemeItem.name)"
                                @input="setValue(schemeItem.name, $event.target.value)">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div v-if="!readOnly" class="row mt-5">
                <div class="col-6 mr-4"></div>
                <button class="btn btn mr-2 pr-5 pl-5 col-2" @click="$router.go(-1)">{{$t('Cancel')}}</button>
                <button class="btn btn-ln btn-default pr-5 pl-5 col-2" @click="save()">{{$t('OK')}}</button>
            </div>
            <div v-if="readOnly" class="row mt-5">
                <div class="col-8 mr-4"></div>
                <button class="col-2 pl-3 pr-3 text-center btn btn-ln btn-default" @click="$router.go(-1)">{{$t('Back')}}</button>
            </div>

        </div>
    </template>

<script>
export default {
    data () {
        return {
            metadataObject: {
                metadata: [{ dc : {} }]
            }
        }
    },

    props: {
        baseUrl: {
            type: String,
            default: "/api/v1/ingest/files",
        },
        urlParam: {
            type: String,
            default: "fileId"
        },
        schemes: {
            type: Array,
            default : () => [
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
            ]
        },
        readOnly: {
            type: Boolean,
            default: false
        }
    },

    computed: {
        idParam: function() {
            return this.$route.params[this.urlParam];
        },
        url: function() {
            return `${this.baseUrl}/${this.idParam}/metadata`;
        }
    },
    async mounted () {
        let response = (await this.get(this.url)).data.data;
        if(response.length > 0) {
            this.metadataObject = response[0];
        } else {
            if(!this.readOnly) {
                this.metadataObject = (await this.post(this.url)).data.data[0];
            }
        }
        // fixing the corner case where the controller returns an empty
        // array instead of an empty object
        // this should have been resoled in the controller
        if(Array.isArray(this.metadataObject.metadata.dc)) {
            this.metadataObject.metadata.dc = {};
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
            if(!this.readOnly) {
                // save errorToast handler just in case there is an error
                // because this object may not be present when the error occur
                let errorToast = this.errorToast;
                this.patch(
                    this.url + '/' + this.metadataObject.id,
                    this.metadataObject,
                ).catch(function (error) {
                    // todo: proper error handling and field input validation
                    let title = "Metadata error";
                    let message = (error.errors === undefined) ? "Saving matadata failed" : JSON.stringify(error.errors);
                    errorToast(title, message);
                });

                this.$router.go(-1);
            }
        },

    }
};
</script>
