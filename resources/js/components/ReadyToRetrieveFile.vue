<template>
    <div>
        <div class="row plist">
            <div class="col-sm-6">
                {{item.filename}}
            </div>
            <div class="col-sm-2">
                {{item.created_at}}
            </div>
            <div class="col-sm-2 text-right">
                {{item.filesize}}
            </div>
            <div class="col-sm-1">
            </div>
            <div class="col-sm-1 text-center">
              <a class="signal" href="#" @click="removeFromRetrieval"> <i class="fas fa-times p-2 hover-hand"></i></a>
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
        item: Object,
    },
    methods: {
        removeFromRetrieval: function() {
            this.$emit('removeFromRetrieval', this.item.id);
        },
    }
}
</script>
