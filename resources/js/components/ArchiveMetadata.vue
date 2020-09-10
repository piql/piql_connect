<template>
    <div>
        <b-form @submit="addMetadata" v-if="form.metadata.dc" >
            <b-form-group v-for="schemeItem in schemes[0].fields"
                :key="schemeItem.id" :id="schemeItem.label.toLowerCase()"
                :label="schemeItem.label" :label-for="schemeItem.label.toLowerCase()">
                <b-form-input
                    :id="schemeItem.label.toLowerCase()"
                    class="mb-4"
                    v-model="form.metadata.dc[schemeItem.name]"
                    :type="schemeItem.type"

                ></b-form-input>
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
                        ]
                    }
                ];
            },
        }
    },
    data(){
        return {
            form: { "metadata": {} }
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
