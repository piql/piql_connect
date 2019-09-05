<template>
    <div>
        <div class="row plist">
            <div class="col">
                {{item.filename}}
            </div>
            <div class="col-1">
                60 MiB
            </div>
            <div class="col-2 listActionItems">
                <i class="fas fa-tags" @click="onClick()"></i>&nbsp;
                <i class="fas fa-trash-alt"></i>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
export default {
    methods: {
        onClick() {
            window.location = window.location+"/metadata/"+this.item.id+"/edit";
        }
    },
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
