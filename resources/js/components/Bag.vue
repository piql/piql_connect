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
                {{ formatShortDate( item.created_at ) }}
            </div>
        </div>
    </div>
</template>

<script>
export default {
    async mounted() {
    },
    props: {
        item: Object,
    },
    methods: {
        selectActiveBag: function(id){
            this.$emit('selectActiveBag', id);
        },
    }
}
</script>
