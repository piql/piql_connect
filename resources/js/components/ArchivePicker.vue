<template>
    <div class="form-group">
        <label v-if="showLabel" for="archivePicker" class="col-form-label-sm">
            {{label}}
        </label>
        <select v-model="selection" id="archivePicker" class="w-100" data-live-search="true" @change="selectionChanged($event.target.value)">
          <option v-for="archive in archivesWithWildcard" v-bind:value="archive.uuid">
            {{archive.title}}
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
        useWildCard: false,
        wildCardLabel: {
            type: String,
            default: "All"
        },
        initialSelection: '',
        archives: Array,
        label: {
            type: String,
            default: ""
        }
    },
    watch: {
        archives: function(val) {
            Vue.nextTick( () => {
                $('#archivePicker').selectpicker('refresh');
            });
            if( this.useWildCard ) {
                $('#archivePicker').selectpicker('val', this.wildCardLabel);
            }

        },
    },
    computed: {
        showLabel: function() {
            return this.label.length > 0;
        },
        archivesWithWildcard: function() {
            return this.useWildCard
                ?  [{'id' : 0, 'title': this.wildCardLabel}, ...this.archives]  /* Push a wildcard element ("All") at the start of the list */
                : this.archives;
        }
    }
}

</script>
