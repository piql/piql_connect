<template>
    <div class="w-100">
        <breadcumb :subTitle="fileName" :subTitleRoute="{ name: 'ingest.uploader' }"/>
        <div class="row">
            <div class="col-sm-4">
                <button class="btn btn mr-2 pr-3 pl-3" @click="showModalData('holding')">{{$t('Holdings')}}</button>
                <button class="btn btn mr-2 pr-3 pl-3" @click="showModalData('archive')">{{$t('Archive')}}</button>
                <button class="btn btn mr-2 pr-3 pl-3" @click="showModalData('account')">{{$t('Accounts')}}</button>
            </div>
        </div>
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
            <modal name="modalData" width="1200" height="400">
                <div class="modalDataTitle">
                    {{modalDataContent.title}}
                </div>
                <div class="modalDataClose">

                    <a @click="hideModalData"><i class="fas fa-times-circle actionIcon text-center mr-2 cursorPointer"></i></a>
                </div>
                <div class="modalDataContent">
                    <div class="row">
                        <div v-for="scheme in schemes" class="col-sm-4">
                            <div v-for="schemeItem in scheme" class="row mb-2">
                                <div class="col-sm-12 input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1">{{schemeItem.label}}</span>
                                    </div>
                                    <input type="text" class="form-control" aria-describedby="basic-addon1" readonly v-bind:value="getMetaValue(schemeItem.name)">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </modal>
        </div>
    </template>

<script>
import axios from 'axios';
import VModal from 'vue-js-modal'
Vue.use(VModal, { componentName: 'modal'})
export default {
    data () {
        return {
            metadataObject: {
                metadata: [{ dc : {} }]
            },
            file: null,
            archiveMetadata: null,
            holdingMetadata: null,
            accountMetadata: null,
            modalDataType: null,
            bag: null,
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
        baseFileUrl: {
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
        },
        fileName: function() {
            this.loadFile();
            if (this.file) {
                return this.file.filename;
            } else {
                return "";
            }
        },
        modalDataContent: function () {
            switch (this.modalDataType) {
                case 'holding':
                    return {title: this.$t('Holdings'), 'content': this.loadHoldingMetadata()};
                case 'archive':
                    return {title: this.$t('Archive'), 'content': this.loadArchiveMetadata()};
                case 'account':
                    return {title: this.$t('Accounts'), 'content': this.loadAccountMetadata()};
                default:
                    return {title: null, 'content': null};
            }
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
        loadFile() {
            if (!this.file && this.baseFileUrl && this.metadataObject && this.idParam) {
                let urlTmp = this.baseFileUrl + this.idParam;
                axios.get(urlTmp).then( async ( resp ) =>  {
                    this.file = resp.data;
                });
            }
        },
        loadBag() {
            this.loadFile();
            if (!this.bag && this.file != null && this.file.bag_id != null) {
                axios.get('/api/v1/ingest/bags/' + this.file.bag_id).then( async ( resp ) =>  {
                    this.bag = resp.data.data;
                });
            }
        },
        loadHoldingMetadata() {
            this.loadBag();
            if (this.bag != null) {
                if (this.holdingMetadata == null) {
                    axios.get('/api/v1/planning/holding/' + this.bag.holding_uuid).then( async ( resp ) =>  {
                        this.holdingMetadata = resp.data.data != undefined ? resp.data.data.metadata.dc : null;
                    });
                }
                return this.holdingMetadata;
            }
        },
        loadArchiveMetadata() {
            this.loadBag();
            if (this.bag != null) {
                if (this.archiveMetadata == null) {
                    axios.get('/api/v1/planning/archive/' + this.bag.archive_uuid).then( async ( resp ) =>  {
                        this.archiveMetadata = resp.data.data.metadata.dc;
                    });
                }
                return this.archiveMetadata;
            }
        },
        loadAccountMetadata() {
            this.loadBag();
            if (this.bag != null) {
                if (this.accountMetadata == null) {
                    axios.get('/api/v1/planning/account/byUser/' + this.bag.owner).then( async ( resp ) =>  {
                        this.accountMetadata = resp.data.data.metadata.dc;
                    });
                }
                return this.accountMetadata;
            }
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
        getMetaValue(key) {
            if (this.modalDataContent != null && this.modalDataContent.content != undefined && key in this.modalDataContent.content) {
                return this.modalDataContent.content[key];
            } else {
                return "";
            }
        },
        showModalData(type) {
            this.modalDataType = type;
            this.$modal.show('modalData');
        },
        hideModalData() {
            this.$modal.hide('modalData');
        }

    }
};
</script>
<style scoped>
    .modalDataContent {
        width: 100%;
        height: 80%;
        overflow: hidden;
        padding: 10px;
    }
    .modalDataTitle {
        float: left;
        font-weight: bold;
        margin: 10px;
    }
    .modalDataClose {
        float: right;
        margin: 10px;
    }
</style>