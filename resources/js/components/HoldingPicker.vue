<template>
    <div class="form-group">
        <label for="holdingPicker" class="col-form-label-sm">
            {{$t('access.browse.selectHolding')}}
        </label>
        <select v-model="selectedHolding" id="holdingPicker" class="form-control selectpicker" v-on:change="holdingChanged">
          <option v-for="holding in holdings" v-bind:value="holding.value">
            {{holding.name}}
          </option>
       </select>
    </div>

</template>

<script>
export default {
    mounted() {
    },
    data() {
        return {
            selectedHolding: this.initialSelectedHolding
        }
    },
    methods: {
        holdingChanged: function (event) {
            this.$emit('holdingSelectionChanged', this.selectedHolding);
        }
    },
    props: {
        initialSelectedHolding: String,
        holdingSelected: Function,
        holdings: Array
    }
}

</script>
