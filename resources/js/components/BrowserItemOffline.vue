<template>
        <div class="row plist">
            <div class="col-sm-1">
                <input type="checkbox" class="checkbox" id="browserList">
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
                <a @click="addToRetrieval" class="fas fa-file-export titleIcon signal" href="#"></i></a>
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
        methods: {
            addToRetrieval: function() {
               this.$emit('addToRetrieval', this.item); 
            },
        },

    }
</script>

