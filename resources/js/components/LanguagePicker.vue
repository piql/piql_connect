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
        this.fetchLanguages(); // Populate language when user settings are empty, should not be needed
    },
    methods: {
        ...mapActions(['fetchLanguages']),
        selectionChanged: function () {
            if (this.selection) {
                this.$emit('selectionChanged', this.selection);
            }
        }
    },
    data() {
        return {
            selection: ''
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
            this.fetchLanguages();
            this.selection = this.initialSelection;
            Vue.nextTick( () => {
                $('#languagePicker').selectpicker('refresh');
                $('#languagePicker').selectpicker('val', this.selection);
            });
        }
    },
    computed: {
        ...mapGetters(['userLanguages']),
        showLabel: function() {
            return this.label.length > 0;
        }
    }
}

</script>
