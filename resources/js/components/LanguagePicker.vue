<template>
    <div class="ml-3">
        <div class="form-group row">
            <label v-if="showLabel" for="languagePicker" class="col-form-label-sm">
                {{label}}
            </label>
            <select v-model="selection" required id="languagePicker" class="col-3 form-control" data-live-search="true" @change="selectionChanged">
                <option v-for="language in userLanguages" :key="language.id" v-bind:value="language.code">
                    {{language.title}}
                </option>
            </select>
        </div>
    </div>
</template>

<script>
import JQuery from 'jquery';
import { mapGetters, mapActions } from "vuex";
let $ = JQuery;

export default {
    async mounted() {
        this.fetchUserSettings();
        this.fetchLanguages(); // Populate language when user settings are empty, should not be needed
        
         $('#languagePicker').selectpicker('refresh');

        Vue.nextTick( () => { 
           Vue.nextTick(()=> {
                this.selection = this.currentLanguage;
                $('#languagePicker').selectpicker('val', this.selection);
           })
        });
   

    },
    updated(){
        
        $('#languagePicker').selectpicker('refresh');

        Vue.nextTick( () => { 
            this.selection = this.currentLanguage;
            $('#languagePicker').selectpicker('val', this.selection);
        });


    },
    methods: {
        ...mapActions(['fetchLanguages','fetchUserSettings','changeLanguage']),
        selectionChanged: function () {
           
            this.changeLanguage(this.selection);
            Vue.nextTick(() => {
                Vue.nextTick(()=> {
                    this.$router.go(0);
                })     
            })
            
        },
    },
    data() {
        return {
            selection: ''
        };
    },
    props: {
        label: {
            type: String,
            default: ""
        }
    },
    watch: {
        currentLanguage(value, oldvalue) {

            if(value){
                this.fetchLanguages();
                this.selection = value;

                $('#languagePicker').selectpicker('refresh');

                Vue.nextTick( () => { 
                    $('#languagePicker').selectpicker('val', value);
                });
            }else {
                this.selection = oldvalue
            }
        }
    },
    computed: {
        ...mapGetters(['userLanguages','currentLanguage']),
        showLabel: function() {
            return this.label.length > 0;
        },
    }
}

</script>
