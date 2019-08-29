<template>
    <div>
        <div class="row plist">
            <div class="col-sm-5">
                {{item.name}}
            </div>
            <div class="col-sm-3">
                {{item.created_at}}
            </div>
            <div class="col-sm-3">
                {{ $t('ingest.processing.creatingStatus') }}
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
export default {
    async mounted() {
        console.log('FileInProcess component mounted.')
        let bagId = this.item.id;
    },
    data() {
        return {
            bag: {},
        };
    },
    props: {
        item: Object
    }
}
</script>
