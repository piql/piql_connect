<template>
    <div>
        <form>
        <div class="row listFilter">
                    <label for="fromDate">{{$t('ingest.taskList.from')}}</label>
                    <input type="date" name="fromDate" placeholder="DDMMYY">
                    <label for="toDate">{{$t('ingest.taskList.to')}}</label>
                    <input type="date" name="toDate" placeholder="DDMMYY">
                    <select name="status">
                        <option style="display: none;" disabled="" selected="">{{$t('ingest.taskList.statusFilter')}}</option>
                        <option>{{$t('ingest.taskList.uploadingFilter')}}</option>
                        <option>{{$t('ingest.taskList.processingFilter')}}</option>
                    </select>
                    <input type="search" :placeholder="$t('Search')">
        </div>
        <br/>
        <div class="row plistHeader">
            <div class="col-1"><input type="checkbox" class="checkbox" id="allSips"></div>
            <div class="col">{{$t('ingest.fileList.fileName')}}</div>
            <div class="col-3">{{$t('ingest.fileList.fileSize')}}</div>
            <div class="col-2 listActionItems"></div>
        </div>
        <br/>
        <file v-for="listitem in items" v-bind:item="listitem" v-bind:key="listitem.id"/>
        </form>
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
