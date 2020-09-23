<template>
    <div>
        <table class="table table-hover table-sm table-bordered table-vcenter text-truncate">
            <thead>
                <tr>
                    <th class="text-center p-3">{{$t("admin.account.metadata.title")}}</th>
                    <th class="text-center p-3">{{$t("admin.account.metadata.creator")}}</th>
                    <th class="text-center p-3">{{$t("admin.account.metadata.subject")}}</th>
                    <th class="text-center p-3">{{$t("admin.account.metadata.schema")}}</th>
                    <th class="text-center p-3">{{$t("admin.account.metadata.actions")}}</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="template in templateListView" :key="template.id" class="mt-2">
                    <td class="text-left p-3">{{template.title}}</td>
                    <td class="text-left p-3">{{template.creator}}</td>
                    <td class="text-left p-3">{{template.subject}}</td>
                    <td class="text-center p-3">Dublin Core v1.1</td>

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
        templateListView: function(){
            let templates = JSON.parse(JSON.stringify(this.metadataTemplates));
            return templates.filter( t => !!t.metadata.dc)
                .map( t => {
                    return {
                        id: t.id,
                        creator: t.metadata.dc.creator,
                        title: t.metadata.dc.title,
                        subject: t.metadata.dc.subject
                    };
                });
        },
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
