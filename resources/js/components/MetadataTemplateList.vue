<template>
    <div>
        <table class="table table-hover table-sm table-bordered table-vcenter text-truncate">
            <thead>
                <tr>
                    <th class="text-center p-3">{{$t("admin.account.metadata.title")}}</th>
                    <th class="text-center p-3">{{$t("admin.account.metadata.creator")}}</th>
                    <th class="text-center p-3">{{$t("admin.account.metadata.created_at")}}</th>
                    <th class="text-center p-3">{{$t("admin.account.metadata.subject")}}</th>
                    <th class="text-center">{{$t("admin.account.metadata.actions")}}</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="template in metadataTemplates" :key="template.id" class="mt-2">
                    <td class="text-left p-3">{{template.metadata.dc.title}}</td>
                    <td class="text-left p-3">{{template.metadata.dc.creator}}</td>
                    <td class="text-center p-3">{{template.created_at}}</td>
                    <td class="text-left p-3">{{template.metadata.dc.subject}}</td>

                    <td class="align-self-center text-center">
                        <a class="ml-2 mr-2 btn btn-xs text-white"
                            @click="cloneTemplate(template.id)" :title="$t('admin.account.metadata.template.clone')" >
                            <i class="fas fa-clone"></i></a>
                        <a class="ml-2 mr-2 btn btn-xs text-white"
                            @click="assignTemplate(template)" :title="$t('admin.account.metadata.template.assign')" >
                            <i class="fas fa-paperclip"></i></a>
                    </td>
                </tr>

            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    components: {},

    mixins: [],

    data () {
        return {
        }
    },

    props: {
        metadataTemplates: {
            type: Array,
            default: null
        }
    },

    computed: {
    },

    created () {
    },

    mounted () {
    },

    methods: {
        cloneTemplate: function( id ){
            this.$emit('cloneTemplate', id);
        },
    }
};
</script>
