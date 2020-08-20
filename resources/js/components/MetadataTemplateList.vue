<template>
    <div>
        <table class="table table-hover table-sm table-bordered table-vcenter text-truncate">
            <thead>
                <tr>
                    <th class="text-center">{{$t("admin.account.metadata.title")}}</th>
                    <th class="text-center">{{$t("admin.account.metadata.identifier")}}</th>
                    <th class="text-center">{{$t("admin.account.metadata.created_at")}}</th>
                    <th class="text-center">{{$t("admin.account.metadata.actions")}}</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="template in metadataTemplateFix" :key="template.id" class="mt-2">
                    <td class="text-center">{{template.metadata.dc.title}}</td>
                    <td class="text-center">{{template.metadata.dc.identifier}}</td>
                    <td class="text-center">{{template.created_at}}</td>

                    <td class="align-self-center text-center">
                        <a class="ml-2 mr-2 btn btn-xs text-white"
                            @click="cloneTemplate(template)" :title="$t('admin.account.metadata.template.clone')" >
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
    },

    computed: {
        metadataTemplateFix: function() {
            return [
                {"id":79, "created_at": "2020-05-22", "metadata":{"dc":{"title":"Svalbard","creator":"a photographer","subject":"cold things","description":"brrr","publisher":"","contributor":"","date":"","type":"","format":"","identifier":"63ff83ef-862e-428f-9cc3-2040693d493e","source":"","language":"","relation":"","coverage":"","rights":""}}},
                {"id":80, "created_at": "2020-05-23", "metadata":{"dc":{"title":"Drammen","creator":"that guy","subject":"awesome place","description":"yay","publisher":"","contributor":"","date":"","type":"","format":"","identifier":"63ff83ef-862e-428f-9cc3-2040693d493e","source":"","language":"","relation":"","coverage":"","rights":""}}}
            ];
        }
    },

    created () {
    },

    mounted () {
    },

    methods: {
        cloneTemplate: function( template ) {
            this.$emit('cloneTemplate', template);
        },
    }
};
</script>
