<template>
    <div class="form-group">
        <label v-if="showLabel" for="holdingPicker" class="col-form-label-sm">
            {{label}}
        </label>
        <select v-model="selection" id="holdingPicker" class="w-100" data-live-search="true" @change="selectionChanged($event.target.value)">
          <option v-for="holding in holdings" v-bind:value="holding.title">
            {{holding.title}}
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
            selection: 'Documents' 
        };
    },
    props: {
        initialSelection: '',
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
    }
}

</script>
