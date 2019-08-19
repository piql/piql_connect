<template>
    <div class="form-group">
        <label for="holdingPicker" class="col-form-label-sm">
            {{$t('access.browse.selectHolding')}}
        </label>
        <select id="holdingPicker" class="form-control selectpicker" v-bind:value="state" v-on:change="holdingChanged">
            <option>Space Exploration</option>
            <option>Customer relations</option>
            <option>Attic</option>
        </select>
    </div>

</template>

<script>
export default {
    mounted() {
    },
    data() {
        return {
            state: "Space Exploration"
        }
    },
    methods: {
        holdingChanged: function (event) {
            this.$emit('holdingSelected', this.state);
        }
    },
    props: {
        holdingSelected: {
            type: Function
        }
    }

}
</script>
