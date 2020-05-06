<template>
    <div>
        <div class="row plist">
            <div class="col-sm-6">
                {{item.filename}}
            </div>
            <div class="col-sm-2 text-center">
                {{ formatShortDate( item.created_at ) }}
            </div>
            <div class="col-sm-2 text-right">
                {{item.filesize}}
            </div>
            <div class="col-sm-2 text-center">
                <div class="spinner-border" role="status">
                    <span class="sr-only">Retrieving</span>
                </div>
            </div>
        </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
        };
    },
    props: {
        item: Object
    },
}
</script>
