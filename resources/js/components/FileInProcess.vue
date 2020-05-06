<template>
    <div>
        <div class="row plist">
            <div class="col-sm-5">
                {{item.name}}
            </div>
            <div class="col-sm-3">
                {{formatShortDate( item.created_at )}}
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
