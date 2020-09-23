<template>
    <div class="w-100">
        <dublin-core-template @saveTemplate="saveTemplate" v-bind:initialTemplate="currentTemplate" />

        <page-heading icon="fa-tags"
            :title="$t('admin.metadata.template.title')"
            :ingress="$t('admin.metadata.template.ingress')" />

				<div class="card">
						<div class="card-header">
								<div class="row">
										<button class="btn form-control-btn ml-3" @click="createTemplate($event.target)">
												<i class="fas fa-plus mr-2"></i>{{$t('admin.metadata.createNewTemplate')}}
										</button>
								</div>
						</div>

						<div class="card-body">
                <metadata-template-list @cloneTemplate="cloneTemplate" v-bind:metadataTemplates="templates" />
						</div>

						<div class="row text-center pagerRow">
								<div class="col">
                    <Pager :meta='templatePageMeta' />
								</div>
						</div>
				</div>



    </div>
</template>

<script>
import {mapGetters, mapActions} from "vuex";


export default {
    data() {
        return {
            updateDcKey: 0,
            currentTemplate: {},
        }
    },
    props: {
    },
    mounted: function(){
        this.$store.dispatch( 'fetchTemplates', this.apiQueryString );
    },
    computed: {
        ...mapGetters(['templates', 'templateById', 'templatePageMeta']),
        apiQueryString: function() {
            let routeQuery = this.$route.query;
            let apiQueryItems = [];
            let filters = [];
            let page;
            if( parseInt( routeQuery.page ) ) {
                filters = filters.concat(`page=${routeQuery.page}`);
            }
            return "?".concat( filters.filter( (f) => f ).join( "&" ) );
            return filter;
        },

    },
    watch: {
        '$route': 'dispatchRouting'
    },
    methods: {
        ...mapActions({
            template: ['addTemplate']
        }),
        dispatchRouting() {
            this.$store.dispatch( 'fetchTemplates', this.apiQueryString );
        },

        createTemplate: function(target){
            target.blur();
            this.currentTemplate = JSON.parse(JSON.stringify({ "metadata": {"dc" : {} } }));
            this.currentTemplate.id = "";       /* Remove the id and creation date, they will be filled out by the server later on */
            this.currentTemplate.created_at = "";
            this.$bvModal.show("meta");
        },
        assignTemplate: function(target) {
            target.blur();
            this.$bvModal.show("assigntemplate");
        },
        cloneTemplate: function( templateId ){
            this.currentTemplate = JSON.parse(JSON.stringify(this.templateById(templateId)));  /* Deep copy the template from the store */
            this.currentTemplate.id = "";       /* Remove the id and creation date, they will be filled out by the server later on */
            this.currentTemplate.created_at = "";
            this.$bvModal.show("meta");
        },
        saveTemplate: async function( template ){
            await this.$store.dispatch( 'addTemplate', template );
        }

    }
}
</script>
