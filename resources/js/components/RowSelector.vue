<template>
  <div class="ml-3">
        <div class="form-group row">
            <label v-if="showLabel" for="rowselect" class="col-form-label-sm">
                {{rowlabel}}
            </label>
            <input id="rowselect" class="col-3 form-control" type="number" v-model="rowselection" required> 
            &nbsp;
            <button class="col-1 btn-xs btn" style="color:white"><i class="fa fa-check"></i></button>
        </div>
    </div>
</template>

<script>
export default {
     methods: {
        
    },
    data() {
        return {
            rowselection: 8 //default is 8
        };
    },
    props: {
        rowlabel: {
            type: String,
            default: ""
        }
    },
    computed: {
        showLabel: function() {
            return this.rowlabel.length > 0;
        }
    }

}
</script>

<style>

</style>