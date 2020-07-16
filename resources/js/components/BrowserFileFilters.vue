<template>
    <div>
        <form v-on:submit.prevent>
            <div class="row mb-2 alignItens">
                <breadcumb :subTitle="subTitle" :subTitleRoute="{ name: 'access.browse' }"/>
                <div v-if="filters.search" class="col-lg-3 col-md col-sm-12">
                    <SearchField :label="$t('access.browse.withContents')" />
                </div>
            </div>
        </form>
    </div>
</template>

<script>

import RouterTools from '../mixins/RouterTools.js';

export default {

    mixins: [ RouterTools ],

    data () {
        return {
            searchField: "",
        }
    },

    props: {
        filters: {
            type: Object,
            default: function() {
                return {
                    search: true
                };
            }
        },
        subTitle: {
            type: String
        },
    },
    watch: {
        '$route': 'dispatchRouting'
    },
};
</script>
<style scoped>
.subTitle {
    margin-top: 10px;
    margin-left: 96px;
    font-weight: bold;
}
.subTitle a {
    color: #cc5d33;
}
.rightAlign {
    display: inline-block;
}
.alignItens {
    justify-content: space-between;
    padding-bottom: 22px;
}
</style>