<template>
    <div>
        <div class="row plist">
            <div class="col-sm-5">
                {{item.name}}
            </div>
            <div class="col-sm-3">
                {{shortDate}}
            </div>
            <div class="col-sm-3 text-truncate">
                {{item.status}}
            </div>
            <div class="col-sm-1">
                <div class="spinner-border" role="status">
                  <span class="sr-only"></span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment';
export default {
    async mounted() {
        let bagId = this.item.id;
    },
    data() {
        return {
            bag: {},
        };
    },
    props: {
        item: Object
    },
    computed: {
        shortDate: function() {
                return moment(this.item.created_at).format('L');
        }
    }
}
</script>
