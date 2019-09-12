<template>
    <div class="row plist">
        <div class="col pt-sm-3">
            {{item.filename}}
        </div>
        <div class="col-2 pt-sm-3 d-flex flex-row justify-content-end">
            {{size}}
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
        },
        getFileSizeIEC(bytes) {
            let value = 0;
            let exp = 0;
            if (bytes) {
                exp = Math.floor(Math.log(bytes) / Math.log(1024));
                value = (bytes / Math.pow(1024, exp)).toFixed(2);
            }
            return value + " " + (exp ? 'KMGTPEZY'[exp - 1] + 'iB' : 'Bytes')
        },
        getFileSizeSI(bytes) {
            let value = 0;
            let exp = 0;
            if (bytes) {
                exp = Math.floor(Math.log(bytes) / Math.log(1000));
                value = (bytes / Math.pow(1000, exp)).toFixed(2);
            }
            return value + " " + (exp ? 'KMGTPEZY'[exp - 1] + 'B' : 'Bytes')
        },
    },
    async mounted() {
        console.log('Task component mounted.')
    },
    props: {
        item: {},
    },
    computed: {
        size: function() {
            if(this.item.filesize !== undefined)
                return this.getFileSizeSI(this.item.filesize);
            else
                return "---";
        },
    },
    data() {
        return {
            fileName: "",
        };
    },

}
</script>
