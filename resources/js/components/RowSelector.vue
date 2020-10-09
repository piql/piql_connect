<template>
  <div class="ml-3">
        <div class="form-group row">
            <label v-if="showLabel" for="rowselect" class="col-form-label-sm">
                {{rowlabel}}
            </label>
            <input id="rowselect" @change="rowCountUpdated" @keypress="rowCountUpdated" class="col-3 form-control" type="number" v-model="rowselection" required > 
            &nbsp;
        </div>
    </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
export default {
    methods: {
        ...mapActions(['updateTableRowCount', 'fetchUserSettings']),
        rowCountUpdated() {
            this.clearDeferFunc();
            this.defer.func = setTimeout(() => {
                this.updateTableRowCount(this.rowselection).then(() => {
                    this.successToast(this.$t('settings.settings.rowslabel'), this.$t('settings.settings.rowslabel.updatedTo') + ' ' + this.rowselection);
                })
                .catch(error => {
                    this.errorToast(this.$t('settings.settings.rowslabel'), JSON.stringify(error));
                    this.rowselection = this.userTableRowCount;
                })
            }, this.defer.timeout);
        },
        clearDeferFunc() {
            if(this.defer.func != null) clearTimeout(this.defer.func);
            this.defer.func = null;
        }
    },
    data() {
        return {
            rowselection: 8, //default is 8
            defer: {
                func: null,
                timeout: 700
            },
        };
    },
    mounted() {
        this.fetchUserSettings().then(() => {
            this.rowselection = this.userTableRowCount
        })
    },
    props: {
        rowlabel: {
            type: String,
            default: ""
        }
    },
    computed: {
        ...mapGetters(['userTableRowCount']),
        showLabel: function() {
            return this.rowlabel.length > 0;
        }
    }

}
</script>

<style>

</style>