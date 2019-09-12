<template>
        <div class="row plist">
            <div class="col-sm-1">
                <input type="checkbox" class="checkbox" id="browserList" v-if="false">
            </div>
            <div class="col-sm-3">
                {{item.name}}
            </div>
            <div class="col-sm-3">
                {{item.archive_name}}
            </div>
            <div class="col-sm-3">
                {{item.holding_name}}
            </div>
            <div class="col-sm-2">
                <a href="http://127.0.0.1:62080/archival-storage/download/aip/ecee5617-fa6a-4d55-9783-db7b782053a9/"><i class="fas fa-file-download titleIcon"></i></a>
            </div>
        </div>
</template>

<script>
    import axios from 'axios';
    export default {
        async mounted() {
        },
        props: {
            item: Object,
            archive: String,
            holding: String,
        },

        data() {
            return {
                fileName: "",
            };
        },

    }
</script>
