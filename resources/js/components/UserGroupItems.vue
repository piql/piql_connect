<template>
  <div class="table-responsive">
      
       <table class="table table-hover table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Group</th>
                            <th>Description</th>
                          
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="group in groups" :key="group.id">
                            <td>{{group.name}}</td>
                            <td>{{group.description}}</td>
                            <td>
                                <a class="btn btn-xs btn-primary" @click="viewActions(group.id)" title="Actions" style="color:white">
                                    <i class="fa fa-eye"></i>
                                    </a>
                                <a class="btn btn-xs btn-primary" @click="viewUsers(group.id)" title="Users" style="color:white">
                                    <i class="fa fa-users"></i>
                                    </a>
                                <a class="btn btn-xs btn-primary" title="Add Role" style="color:white" @click="showAssignActionModal(group.id)">
                                    <i class="fa fa-user-secret"></i>
                                    </a>
                                <a class="btn btn-xs btn-primary" @click="showAssignModal(group.id)" title="Assign Users" style="color:white">
                                    <i class="fa fa-user-plus"></i>
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

                <b-modal id="add-action" hide-footer>
                    <template v-slot:modal-title>
                    Add Role
                    </template>
                    <div class="d-block">
                    <div class="form-group">
                        <label>Role</label>
                        <input type="text" class="form-control" v-model="role" >
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea v-model="description" class="form-control"></textarea>
                    </div>
                    </div>
                    <b-button class="mt-3" block @click="addRoleClicked" @keydown="addRoleClicked"><i class="fa fa-user-secret"></i> Add Role</b-button>
                </b-modal>

                <b-modal id="assign-group" size="lg" hide-footer>
                    <template v-slot:modal-title>
                   <h4> <b>ASSIGN USERS TO GROUP [ {{ group[0].name.toUpperCase() }} ]</b></h4>
                    </template>
                    <div>
                        <vue-select-sides
                        type="mirror"
                        v-model="selectedUsers"
                        :list="list"
                        ></vue-select-sides>
                    
                    </div>
                    <b-button class="mt-3" @click="assignButtonClicked(group[0].id)" block><i class="fa fa-users"></i> Assign Users</b-button>
                </b-modal>
                
              
  
  
  
  </div>

</template>

<script>
export default {
    data() {
            return {
                response:null,
                role:null,
                description: null,
                groups:null,
                pageMeta: null,
                groupId: null,
                group: null,
                list: [],
                selectedUsers: []
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

        /**list users * i can only pull in 10 at a time, need help getting all at 
         * the same time unless allowed to tamper with the backend **/

       let users = (await axios.get("/api/v1/admin/users",{params: {limit: 100}})).data.data;
        users.forEach(single => {
            this.list.push({
                label: single.full_name,
                value: single.id
                })
        });
        
        
    },
    methods:{
        assignButtonClicked(groupId){
            let data = {
                users: this.selectedUsers,
                permissions: [groupId]
            }

            this.$emit('assignGroupToUsers', data);


        },
        showAssignModal(groupId){
            this.group = this.groups.filter(group => group.id === groupId);
            this.$bvModal.show('assign-group')

        },
       dispatchRouting() {
            this.refreshObjects( this.apiQueryString );
        },

        refreshObjects( apiQueryString ){
            axios.get("/api/v1/admin/permissions/groups" + apiQueryString).then( (response ) => {
               this.response = response
                this.groups = this.response.data.data;
                this.pageMeta = this.response.data.meta
            });
        },

        showAssignActionModal(groupId){
          this.groupId = groupId;
          this.$bvModal.show('add-action');
        },

        addRoleClicked(){
          let role = {
            name: this.role,
            description: this.description,
            groupId: this.groupId
          }
          this.$emit('addRole', role);
        },
        viewActions(groupId){
          this.$router.push({ path:'/settings/roles', query:{groupId} });
        },

        viewUsers(groupId){
          this.$router.push({ path:'/settings/listing', query:{groupId} });
        }
        
    }


}
</script>

<style>

</style>