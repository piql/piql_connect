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
        rc: function() {
            return item.rc;
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
