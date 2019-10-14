<template>
    <div class="form-group">
        <label v-if="showLabel" for="archivePicker" class="col-form-label-sm">
            {{label}}
        </label>
        <select v-model="selection" id="archivePicker" class="w-100" data-live-search="true" @change="selectionChanged($event.target.value)">
          <option v-for="archive in archives" v-bind:value="archive.uuid">
            {{archive.title}}
          </option>
       </select>
    </div>

</template>

<script>
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
        archives: Array,
        label: {
            type: String,
            default: ""
        }
    },
    computed: {
        showLabel: function() {
            return this.label.length > 0;
        },
    }
}

</script>
