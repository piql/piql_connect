<template>
    <div>
        <b-form @submit="addMetadata">

            <div class="form-group">
                <label>{{$t('settings.archives.template')}}</label>
                <select class="form-control" v-model="selection">
                    <option v-for="template in listTemplates" :key="template.id" :value="template.id">
                        {{ template.metadata.dc.title }}
                    </option>
                </select>
            </div>

            <b-form-group v-for="schemeItem in schemes[0].fields" 
            :key="schemeItem.id" :id="schemeItem.label.toLowerCase()" 
            :label="schemeItem.label" :label-for="schemeItem.label.toLowerCase()">
                <b-form-input v-if="schemeItem.name === 'identifier'"
                :id="schemeItem.label.toLowerCase()"
                class="mb-4"
                v-model="form.metadata[schemeItem.name]"
                :type="schemeItem.type"
                :disabled='true'
                
                ></b-form-input>

                <b-form-input v-else
                :id="schemeItem.label.toLowerCase()"
                class="mb-4"
                v-model="form.metadata[schemeItem.name]"
                :type="schemeItem.type"
                >
                </b-form-input>
            </b-form-group>
            <b-button type="submit" variant="primary">{{$t('settings.archives.assignMeta')}}</b-button>

        </b-form>

    </div>
</template>

<script>
import {mapGetters, mapActions} from "vuex"
export default {
    props:{
        archiveId: {
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
            form: {"metadata": {}},
            selection: ''
        }
    },
    watch:{
        selection(val){
            if(val && val != ''){
                //when selction changes, we prefill the metadata
                let template = this.templates.filter(single => single.id === val);
                let data = { 
                    id: this.archiveId,
                    metadata:  {
                        metadata: template[0].metadata.dc
                    } 
                };

                this.addArchiveMetadata(data);
                //after adding, prefill the  form
                let archive = this.retrievedArchives.filter(single => single.id === this.archiveId)
                if(archive[0].metadata){
                    this.form.metadata = archive[0].metadata;

                }

            }else{
                this.form.metadata = {}
                
            }

        }

    },
    watch: {
        'archiveId': 'initMetadata',
    },
    async mounted(){
        await this.fetchAccounts();
        await this.fetchArchives( this.firstAccount.id ); //TODO: Actual account handling
    },
    computed: {
        ...mapGetters(['accounts','archives']),
        firstAccount(){
            return this.accounts[0] || 0;
        },
    },
    methods: {
        ...mapActions(['addArchiveMetadata', 'fetchArchives', 'fetchAccounts']),
        async initMetadata() {
            if (!this.archiveId ) return;
            let archive = this.archives.find( archive => archive.id === this.archiveId );
            if( !archive || !archive.metadata  || !archive.metadata.metadata.dc ) {
                this.form = { metadata: { dc: {} } };
                return;
            }
            let dc = JSON.parse( JSON.stringify( archive.metadata.metadata.dc ) );
            this.form.metadata =  { dc };
        },
        async addMetadata(e){
            e.preventDefault();

            const data = {
                archiveId: this.archiveId,
                accountId: this.firstAccount.id,
                metadata: this.form
            };
            await this.addArchiveMetadata( data );
            this.successToast(
                this.$t('settings.archives.toast.addingArchiveMeta'),
                this.$t('settings.archives.toast.addingArchiveMeta') + ' ' + this.archiveId
            );
            this.$emit('disableMetaForm');
        }
    }
}
</script>
