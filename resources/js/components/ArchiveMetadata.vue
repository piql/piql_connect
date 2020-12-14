<template>
    <div>
        <b-modal id="edit-collection-metadata"
            size="xl" :centered=true
            :title="$t('admin.metadata.template.edit.title')"
            :header-class="['d-ruby','text-center']"
            :no-fade=true title-class="h3"
            :ok-title="$t('settings.collections.assignMeta')"
            :cancel-title="$t('admin.metadata.template.edit.cancelButton')"
            @ok="updateDefaultMetadataTemplate"
            @cancel="cancelUpdate"
        >
            <template v-slot:modal-header>
                <b-dropdown id="templateSelector" ref="templateSelector" text="Select from templates">
                    <b-dropdown-header>
                        Replace current fields with fields from template.
                    </b-dropdown-header>
                    <b-dropdown-item-button v-for="template in templates" :key="template.id" @click="selectTemplate(template.id)">
                        {{ template.metadata.dc.title }}
                    </b-dropdown-item-button>
                        <!--b-form-group class="w-100" :label="$t('settings.collections.template')" for="templateSelector" >
                            <b-form-select v-for="template in templates" :key="template.id" :value="template.id">
                                {{ template.metadata.dc.title }}
                            </b-form-input>
                        </b-form-group-->
                </b-dropdown>
            </template>

        <b-form v-if="collection">
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
                    v-model="collection.defaultMetadataTemplate.dc[schemeItem.name]"
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
        collectionId: {
            type: Number,
            default: 0,
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
            selectedTemplate: '',
            collection: { 'defaultMetadataTemplate': {'dc': {}}},
            backup:  { 'defaultMetadataTemplate': {'dc': {}}},
        }
    },
    async mounted() {
        await this.fetchTemplates();
    },
    watch: {
        collectionId( id ) {
            this.collection = this.collectionById( this.collectionId );
            this.backup = JSON.parse(JSON.stringify( this.collection ));
        }
    },
    computed: {
        ...mapGetters(['collections', 'collectionById', 'firstAccount', 'templates', 'templateById']),
        defaultMetadataTemplate: {
            get(){
                return this.collection.defaultMetadataTemplate;
            },
            set( template ) {
                this.collection.defaultMetadataTemplate = template;
            }
        },
    },
    methods: {
        ...mapActions(['updateCollectionMetadata', 'fetchCollections', 'fetchAccounts', 'fetchTemplates']),

        selectTemplate( templateId ) {
            let template = JSON.parse( JSON.stringify( this.templateById( templateId ) ) );
            this.collection.defaultMetadataTemplate.dc = template.metadata.dc;
        },
        async cancelUpdate(){
            this.collection = JSON.parse(JSON.stringify(this.backup));
        },
        async updateDefaultMetadataTemplate(){
            const payload = {
                accountId: this.firstAccount.id,
                collection: this.collection
            };
            this.updateCollectionMetadata( payload ).then( result => {
                this.backup = JSON.parse(JSON.stringify( this.collection ));
                this.successToast(
                    this.$t('settings.collections.toast.addingCollectionMeta'),
                    this.$t('settings.collections.toast.addingCollectionMeta') + ' ' + this.collectionId
                );
                this.$emit('disableMetaForm');
            }).catch( error => {
                console.error(error);
            });
        },
    }
}
</script>
