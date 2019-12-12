<template>
        <form>
            <div class="form-row mt-2 mb-5">
                <div class="col-sm-2 mr-5">
                    <label class="col-form-label-sm" for="fromDate">{{$t('ingest.processing.from')}}</label>
                    <input type="date" id="fromDate" placeholder="dd/mm/yyyy" class="form-control">
                </div>
                <div class="col-sm-2 mr-5">
                    <label class="col-form-label-sm" for="toDate">{{$t('ingest.processing.to')}}</label>
                    <input type="date" id="toDate" placeholder="dd/mm/yyyy" class="form-control">
                </div>
                <div class="col-sm-3 mr-5" v-if="filters">
                    <label class="col-form-label-sm" for="statusFilter">{{$t('ingest.processing.statusFilter')}}</label>
                    <select id="statusFilter" class="form-control selectpicker">
                        <option v-for="filter in filters" v-bind:key="filter">{{ $t(filter)}}</option>
                    </select>
                </div>
                <div class="col">
                    <label class="col-form-label-sm" for="searchInput">{{$t('Search')}}</label>
                    <div class="input-group">
                        <div class="input-group addon">
                            <input id="searchContents" type="text" class="form-control" style="border-radius: 4px">
                            <span class="input-group-addon">
                                <i class="fas fa-search search-icon-inline p-2"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </form>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
       }
    },
    props: {
        filters: Array
    },
    computed: {
    },
    methods: {
    },
    created() {
    },
    beforeDestroy(){
    },
    async mounted() {
    },
}
</script>
