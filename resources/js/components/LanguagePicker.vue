<template>
    <div class="form-group">
        <label v-if="showLabel" for="languagePicker" class="col-form-label-sm">
            {{label}}
        </label>
        <select v-model="selection" id="languagePicker" class="w-100" data-live-search="true" @change="selectionChanged($event.target.value)">
          <option v-for="language in allLanguages" v-bind:value="language.code">
            {{language.title}}
          </option>
       </select>
    </div>
</template>

<script>
import JQuery from 'jquery';
let $ = JQuery;
import selectpicker from 'bootstrap-select';

export default {
    mounted() {
        this.selection = this.initialSelection;
    },
    methods: {
        selectionChanged: function (value) {
          this.$emit('selectionChanged', value);
        }
    },
    data() {
        return {
            selection: '' 
        };
    },
    props: {
        initialSelection: '',
        languages: Array,
        label: {
            type: String,
            default: ""
        }
    },
    watch: {
        languages: function(val) {
            Vue.nextTick( () => {
                $('#languagePicker').selectpicker('refresh');
            });
        },
    },
    computed: {
        showLabel: function() {
            return this.label.length > 0;
        },
        allLanguages: function() {
            return this.languages;
        }
    }
}

</script>
