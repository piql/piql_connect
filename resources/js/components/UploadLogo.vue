<template>
  <div class="ml-3">
        <div class="form-group row">
            <label v-if="showLabel" for="logo" class="col-form-label-sm">
                {{logolabel}}
            </label>
            <input id="logo" class="col-3 form-control" type="file"> 
            &nbsp;
            <button class="col-1 btn-xs btn" style="color:white"><i class="fa fa-upload"></i></button>
        </div>
    </div>
</template>

<script>
export default {
     methods: {
        
    },
    data() {
        return {
        };
    },
    props: {
        logolabel: {
            type: String,
            default: ""
        }
    },
    computed: {
        showLabel: function() {
            return this.logolabel.length > 0;
        }
    }

}
</script>

<style>

</style>