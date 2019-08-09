<template>
    <div>
        <div class="row plist">
            <div class="col-1"><input type="checkbox" :id="item.uuid" class="checkbox"></div>
            <div class="col">
                {{item.filename}}
            </div>
            <div class="col-3">
                &nbsp <!-- fileSize -->
            </div>
            <div class="col-2 listActionItems">
                <i class="fas fa-eye"></i>&nbsp;
                <i class="fas fa-trash-alt"></i>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    async mounted() {
        console.log('Task component mounted.')
    },
    props: {
        item: Object,
    },

    data() {
        return {
            fileName: "",
        };
    },

}
</script>
