<template>
    <div class="form-group">
        <label v-if="showLabel" for="holdingPicker" class="col-form-label-sm">
            {{label}}
        </label>
        <select v-model="selection" id="holdingPicker" class="w-100" data-live-search="true" @change="changed($event.target.value)">
          <option v-for="holding in holdings" v-bind:value="holding.id">
            {{holding.title}}
          </option>
       </select>
    </div>

</template>

<script>
import Select from 'bootstrap-select';
export default {
    mounted() {
        this.selection = this.initialSelectedArchive;

    },
    methods: {
        changed: function (value) {
            this.selected(Number.parseInt(value));
        }
    },
    data() {
        return {
            selection: 0
        };
    },
    props: {
        initialSelectedArchive: Number,
        selected: Function,
        holdings: Array,
        label: {
            type: String,
            default: ""
        }
    },
    computed: {
        showLabel: function() {
            return this.label.length > 0;
        },
        /*selection: function() {
            return this.initialSelectedArchive;
        }*/

    }
}

</script>
