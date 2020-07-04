<template>
  <div class="table-responsive">
      
       <table class="table table-hover table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Role</th>
                            <th>Description</th>
                            <th width="18%">Actions </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="role in roles" :key="role.id">
                            <td>{{role.name}}</td>
                            <td>{{role.description}}</td>
                            <td>
                                <a class="btn btn-xs btn-primary" title="Edit Role" style="color:white">
                                    <i class="fa fa-edit"></i>
                                    </a>
                                <a class="btn btn-xs btn-primary" @click="viewUsers(role.id)" title="Users" style="color:white">
                                    <i class="fa fa-users"></i>
                                    </a>
                                <a class="btn btn-xs btn-primary" title="Delete Role" style="color:white">
                                    <i class="fa fa-trash"></i>
                                    </a>
                                <a class="btn btn-xs btn-primary" @click="showAssignModal(role.id)" title="Assign Users" style="color:white">
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

                <b-modal id="assign-role" size="lg" hide-footer>
                    <template v-slot:modal-title>
                   <h4> <b>ASSIGN USERS TO ROLE [ {{ role[0].name.toUpperCase() }} ]</b></h4>
                    </template>
                    <div>
                        <vue-select-sides
                        type="mirror"
                        v-model="selectedUsers"
                        :list="list"
                        ></vue-select-sides>
                    
                    </div>
                    <b-button class="mt-3" @click="assignButtonClicked(role[0].id)" block><i class="fa fa-users"></i> Assign Users</b-button>
                </b-modal>


  
  </div>
</template>

<script>
export default {
    data() {
        return {
            response:null,
            roles: null,
            pageMeta: null,
            role: null,
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

        apiEndPoint: function () {
            let query = this.$route.query;

            if( parseInt( query.groupId ) ) {
                return '/api/v1/admin/permissions/groups/'+ query.groupId + '/roles';
            } else{
                return '/api/v1/admin/permissions/roles';
            }
            
        }

    },
     watch: {
        '$route': 'dispatchRouting'
    },
    async mounted() {
       let page = this.$route.query.page;
        if( isNaN( page ) || parseInt( page ) < 2 ) {
            this.$route.query.page = 1;
        }
        this.refreshObjects( this.apiQueryString, this.apiEndPoint );

        /**list users * i can only pull in 10 at a time, need help getting all at 
         * the same time unless allowed to tamper with the backend **/

       let users = (await axios.get("/api/v1/admin/users",{ params: { limit: 100 } })).data.data;
        users.forEach(single => {
            this.list.push({
                label: single.full_name,
                value: single.id
                })
        });

    },
    methods:{
        assignButtonClicked(roleId){
            let data = {
                users: this.selectedUsers,
                permissions: [roleId]
            }

            this.$emit('assignRoleToUsers', data);


        },
        showAssignModal(roleId){
            this.role = this.roles.filter(role => role.id === roleId);
            this.$bvModal.show('assign-role')

        },
        
       dispatchRouting() {
            this.refreshObjects( this.apiQueryString, this.apiEndPoint );
        },

        refreshObjects( apiQueryString, apiEndPoint ){
            axios.get(apiEndPoint + apiQueryString).then( (response ) => {
               this.response = response
                this.roles = this.response.data.data;
                
                this.pageMeta = this.response.data.meta
            });
        },
        viewUsers(roleId){
          this.$router.push({ path:'/settings/listing', query:{roleId} });
        }
        
    }

}
</script>



