<template>
    <div class="form-group">
        <label for="locationPicker" class="col-form-label-sm">
            {{$t('access.browse.selectLocation')}}
        </label>
        <select v-model="selectedLocation"id="locationPicker" class="form-control selectpicker" v-on:change="locationChanged">
            <option v-for="location in locations" v-bind:value="location.value">
                {{location.name}}
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
            selectedLocation: this.initialSelectedLocation
        }
    },
    methods: {
        locationChanged: function (event) {
            this.$emit('locationSelectionChanged', this.selectedLocation);
        }
    },
    props: {
        initialSelectedLocation: String,
        locationSelected: Function,
        locations: Array
    }

}
</script>
