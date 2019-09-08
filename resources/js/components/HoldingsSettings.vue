<template>
    <div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6 col-lg-6 col-xs-6 col-lg-6">
                    <h4>{{$t('Current holdings')}}</h4>
                    <ul id="holdingsList" class="list-group mt-4">
                        <li class="list-group-item list-group-item-light" v-for="holding in holdings">{{ holding.title }} <button class="btn btn-sm btn-right">{{$t('Assign Fonds')}}</button></li>
                    </ul>
                </div>
                <div class="col-sm-1"></div>
                <div class="col-sm-5">
                    <h4>Create new</h4>
                    <form class="form mb-3" v-on:submit.prevent>
                        <div class="form-group">
                            <label for="holding-title">{{$t('Title')}}</label>
                            <input type="text" class="form-control" id="holding-title" v-model="holdingTitle">
                        </div>
                        <div class="form-group">
                            <label for="holding-description">{{$t('Description')}}</label>
                            <textarea class="form-control description-area" rows="5" id="holding-description" v-model="holdingDescription"></textarea>
                        </div>
                        <div class="form-group form-submit">
                            <button class="btn btn-primary btn-right" @click="createHolding">Create holding</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
export default {
    data() {
        return {
            holdingTitle: "",
            holdingDescription: "",
            selectedHolding: Number,
            holdings: [],
        }
    },
    async mounted() {
        this.holdings = (await axios.get("/api/v1/planning/holdings")).data;
    },
    methods: {
        async createHolding() {
            axios.post("/api/v1/planning/holdings", {
                title: this.holdingTitle,
                description: this.holdingDescription
            }).then( async (response) => {
                this.holdings = (await axios.get("/api/v1/planning/holdings")).data;
            });
            
        }
    },
}
</script>
