<template>
    <div>
        <!-- <form>
            <div class="row">
                <div class="col-sm-4">
                    <div class="row mb-2">
                        <div class="col-sm-7 form-group mb-2">
                            <label class="small"  for="m_title">Title</label>
                            <input class="form-control input-sm" type="text" id="m_title">
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-sm-7 form-group mb-2">
                            <label class="small"  for="m_author">Author</label>
                            <input class="form-control input-sm" type="text" id="m_author">
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-sm-7 form-group mb-2">
                            <label class="small"  for="m_subject">Subject</label>
                            <input class="form-control input-sm" type="text" id="m_subject">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-7 form-group mb-2">
                            <label class="small"  for="m_description">Description</label>
                            <input class="form-control input-sm" type="text" id="m_description">
                        </div>
                    </div>


                    <div class="row mb-2">
                        <div class="col-sm-7 form-group mb-2">
                            <label class="small"  for="m_publisher">Publisher</label>
                            <input class="form-control input-sm" type="text" id="m_publisher">
                        </div>
                    </div>
                </div>

                <div class="col-sm-4">

                    <div class="row mb-2">
                        <div class="col-sm-7 form-group mb-2">
                            <label class="small"  for="m_date">Date</label>
                            <input class="form-control input-sm" type="text" id="m_date">
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-sm-7 form-group mb-2">
                            <label class="small"  for="m_language">Language</label>
                            <input class="form-control input-sm" type="text" id="m_language">
                        </div>
                    </div>



                    <div class="row mb-2">
                        <div class="col-sm-7 form-group mb-2">
                            <label class="small"  for="m_resourcetype">Resource type</label>
                            <input class="form-control input-sm" type="text" id="m_resourcetype">
                        </div>
                    </div>


                    <div class="row mb-2">
                        <div class="col-sm-7 form-group mb-2">
                            <label class="small"  for="m_resourceidentifier">Resource Identifier</label>
                            <input class="form-control input-sm" type="text" id="m_resourceidentifier">
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-sm-7 form-group mb-2">
                            <label class="small"  for="m_otherContributor">Other Contributor</label>
                            <input class="form-control input-sm" type="text" id="m_othercontributor">
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">

                    <div class="row mb-2">
                        <div class="col-sm-7 form-group mb-2">
                            <label class="small"  for="m_format">Format</label>
                            <input class="form-control input-sm" type="text" id="m_format">
                        </div>
                    </div>


                    <div class="row mb-2">
                        <div class="col-sm-7 form-group mb-2">
                            <label class="small"  for="m_source">Source</label>
                            <input class="form-control input-sm" type="text" id="m_source">
                        </div>
                    </div>


                    <div class="row mb-2">
                        <div class="col-sm-7 form-group mb-2">
                            <label class="small"  for="m_relation">Relation</label>
                            <input class="form-control input-sm" type="text" id="m_relation">
                        </div>
                    </div>


                    <div class="row mb-2">
                        <div class="col-sm-7 form-group mb-2">
                            <label class="small"  for="m_coverage">Coverage</label>
                            <input class="form-control input-sm" type="text" id="m_coverage">
                        </div>
                    </div>


                    <div class="row mb-2">
                        <div class="col-sm-7 form-group mb-2">
                            <label class="small"  for="m_Rights Management">Rights Management</label>
                            <input class="form-control input-sm" type="text" id="m_rightsmanagement">
                        </div>
                    </div>
                </div>
            </div>
        </form> -->
        <form>
            <div class="row">
                <div v-for="scheme in schemes" class="col-sm-4">

                    <div v-for="schemeItem in scheme" class="row mb-2">
                        <div class="col-sm-7 form-group mb-2">
                            <label class="small" >{{schemeItem.label}}</label>
                            <input class="form-control input-sm" type="text" v-bind:id="schemeItem.name" v-bind:value="getValue(schemeItem.name)" @input="setValue(schemeItem.name, $event.target.value)">
                        </div>
                    </div>

                </div>
            </div>
        </form>
        <div class="row mt-1">
            <div class="col-sm-3"></div>
            <div class="col-sm-8 p-4 right">
                <a href="" >
                    <button class="btn btn p-3 mr-5 w-300">Cancel</button>
                </a>
                    <button class="btn btn-ln btn-default w-300 p-3" @click="save()">Save</button>
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
                metadataObject: {}
            }
        },
        props: {
            fileid: '',
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
                this.metadataObject = (await axios.patch('/api/v1/ingest/files/' + this.fileid + '/metadata/' + this.metadataObject.id, this.metadataObject)).data.data[0];
            }
        },
        async mounted() {
            console.log(this.fileid);
            let response = (await axios.get('/api/v1/ingest/files/' + this.fileid + '/metadata')).data.data;
            if(response.length > 0) {
                this.metadataObject = response[0];
            } else {
                this.metadataObject = (await axios.post('/api/v1/ingest/files/' + this.fileid + '/metadata')).data.data[0];
            }
        }
    }
</script>

<style scoped>

</style>
