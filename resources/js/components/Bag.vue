<template>
    <div>
        <div class="row plist">
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
        item: Object
    },
    methods: {
        shortDate: function(date){
            return moment(date).format("YYYY-MM-DD");
        }
    }
}
</script>
