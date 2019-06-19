<template>
    <div>
        <div class="row listFilter">
                <form>
                    <label for="fromDate">From</label>
                    <input type="date" name="fromDate" placeholder="DDMMYY">
                    <label for="toDate">To</label>
                    <input type="date" name="toDate" placeholder="DDMMYY">
                    <select name="status">
                        <option style="display: none;" disabled="" selected="">Status</option>
                        <option>Uploading</option>
                        <option>Processing</option>
                    </select>
                    <input type="search" placeholder="Search">
                </form>
        </div>
        <br/>
        <div class="row plistHeader">
            <div class="col">Bag</div>
            <div class="col">Content</div>
            <div class="col">Ingest Date</div>
            <div class="col">Status</div>
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
            this.items = (await axios.get("/api/v1/ingest/uploaded/")).data; 
            console.log(this.items);

            console.log('Processing component mounted.')
        },
    }
</script>
