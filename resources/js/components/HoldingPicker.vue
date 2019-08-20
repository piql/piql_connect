<template>
    <div class="form-group">
        <label for="holdingPicker" class="col-form-label-sm">
            {{$t('access.browse.selectHolding')}}
        </label>
        <select id="holdingPicker" class="form-control selectpicker" v-bind:value="state" v-on:change="holdingChanged">
            <option>Forsvarsmuseet</option>
            <option>Bergenhus</option>
            <option>Hjemmefrontmuseet</option>
            <option>Luftforsvarsmuseet</option>
            <option>Marinemuseet</option>
            <option>Oscarsborg</option>
            <option>Rustkammeret</option>
        </select>
    </div>

</template>

<script>
export default {
    mounted() {
    },
    data() {
        return {
            state: "Forsvarsmuseet"
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
