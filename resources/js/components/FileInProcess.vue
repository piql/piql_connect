<template>
    <div>
        <div class="row plist">
            <div class="col">
                {{item.name}}
            </div>
            <div class="col">
                
            </div>
            <div class="col">
                {{item.created_at}}
            </div>
            <div class="col">
                Ingesting
            </div>
        </div>
    </div>
</template>

<script>
export default {
    async mounted() {
        console.log('FileInProcess component mounted.')
    },
    data() {
        return {
            bag: {}
        };
    },
    props: {
        item: Object
    }
}
</script>
