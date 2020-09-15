<template>
    <div class="subTitle">
        <span>
            <i class="fas fa-home"></i>
            <span v-for="crumb in breadcrumbs" :key="crumb.name">
                <i class="fas fa-chevron-right"></i>
                <router-link :to="crumb.link" class="cursorPointer">{{crumb.name}}</router-link>
            </span>    
            <span v-if="subTitle">
                <i class="fas fa-chevron-right"></i>
                {{subTitle}}
            </span>
        </span>
    </div>
</template>

<script>
    export default {
        props: {
            subTitle: {
                type: String,
                default: null
            },
            subTitleRoute: {
                type: Object,
                default: null
            },
        },
        methods:{
            createLink(crumb){
                //search path
                let crumbLocation = this.$route.path.search(crumb);
                let crumbLength = crumb.length();
                let totalLength = crumbLocation + crumbLength;
                return this.$route.path.substring(0, totalLength);

            },

        },
        computed:{
            breadcrumbs(){
                let crumbs =  this.$route.name.split(".");
                crumbs.shift();
                return crumbs.map(crumb => {
                    return {
                            name: crumb,
                            link: this.$route.path.substring(0,(this.$route.path.search(crumb) + crumb.length))
                        
                        }           
                });
            },
            
        }
    }
</script>

<style scoped>
    .subTitle {
        margin-top: 10px;
        margin-left: 84px;
        color: #333;

       
    }
    .subTitle a {
        color: #000;
    }
    .subTitle a:hover {
        color: #cc5d33;
        text-decoration: none;
    }
</style>
