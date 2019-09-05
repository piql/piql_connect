<template>
    <div class="row plist">
        <div class="col pt-sm-3">
            {{item.filename}}
        </div>
        <div class="col-1 pt-sm-3">
            60 MiB
        </div>
        <div class="col-sm-2 listActionItems d-flex flex-row justify-content-end pt-sm-1">
            <div class="mx-sm-1">
                <i class="fas fa-tags cursor-pointer hover-hand"  style="font-size: 21px;" @click="onTagsClick()"></i>
            </div>
            <div class="mx-sm-1">
                <i class="fas fa-trash-alt hover-hand" style="font-size: 21px;"></i>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
export default {
    methods: {
        onTagsClick() {
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
