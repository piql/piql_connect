<template>
    <div>
        <form @submit="createBag">
            <div class="row">
                <div class="col-2">
                    <button type="submit" class="btn btn-primary btn-lg">Create new bag</button>
                </div>
                <div class="col-3">
                    <input value="" placeholder="New bag name..." v-model="bagName" type="text" class="form-control-lg" style="padding: 24px;"> 
                </div>
            </div>
        </form>
        <br/>
        <div class="row plistHeader">
            <div class="col-1">Activate</div>
            <div class="col">Name</div>
            <div class="col">Id</div>
            <div class="col">Status</div>
            <div class="col">Start date</div>
        </div>

        <div class="plist">
            <Bag v-for="item in items" :item="item"/>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            items : {},
            bagName: '',
            userId: '',
        };
    },

    async mounted() {
        console.log('Bags component mounted.')
        this.items = (await axios.get("/api/v1/ingest/bags")).data; 
        this.userId = (await axios.get("/api/v1/system/currentUser")).data;
        console.log("current user: " + this.userId);
        console.log(this.items);
        console.log('Bag data loaded.');

    },
    methods: {
        async createBag(e) {
            e.preventDefault();
            console.log("BagName: "+this.bagName);
            this.errors = {};
            let response = await axios.post("/api/v1/ingest/bags/", {
                bagName: this.bagName,
                userId: this.userId,
            });
            console.log(response.data);
            this.items = (await axios.get("/api/v1/ingest/bags")).data; 
        }
    }
}
</script>
