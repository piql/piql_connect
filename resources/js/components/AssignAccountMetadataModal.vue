<template>
    <b-modal id="assign-account-metadata"
        size="xl" :centered=true 
        :title="$t('admin.metadata.template.edit.title')"
        :header-class="['d-ruby','text-center']"
        :no-fade=true title-class="h3"
        :ok-title="$t('admin.metadata.template.edit.saveButton')"
        :cancel-title="$t('admin.metadata.template.edit.cancelButton')"
        @ok="assignAccountMetadata"
    >
        <template v-slot:modal-title>
            <h4>{{$t('settings.user.assignAccountMetadata.title')}}</h4>
        </template>

        <b-row>
            <b-col>
                <div>{{$t('settings.admin.accountMetadataUser.fullname')}}</div>
            </b-col>
            <b-col>
                <div>{{$t('settings.admin.accountMetadataUser.username')}}</div>
            </b-col>
        </b-row>
        <b-row>
            <b-col>
                <div>{{userAccount.full_name}}</div>
            </b-col>
            <b-col>
                <div>{{userAccount.username}}</div>
            </b-col>
        </b-row>

        <b-row class="mt-4">
            <b-col>
                <div class="form-group w-100">
                    <label for="templatePicker" class="col-form-label-sm">{{$t('settings.admin.assignMetadataDropdownLabel')}}</label>
                    <select v-model="selectedTemplateId" id="templatePicker" class="form-control w-100" data-live-search="true" :data-none-selected-text="$t('settings.admin.assignMetadataDropdownPlaceholder')">
                        <option v-for="template in templates" v-bind:value="template.id">
                            {{template.metadata.dc.title}}
                        </option>
                    </select>
                </div>
            </b-col>
        </b-row>

        <b-row v-for="scheme in schemes" :key="scheme.id">
            <b-form inline v-if="userMetadataInstance">
                <b-col class="form-group">
                    <b-form-row v-for="schemeItem in scheme.fields" :key="schemeItem.id" class="w-100">
                        <label :for="fieldId(scheme.type, schemeItem.name)" class="small" >{{schemeItem.label}}</label>
                        <b-col class="m-1 w-100">
                            <b-form-input class="w-100"
                                :id="fieldId(scheme.type, schemeItem.name)"
                                v-model="activeUserMetadataTemplate.metadata.dc[schemeItem.name]"
                                v-bind:readOnly="false"
                            />
                        </b-col>
                    </b-form-row>
                </b-col>
            </b-form>
        </b-row>

    </b-modal>


</template>

<script>

import { mapActions, mapGetters } from "vuex";

export default {
    components: {},

    mixins: [],

    data () {
        return {
            selectedTemplateId: null,
            userMetadataInstance: null,
        }
    },

    props: {
        userAccount: {
            type: Object,
            default: () => {
                return {
                    full_name: "",
                    username: ""
                };
            }
        },
        schemes: {
            /* Later on, this should also arrive from an api */
            type: Array,
            default : () => {
                return [
                    {
                        "type": "Dublin Core v1.1",
                        "fields":
                        [
                            {"name" : "identifier",  "label" : "Identifier",  "type": "text"},
                            {"name" : "title",       "label" : "Title",       "type": "text"},
                            {"name" : "creator",     "label" : "Creator",     "type": "text"},
                            {"name" : "subject",     "label" : "Subject",     "type": "text"},
                            {"name" : "description", "label" : "Description", "type": "text"},
                            {"name" : "publisher",   "label" : "Publisher",   "type": "text"},
                            {"name" : "contributor", "label" : "Contributor", "type": "text"},
                            {"name" : "date",        "label" : "Date",        "type": "text"},
                            {"name" : "type",        "label" : "Type",        "type": "text"},
                            {"name" : "format",      "label" : "Format",      "type": "text"},
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
    },
    watch: {
        selectedTemplateId( value ) {
            this.userMetadataInstance = JSON.parse(JSON.stringify(this.selectedTemplate)); //When the user selects a template, copy it to the current template instance
        },
        userAccount( value ) {
            this.selectedTemplateId = null;
            this.userMetadataInstance = null;
            this.updateActiveTemplateFromStore();
        },
        userMetadataTemplates( value ) {
            this.userMetadataInstance = JSON.parse( JSON.stringify( this.userMetadataTemplateByUserId( this.userAccount.id ) ));
        },
    },
    computed: {
        ...mapGetters(['templates', 'userMetadataTemplateByUserId', 'userMetadataTemplates']),
        activeUserMetadataTemplate: {
            get: function() {
                return this.userMetadataInstance;
            },
            set: function(value){
                this.userMetadataInstance = value;
            }
        },
        selectedTemplate(){
            let selectedTemplateId = this.selectedTemplateId;
            if(!selectedTemplateId){
                updateActiveTemplateFromStore();
                return this.userMetadataTemplate; 
            }
            return this.templates.find( (t) => t.id == selectedTemplateId );
        }
    },
    methods: {
        fieldId(type, name){
            return `${type}-${name}`.replace(/\s/g,'');     /* Strip all whitespace from fieldId */
        },

        ...mapActions(['fetchMetadataTemplates', 'setAccountMetadataTemplate', 'createEmptyTemplateWithUserAsCreator']),
        assignAccountMetadata( user, meta ){
            this.userMetadataInstance.owner_id = this.userAccount.id;
            this.setAccountMetadataTemplate( this.userMetadataInstance );
            Vue.nextTick( () => this.updateActiveTemplateFromStore() );
        },
        updateActiveTemplateFromStore(){
            let existingTemplate = this.userMetadataTemplateByUserId( this.userAccount.id );
            if( existingTemplate ){
                this.userMetadataInstance = JSON.parse( JSON.stringify( existingTemplate ) );
                return;
            }
            this.createEmptyTemplateWithUserAsCreator( this.userAccount );
        },
        cancel(){
            this.userMetadataInstance = null;
        }
    }
};
</script>
