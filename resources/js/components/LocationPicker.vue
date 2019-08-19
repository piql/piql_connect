<template>
    <div class="form-group">
        <label for="locationPicker" class="col-form-label-sm">
            {{$t('access.browse.selectLocation')}}
        </label>
        <select id="locationPicker" class="form-control selectpicker w-auto" v-bind:value="state" v-on:change="locationChanged">
            <option>Online</option>
            <option>Offline</option>
        </select>
    </div>

</template>

<script>
export default {
    mounted() {
    },
    data() {
        return {
            state: "Online"
        }
    },
    methods: {
        locationChanged: function (event) {
            this.$emit('locationSelected', this.state);
        }
    },
    props: {
        locationSelected: {
            type: Function
        }
    }

}
</script>
