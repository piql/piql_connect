<template> 
    <div>
        <div class="row">
            <div class="col-sm-2 text-center">
                <i :class="icon" class="fas color-main-brand titleIcon"></i>
            </div>
            <div class="text-left ml-5">
                <h1 class="ml-1">{{title}}</h1>
                <div class="ml-1">{{ingress}}</div>
            </div>
        </div>
    </div>

</template>

<script>
    export default {
        mounted() {
            console.log("Hello head!");
        },
        props: {
            title: {
                type: String,
                default: "Page title"
            },
            icon: {
                type: String,
                default: "fa-cog"
            },
            ingress: {
                type: String,
                default: "Page description"
            }
        }
    }
</script>
