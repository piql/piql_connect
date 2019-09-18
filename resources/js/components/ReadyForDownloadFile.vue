<template>
    <div>
        <div class="row plist">
            <div class="col-sm-8">
                {{item.name}}
            </div>
            <div class="col-sm-2 text-center">
              <a v-bind:href="downloadUrl"><i class="fas fa-file-download actionIcon"></i></a>
            </div>
            <div class="col-sm-2 text-center">
              <a href="#" @click="takeOffline"><i class="fas fa-unlink actionIcon"></i></a>
            </div>
        </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment';
export default {
    async mounted() {
    },
    data() {
        return {
        };
    },
    props: {
        item: Object
    },
    computed: {
        bag: function() {
            return item.bag;
        },
        rc: function() {
            return item.rc;
        },
        friendlyDate: function(){
            return moment(this.item.created_at).format('L');
        },
        downloadUrl: function(){
            return "/api/v1/storage/retrievals/"+this.item.id+"/download";
        },
    },
    methods: {
        takeOffline: function(){
            this.$emit('takeOffline', this.item.retr_id);
        }
    }
}
</script>
