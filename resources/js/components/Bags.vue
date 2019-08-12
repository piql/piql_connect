<template>
    <div>
        <form @submit="createBag">
            <div class="row">
                <div class="col-2">
                    <button type="submit" class="btn btn-primary btn-lg">Start new Ingest Session</button>
                </div>
                <div class="col-3">
                    <input value="" placeholder="New Ingest Session name..." v-model="bagName" type="text" class="form-control-lg" style="padding: 24px;"> 
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

        <div class="plist" style="height: 500px; overflow-y: scroll !important;">
            <Bag v-for="item in items" v-bind:item="item" v-bind:key="item.id" @selectActiveBag="selectActiveBag"/>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            bagName: '',
            userId: '',
        };
    },
    props: {
        items : {},
    },
    async mounted() {
        console.log('Bags component mounted.')
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
            let createdBag = (await axios.post("/api/v1/ingest/bags/", {
                bagName: this.bagName,
                userId: this.userId,
            })).data;
            this.items = (await axios.get("/api/v1/ingest/bags")).data;
            this.$emit('selectActiveBag', createdBag.id);
            this.bagName = "";
        },
        selectActiveBag: function(id) {
            console.log("Bags select active id: "+id);
            this.$emit('selectActiveBag', id);
        }
    }
}
</script>
