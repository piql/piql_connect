<template>
  <div class="table-responsive">
      
       <table class="table table-hover table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Group</th>
                            <th>Description</th>
                            <th width="18%">Actions </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="group in groups" :key="group.id">
                            <td>{{group.name}}</td>
                            <td>{{group.description}}</td>
                            <td>
                                <a class="btn btn-xs btn-primary" title="Edit Group" style="color:white">
                                    <i class="fa fa-edit"></i>
                                    </a>
                                <a class="btn btn-xs btn-primary" @click="viewUsers(group.id)" title="List Users" style="color:white">
                                    <i class="fa fa-users"></i>
                                    </a>
                                
                                <a class="btn btn-xs btn-primary" @click="showAssignModal(group.id)" title="Assign Roles" style="color:white">
                                    <i class="fa fa-user-shield"></i>
                                    </a>
                                <a class="btn btn-xs btn-primary" title="Delete Role" style="color:white">
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

                <b-modal id="group-users" hide-footer>
                    <template v-slot:modal-title>
                   <h4> <b>ROLE [ {{ group[0].name.toUpperCase() }} ] USERS</b></h4>
                    </template>
                    <div>
                        <ul>
                            <li>list of users in this role</li>
                        </ul>
                    
                    </div>
                </b-modal>

                <b-modal id="assign-group" size="lg" hide-footer>
                    <template v-slot:modal-title>
                   <h4> <b>ASSIGN ROLES TO GROUP [ {{ group[0].name.toUpperCase() }} ]</b></h4>
                    </template>
                    <div>
                        <vue-select-sides
                        type="mirror"
                        v-model="selectedRoles"
                        :list="list"
                        ></vue-select-sides>
                    
                    </div>
                    <b-button class="mt-3" @click="assignButtonClicked(group[0].id)" block><i class="fa fa-user-shield"></i> Assign Roles</b-button>
                </b-modal>


  
  </div>
</template>

<script>
export default {
    data() {
        return {
            response:null,
            groups: null,
            pageMeta: null,
            group: null,
            list: [],
            selectedRoles: [],
            users: []
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
        this.refreshObjects( this.apiQueryString);

        /**list roles * i can only pull in 10 at a time, need help getting all at 
         * the same time unless allowed to tamper with the backend **/

       let roles = (await axios.get("/api/v1/admin/access-control/permission-groups",{ params: { limit: 100 } })).data.data;
        roles.forEach(single => {
            this.list.push({
                label: single.name,
                value: single.id
                })
        });


    },
    methods:{
        assignButtonClicked(groupId){
            let data = {
                roles: this.selectedRoles,
                groupId: groupId
            }

            this.$emit('assignGroupToRoles', data);


        },
        showAssignModal(groupId){
            this.group = this.groups.filter(group => group.id === groupId);
            this.$bvModal.show('assign-group')

        },
        
       dispatchRouting() {
            this.refreshObjects( this.apiQueryString);
        },

        refreshObjects( apiQueryString){
            axios.get('/api/v1/admin/access-control/roles' + apiQueryString).then( (response ) => {
               this.response = response
                this.groups = this.response.data.data;
                
                this.pageMeta = this.response.data.meta
            });
        },
        async viewUsers(groupId){
            this.group = this.groups.filter(group => group.id === groupId);
            await axios.get('/api/v1/admin/access-control/roles/' + groupId +'/users',{ params: { limit: 100 } }).then( response => {
                this.response = response;
                this.users = this.response.data.data;
            }).catch(error => {
                console.log(error);
            });

            this.$bvModal.show('group-users');
        }
        
    }

}
</script>



