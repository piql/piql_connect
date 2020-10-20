<template>
    <div>
        <b-modal id="edit-holding-metadata"
            size="xl" :centered=true
            :title="$t('admin.metadata.template.edit.title')"
            :header-class="['d-ruby','text-center']"
            :no-fade=true title-class="h3"
            :ok-title="$t('settings.archives.assignMeta')"
            :cancel-title="$t('admin.metadata.template.edit.cancelButton')"
            @ok="updateHoldingDefaultMetadataTemplate"
            @cancel="cancelUpdate" >

            <template v-slot:modal-header>
                <b-dropdown id="templateSelector" ref="templateSelector" text="Select from templates">
                    <b-dropdown-header>
                        Replace current fields with fields from template.
                    </b-dropdown-header>
                    <b-dropdown-item-button v-for="template in templates" :key="template.id" @click="selectTemplate(template.id)">
                        {{ template.metadata.dc.title }}
                    </b-dropdown-item-button>
                </b-dropdown>
            </template>

            <b-form v-if="holding">
                <b-form-group
                    key="identifier" id="identifier"
                    label="Identifier" label-for="identifier">
                    <b-form-input
                        id="identifier"
                        class="mb-4"
                        v-model="defaultMetadataTemplate.dc.identifier"
                        type="text"
                        :disabled='true' />
                </b-form-group>

                <b-form-group v-if="schemeItem.name !== 'identifier'" v-for="schemeItem in schemes[0].fields"
                    :key="schemeItem.id" :id="schemeItem.label.toLowerCase()"
                    :label="schemeItem.label" :label-for="schemeItem.label.toLowerCase()">
                    <b-form-input
                        :id="schemeItem.label.toLowerCase()"
                        class="mb-4"
                        v-model="holding.defaultMetadataTemplate.dc[schemeItem.name]"
                        :type="schemeItem.type" />
                </b-form-group>
            </b-form>
        </b-modal>
    </div>
</template>

<script>
import {mapGetters, mapActions} from "vuex"
export default {
    props:{
        accountId: {
            type: Number,
            default: 0
        },
        archiveId: {
            type: Number,
            default: 0,
        },
        originalHolding: {
            type: Object,
            value: null
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
                            {"name" : "title",       "label" : "Title",       "type": "text"},
                            {"name" : "description", "label" : "Description", "type": "text"},
                            {"name" : "creator",     "label" : "Creator",     "type": "text"},
                            {"name" : "subject",     "label" : "Subject",     "type": "text"},
                            {"name" : "publisher",   "label" : "Publisher",   "type": "text"},
                            {"name" : "contributor", "label" : "Contributor", "type": "text"},
                            {"name" : "date",        "label" : "Date",        "type": "date"},
                            {"name" : "type",        "label" : "Type",        "type": "text"},
                            {"name" : "format",      "label" : "Format",      "type": "text"},
                            {"name" : "source",      "label" : "Source",      "type": "text"},
                            {"name" : "language",    "label" : "Language",    "type": "text"},
                            {"name" : "relation",    "label" : "Relation",    "type": "text"},
                            {"name" : "coverage",    "label" : "Coverage",    "type": "text"},
                            {"name" : "rights",      "label" : "Rights",      "type": "text"},
                            {"name" : "identifier",  "label" : "Identifier",  "type": "text"},
                        ]
                    }
                ];
            },
        }
    },
    data(){
        return {
            holding: { 'defaultMetadataTemplate': {'dc': {}}},
            backup:  { 'defaultMetadataTemplate': {'dc': {}}},
        }
    },
    async mounted() {
        await this.fetchTemplates();
    },
    watch: {
        originalHolding( value ) {
            this.holding = JSON.parse( JSON.stringify( this.originalHolding ) );
        }
    },
    computed: {
        ...mapGetters(['templates', 'templateById', 'archiveById']),
        defaultMetadataTemplate: {
            get(){
                return this.holding.defaultMetadataTemplate;
            },
            set( template ) {
                this.holding.defaultMetadataTemplate = template;
            }
        },
    },
    methods: {
        ...mapActions(['updateHoldingMetadata', 'fetchArchives', 'fetchAccounts', 'fetchTemplates']),

        selectTemplate( templateId ) {
            let template = JSON.parse( JSON.stringify( this.templateById( templateId ) ) );
            this.holding.defaultMetadataTemplate.dc = template.metadata.dc;
        },
        async cancelUpdate(){
            this.holding = JSON.parse(JSON.stringify(this.backup));
        },
        async updateHoldingDefaultMetadataTemplate(){
            const payload = {
                accountId: this.accountId,
                archiveId: this.archiveId,
                holding: this.holding
            };
            this.updateHoldingMetadata( payload ).then( result => {
                this.backup = JSON.parse(JSON.stringify( this.holding ));
                this.successToast(
                    this.$t('settings.holdings.toast.addingArchiveMeta'),
                    this.$t('settings.holdings.toast.addingArchiveMeta') + ' ' + this.holding.id
                );
                this.$emit('disableMetaForm');
            }).catch( error => {
                console.error(error);
            });
        },
    }
}
</script>
