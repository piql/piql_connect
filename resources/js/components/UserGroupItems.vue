<template>
  <div class="table-responsive">
      
       <table class="table table-hover table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Group</th>
                            <th>Description</th>
                          
                            <th width="18%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="group in groups" :key="group.id">
                            <td>{{group.name}}</td>
                            <td>{{group.description}}</td>
                            <td>
                                <a class="btn btn-xs btn-primary" @click="viewPermissions(group.id)" title="View Permissions" style="color:white">
                                    <i class="fa fa-eye"></i>
                                    </a>
                                <a class="btn btn-xs btn-primary" title="Edit Access Group" style="color:white" @click="showEditModal(group.id)">
                                    <i class="fa fa-edit"></i>
                                    </a>
                                <a class="btn btn-xs btn-primary"  title="Delete Access Group" @click="showDeleteModal(group.id)" style="color:white">
                                    <i class="fa fa-trash"></i>
                                    </a>
                            </td>
                        </tr>
                    
                       
                    </tbody>
                </table>
                <div class="row text-center pagerRow">
                    <div class="col">

                      
                        <Pager :meta='pageMeta' :height='height' />
                    </div>
                </div>

                <b-modal id="delete-group" hide-footer>
                    <template v-slot:modal-title>
                        <h4> <b>DELETE GROUP [ {{ group[0].name.toUpperCase() }} ]</b></h4>
                    </template>
                    <div class="d-block">
                        <b-alert show variant="warning">Do you want to delete this group? if so, click below to proceed</b-alert>
                    </div>
                    <b-button class="mt-3" block @click="deleteGroupClicked(group[0].id)" @keydown="deleteGroupClicked(group[0].id)"><i class="fa fa-trash"></i> DELETE GROUP</b-button>
                </b-modal>

                <b-modal id="edit-group" size="lg" hide-footer>
                    <template v-slot:modal-title>
                   <h4> <b>EDIT GROUP [ {{ group[0].name.toUpperCase() }} ]</b></h4>
                    </template>
                    <div class="d-block">
                        <div class="form-group">
                            <label>Group</label>
                            <input type="text" class="form-control" v-model="groupName" >
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea v-model="description" class="form-control"></textarea>
                        </div>
                    </div>
                    <b-button class="mt-3" @click="editButtonClicked(group[0].id)" block><i class="fa fa-edit"></i> Edit Group</b-button>
                </b-modal>
                
              
  
  
  
  </div>

</template>

<script>
export default {
    data() {
            return {
                response:null,
                description: null,
                groups:null,
                pageMeta: null,
                groupId: null,
                group: null,
                groupName: null,
                description: null,
               
            };
    },

    props: {
        height: {
            type: Number,
            default: 0
        }
    },
    computed:{
      apiQueryString: function() {
            let query = this.$route.query;
            let filter = '';
            if( parseInt( query.page ) ) {
                filter += "?page=" + query.page;
            }
            return filter;
        },

    },
     watch: {
        '$route': 'dispatchRouting'
    },
    async mounted() {
       let page = this.$route.query.page;
        if( isNaN( page ) || parseInt( page ) < 2 ) {
            this.$route.query.page = 1;
        }
        this.refreshObjects( this.apiQueryString );

        
    },
    methods:{
        editButtonClicked(groupId){
            let data = {
                name: this.groupName,
                description: this.description,
                groupId: groupId
            }

            this.$emit('editGroup', data);


        },
        showEditModal(groupId){
            this.group = this.groups.filter(group => group.id === groupId);
            this.groupName = this.group[0].name;
            this.description = this.group[0].description;
            this.$bvModal.show('edit-group')

        },
       dispatchRouting() {
            this.refreshObjects( this.apiQueryString );
        },
       showDeleteModal(groupId){
           this.group = this.groups.filter(group => group.id === groupId);
           this.$bvModal.show('delete-group')

        },
        deleteGroupClicked(groupId){
            this.$emit('deleteGroup', groupId);

        },

        refreshObjects( apiQueryString ){
            axios.get("/api/v1/admin/access-control/permission-groups" + apiQueryString).then( (response ) => {
               this.response = response
                this.groups = this.response.data.data;
                this.pageMeta = this.response.data.meta
            }).catch(error => {
                this.response = error;
            });
        },

        viewPermissions(groupId){
          this.$router.push({ name:'settings.groups.permissions', params: { groupId: groupId } });
        },
        
    }


}
</script>

<style>

</style>