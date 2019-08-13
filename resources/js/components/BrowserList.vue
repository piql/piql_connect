<template>
    <div>
        <div class="row plistHeader mb-3">
            <div class="col-1"><input type="checkbox" class="checkbox" id="browserList"></div>
            <div class="col">{{$t('access.browse.itemInfo')}}</div>
            <div class="col-3">{{$t('access.browse.addToWorkflow')}}</div>
            <div class="col-1"><input type="checkbox" class="checkbox"></div>
            <div class="col-1"><input type="checkbox" class="checkbox"></div>
        </div>
        <div class="col">
            <browser-item v-for="item in items" v-bind:item="item" v-bind:key="item.id"></browser-item>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
export default {
    data() {
        return {
            items : {},
        }
    },

    props: {
        bagId: {
            type: String,
            default: ""
        },
    },

    async mounted() {
        let paginatedData = (await axios.get("/api/v1/ingest/bags/")).data;
        this.items = paginatedData.data;
    },
}
</script>
