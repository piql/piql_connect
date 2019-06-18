<template>
    <div>
        <form>
        <div class="row listFilter">
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
        </div>
        <br/>
        <div class="row plistHeader">
            <div class="col"><input type="checkbox" class="checkbox" id="allSips"></div>
            <div class="col">SIP</div>
            <div class="col">Content</div>
            <div class="col">Ingest Date</div>
            <div class="col listActionItems">&nbsp;</div>
            <div class="col piqlIt">&nbsp;</div>
        </div>

        <Task v-for="item in items" :item="item"/>
        </form>
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
            this.items = (await axios.get("/api/v1/ingest/uploaded/all")).data; 
            console.log(this.items);

            console.log('TaskList component mounted.')
        },
    }
</script>
