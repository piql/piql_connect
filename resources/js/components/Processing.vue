<template>
    <div>
        <div class="row listFilter">
                <form>
                    <label for="fromDate">{{$t('ingest.processing.from')}}</label>
                    <input type="date" name="fromDate" placeholder="DDMMYY">
                    <label for="toDate">{{$t('ingest.processing.to')}}</label>
                    <input type="date" name="toDate" placeholder="DDMMYY">
                    <select name="status">
                        <option style="display: none;" disabled="" selected="">{{$t('ingest.processing.statusFilter')}}</option>
                        <option>{{$t('ingest.processing.uploadingFilter')}}</option>
                        <option>{{$t('ingest.processing.processingFilter')}}</option>
                    </select>
                    <input type="search" :placeholder="$t('Search')">
                </form>
        </div>
        <br/>
        <div class="row plistHeader">
            <div class="col">{{$t('ingest.processing.bag')}}</div>
            <div class="col">{{$t('ingest.processing.content')}}</div>
            <div class="col">{{$t('ingest.processing.ingestDate')}}</div>
            <div class="col">{{$t('ingest.processing.status')}}</div>
        </div>

        <FileInProcess v-for="item in items" :item="item"/>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                items : {}
            }
        },

        async mounted() {
            this.items = (await axios.get("/api/v1/ingest/processing/")).data;
            console.log(this.items);

            console.log('Processing component mounted.')
        },
    }
</script>
