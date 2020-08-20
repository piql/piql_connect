<template>
    <div class="w-100">

        <page-heading icon="fa-tags"
            :title="$t('admin.metadata.template.title')"
            :ingress="$t('admin.metadata.template.ingress')" />

				<div class="card">
						<div class="card-header">
								<div class="row">
										<button class="btn form-control-btn ml-3" @click="createTemplate($event.target)">
												<i class="fas fa-plus mr-2"></i>{{$t('admin.metadata.createNewTemplate')}}
										</button>
										<button class="btn form-control-btn ml-3" @click="assignTemplate($event.target)">
												<i class="fas fa-list-ol mr-2"></i>{{$t('admin.metadata.listCurrentAssignments')}}
										</button>
								</div>
						</div>

						<div class="card-body">
                <MetadataTemplateList @cloneTemplate="cloneTemplate" />
						</div>

						<div class="row text-center pagerRow">
								<div class="col">
										<!--Pager :meta='templatePageMeta' :height='height' /-->
								</div>
						</div>
				</div>


        <dublin-core-template @save="save" v-bind:initialTemplate="currentTemplate" />

    </div>
</template>

<script>
export default {
    data() {
        return {
            updateDcKey: 0,
            currentTemplate: {}
        }
    },
    props: {
    },
    methods: {
        save: function() {
            this.$emit('save');
        },
        createTemplate: function(target){
            target.blur();
            this.currentTemplate = {};
            this.$bvModal.show("meta");
        },
        assignTemplate: function(target) {
            target.blur();
        },
        cloneTemplate: function( template ){
            this.currentTemplate = template;
            this.$bvModal.show("meta");
        }

    }
}
</script>
