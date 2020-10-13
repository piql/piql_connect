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
    mounted() {
        this.fetchLanguages();
        this.fetchUserSettings().then(() => {
            this.selection = this.currentLanguage;  
        });
    },
    methods: {
        ...mapActions(['changeLanguage', 'fetchUserSettings', 'fetchLanguages']),
        selectionChanged: function () {
            this.$i18n.locale = this.selection;
            this.changeLanguage(this.selection);
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
    computed: {
        ...mapGetters(['userLanguages','currentLanguage']),
        showLabel: function() {
            return this.label.length > 0;
        },
    }
}

</script>
