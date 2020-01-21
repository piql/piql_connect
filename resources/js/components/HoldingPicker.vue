<template>
    <div class="form-group">
        <label v-if="showLabel" for="holdingPicker" class="col-form-label-sm">
            {{label}}
        </label>
        <select v-model="selection" id="holdingPicker" class="form-control" data-live-search="true" @change="selectionChanged($event.target.value)">
          <option v-for="holding in holdingsWithWildcard" v-bind:value="holding.title">
            {{holding.title}}
          </option>
       </select>
    </div>

</template>

<script>
    import JQuery from 'jquery';
    let $ = JQuery;
    import selectpicker from 'bootstrap-select';
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
            useWildCard: false,
            selectionDisabled: false,
            wildCardLabel: {
                type: String,
                default: "All"
            },
            initialSelection: '',
            holdings: Array,
            label: {
                type: String,
                default: ""
            }
        },
        watch: {
            holdings: function(val) {
                Vue.nextTick( () => {
                    $('#holdingPicker').selectpicker('refresh');
                    if( this.useWildCard ) {
                        $('#holdingPicker').selectpicker('val', this.wildCardLabel);
                    }
                });
            },
            selectionDisabled: function(val) {
                if(val) {
                    Vue.nextTick( () => {
                        $('#holdingPicker').selectpicker('setStyle','collapse','add');
                    });
                } else {
                    Vue.nextTick( () => {
                        $('#holdingPicker').selectpicker('setStyle','collapse','remove');
                    });
                }
            },
        },
        computed: {
            showLabel: function() {
                return this.label.length > 0;
            },
            holdingsWithWildcard: function() {
                return this.useWildCard
                    ? [{'id' : 0, 'title': this.wildCardLabel}, ...this.holdings]  /* Push a wildcard element ("All") at the start of the list */
                    : this.holdings;
            }
        }
    }

</script>
