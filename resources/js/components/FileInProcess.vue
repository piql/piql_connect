<template>
    <div>
        <div class="row plist">
            <div class="col">
                {{item.uuid}}
            </div>
            <div class="col">
                {{item.fileName}}
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
    mounted() {
        console.log('FileInProcess component mounted.')
    },
    props: {
        item: Object
    }
}
</script>
