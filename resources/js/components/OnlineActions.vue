<template>
        <div class="row justify-content-center">
           <div class="col-md-8">
                <div v-if="downloadCartEnabled" class="row">
                    <div class="col">
                        <a style="color: black" href="#"><i class="fas fa-arrow-down browseActionItem mr-2"></i>Download</a></br>
                    </div>
                </div>
                <div v-if="exportEnabled" class="row mb-5">
                    <div class="col"> 
                        <a style="color: black" href="#">Export metadata</a><br>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
        },
        props: {
            exportEnabled: {
                type: Boolean,
                default: false
            },
            downloadCartEnabled: {
                type: Boolean,
                default: false
            },

        },
    }
</script>
