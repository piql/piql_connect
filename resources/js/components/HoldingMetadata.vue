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
                
                ></b-form-input>
            </b-form-group>
            <b-button type="submit" variant="primary">{{$t('settings.holdings.assignMeta')}}</b-button>
            
        </b-form>

    </div> 
</template>

<script>
import {mapGetters, mapActions} from "vuex"
export default {
    props:{
        holdingId: Number,
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
    async mounted(){
        let holding = this.retrievedHoldings.filter(single => single.id === this.holdingId)
        if(holding[0].metadata){
            this.form.metadata = holding[0].metadata;

        }

    },
     watch:{
        selection(val){
            if(val && val != ''){
                //when selction changes, we prefill the metadata
                let template = this.templates.filter(single => single.id === val);
                let data = { 
                    id: this.holdingId,
                    metadata:  {
                        metadata: template[0].metadata.dc
                    } 
                };

                this.addHoldingMetadata(data);
                //after adding, prefill the  form
                let holding = this.retrievedHoldings.filter(single => single.id === this.holdingId)
                if(holding[0].metadata){
                    this.form.metadata = holding[0].metadata;

                }



            }else{
                this.form.metadata = {}
                
            }
     

        }

    },
    computed:{
        ...mapGetters(['retrievedHoldings','templates']),
        listTemplates(){
            return this.templates
                ? [{'id' : '', 'metadata':{'dc': {'title':'Nothing Selected'}}}, ...this.templates ]
                    : null;
        }

    },
    methods:{
        ...mapActions(['addHoldingMetadata']),
        fieldId(type, name){
            return `${type}-${name}`.replace(/\s/g,'');     /* Strip all whitespace from fieldId */
        },
        addMetadata(e){
            e.preventDefault();

            let data = {
                id: this.holdingId,
                metadata: this.form
            }

            this.addHoldingMetadata(data)


            this.successToast(
                this.$t('settings.holdings.toast.addingHoldingMeta'), 
                this.$t('settings.holdings.toast.addingHoldingMeta') + ' ' + this.holdingId
            );

            this.$emit('disableMetaForm');

        }
    }


}
</script>
