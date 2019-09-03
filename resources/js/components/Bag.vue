<template>
    <div>
        <div class="row plist">
            <div class="col-1">
                <div v-on:click="selectActiveBag(item.id)"><i class="fas fa-arrow-circle-right"></i></div>
            </div>
            <div class="col">
                {{item.name}}
            </div>
            <div class="col">
                {{item.uuid}}
            </div>
            <div class="col">
                {{item.status}}
            </div>
            <div class="col">
                {{shortDate(item.created_at)}}
            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment';
export default {
    async mounted() {
        console.log('Bag component mounted.');
    },
    props: {
        item: Object,
    },
    methods: {
        shortDate: function(date){
            return moment(date).format("YYYY-MM-DD");
        },
        selectActiveBag: function(id){
            console.log("Bag item select active id: "+id);
            this.$emit('selectActiveBag', id);
        },
    }
}
</script>
