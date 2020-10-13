<template>
    <b-modal id="meta" size="xl" :centered=true 
        :title="$t('admin.metadata.template.edit.title')"
        :header-class="['d-ruby','text-center']"
        :no-fade=true title-class="h3"
        :ok-title="$t('admin.metadata.template.edit.saveButton')"
        :cancel-title="$t('admin.metadata.template.edit.cancelButton')"
        @ok="saveTemplate"
    >
        <b-container fluid>
            <b-row v-for="scheme in schemes" :key="scheme.id">
                <b-form inline>
                    <b-col class="form-group">
                        <b-form-row v-for="schemeItem in scheme.fields" :key="schemeItem.id" class="w-100">
                            <label :for="fieldId(scheme.type, schemeItem.name)" class="small" >{{schemeItem.label}}</label>
                            <b-col class="m-1 w-100">
                                <b-form-input class="w-100"
                                    :id="fieldId(scheme.type, schemeItem.name)"
                                    v-model="metadataObject.metadata.dc[schemeItem.name]"
                                    v-bind:readOnly="readOnly"
                                />
                            </b-col>
                        </b-form-row>
                    </b-col>
                </b-form>
            </b-row>
        </b-container>
    </b-modal>
</template>

<script>
import axios from 'axios';

export default {
    data () {
        return {
            metadataObject: { "metadata": {"dc": {}} }
        }
    },

    props: {
        schemes: {
            /* Later on, this should also arrive from an api */
            type: Array,
            default : () => {
                return [
                    {
                        "type": "Dublin Core v1.1",
                        "fields":
                        [
                            {"name" : "title",       "label" : "Title",       "type": "text"},
                            {"name" : "creator",     "label" : "Creator",     "type": "text"},
                            {"name" : "subject",     "label" : "Subject",     "type": "text"},
                            {"name" : "description", "label" : "Description", "type": "text"},
                            {"name" : "publisher",   "label" : "Publisher",   "type": "text"},
                            {"name" : "contributor", "label" : "Contributor", "type": "text"},
                            {"name" : "date",        "label" : "Date",        "type": "text"},
                            {"name" : "type",        "label" : "Type",        "type": "text"},
                            {"name" : "format",      "label" : "Format",      "type": "text"},
                            {"name" : "identifier",  "label" : "Identifier",  "type": "text"},
                            {"name" : "source",      "label" : "Source",      "type": "text"},
                            {"name" : "language",    "label" : "Language",    "type": "text"},
                            {"name" : "relation",    "label" : "Relation",    "type": "text"},
                            {"name" : "coverage",    "label" : "Coverage",    "type": "text"},
                            {"name" : "rights",      "label" : "Rights",      "type": "text"},
                        ]
                    }
                ];
            },
        },
        readOnly: {
            type: Boolean,
            default: false
        },
        initialTemplate: {
            type: Object,
            default: null
        },
    },

    computed: {
    },
    mounted () {
    },
    watch: {
        initialTemplate: function(value){
            this.metadataObject.metadata = JSON.parse(JSON.stringify(value.metadata));

        },
    },
    methods: {
        fieldId(type, name){
            return `${type}-${name}`.replace(/\s/g,'');     /* Strip all whitespace from fieldId */
        },
        saveTemplate() {
            this.$emit( 'saveTemplate', this.metadataObject )
        }
    }
};
</script>
