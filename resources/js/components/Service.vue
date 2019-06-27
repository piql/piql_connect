<template>
    <div>
        <div class="row plist">
            <div class="col-1">
                <div v-on:click="pingService(item.url)"><i class="fas fa-ethernet"></i></div>
            </div>
            <div class="col">
                {{item.url}}
            </div>
            <div class="col">
                {{item.api_token}}
            </div>
            <div class="col">
                {{item.id}}
            </div>
            <div class="col-1 text-center">
                {{status}}
            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment';
export default {
    async mounted() {
        console.log('Service component mounted.');
    },
    props: {
        item: Object,
    },
    data() {
        return {
            status: "?",
        };
    },
    methods: {
        shortDate: function(date){
            return moment(date).format("YYYY-MM-DD");
        },
        async pingService(url) {
            this.status = "Pending...";

            console.log("Pinging url "+url);
            this.errors = {};
            let self = this;
   
            axios(url, {
                method: "GET",
                mode: "no-cors",
                headers: {
                    'Access-Control-Allow-Origin': '*',
                    'Content-Type': 'application/json',
                },
            }
            ).then( response => {
                if(response.status == 200) {
                self.status = "OK!";
                } else {
                    self.status = response.status;
                }
            }).catch( error => {
                self.status = error;
            });

        },
 
    }
}
</script>
