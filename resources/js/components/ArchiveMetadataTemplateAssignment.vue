<template>
    <div class="w-100">
        <page-heading icon="fa-user" :title="$t('admin.archive.metadata.template.assign')" :ingress="$t('admin.archive.metadata.template.assign.ingress')" />
        <div class="card">
            <div class="card-header">
            </div>
            <div class="card-body">
                <user-listing :key="listingKey" @disableUser="disableUser" :users="formattedUsers" @editUser="editUser" @enableUser="enableUser"></user-listing>
                <div class="row text-center pagerRow">
                    <div class="col">
                        <Pager :meta='usersPageMeta' :height='height' />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Pager from "@components/Pager"
import { mapGetters, mapActions } from "vuex";


export default {
    components:{
        Pager
    },

    data () {
        return {
            listingKey: 0,
            metadataObject: { "metadata": {"dc": {}} },
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
            this.metadataObject.metadata = value.metadata;
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
