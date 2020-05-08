<template>
    <div class="form-group row">
        <label v-if="showLabel" for="languagePicker" class="col-2 col-form-label-sm">
            {{label}}
        </label>
        <select v-model="selection" id="languagePicker" class="col-4" data-live-search="true" @change="selectionChanged">
          <option v-for="language in languages" v-bind:value="language.code">
            {{language.title}}
          </option>
       </select>
    </div>
</template>

<script>
import JQuery from 'jquery';
let $ = JQuery;

export default {
    async mounted() {
        this.languages = (await axios.get("/api/v1/system/languages")).data; // Populate language when user settings are empty, should not be needed
    },
    methods: {
        selectionChanged: function () {
            if (this.selection) {
                this.$emit('selectionChanged', this.selection);
            }
        }
    },
    data() {
        return {
            selection: '',
            languages: []
        };
    },
    props: {
        initialSelection: '',
        label: {
            type: String,
            default: ""
        }
    },
    watch: {
        async initialSelection(value) {
            this.languages = (await axios.get("/api/v1/system/languages")).data;
            this.selection = this.initialSelection;
            Vue.nextTick( () => {
                $('#languagePicker').selectpicker('refresh');
                $('#languagePicker').selectpicker('val', this.selection);
            });
        }
    },
    computed: {
        showLabel: function() {
            return this.label.length > 0;
        }
    }
}

</script>
