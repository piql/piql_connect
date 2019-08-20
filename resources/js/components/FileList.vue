<template>
    <div class="container-fluid">
        <ingest-filter-search class="mr-5"></ingest-filter-search>
        <div class="row plistHeader">
            <div class="col">{{$t('ingest.fileList.fileName')}}</div>
            <div class="col-3">{{$t('ingest.fileList.fileSize')}}</div>
            <div class="col-2 listActionItems"></div>
        </div>

        <file v-for="listitem in items" v-bind:item="listitem" v-bind:key="listitem.id"/>
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
            this.items = (await axios.get("/api/v1/ingest/bags/"+this.bagId+"/files")).data;
            console.log(this.items);
            console.log('TaskList component mounted.')
        },
    }
</script>
