<template>
    <div>
        <div class="row plist">
            <div class="col-2">
                <i class="fas fa-file display-4 thumbnailIcon"></i>
            </div>
            <div class="col-5">
                <div class="row">
                    <div class="col">
                        {{item.name}}
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <span class="small">Forsvarsmuseet</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <span class="small">{{selectedFond}}</span>
                    </div>
                </div>
            </div>
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
        selectedFond: String
    },

    data() {
        return {
            fileName: "",
        };
    },

}
</script>
