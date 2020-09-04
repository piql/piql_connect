<template>
    <div>
        <b-form>
            <b-form-group v-for="schemeItem in schemes[0].fields" 
            :key="schemeItem.id" :id="schemeItem.label.toLowerCase()" 
            :label="schemeItem.label" :label-for="schemeItem.label.toLowerCase()">
                <b-form-input
                :id="schemeItem.label.toLowerCase()"
                class="mb-4"
                v-model="form.metadata[schemeItem.name]"
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
        archiveId: Number,
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
            form: {
                "metadata": {} //where you put the default values

            }
        }
    },
    methods:{
        fieldId(type, name){
            return `${type}-${name}`.replace(/\s/g,'');     /* Strip all whitespace from fieldId */
        }
    }


}
</script>
