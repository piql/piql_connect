<template>
  <div class="table-responsive">
      
       <table class="table table-hover table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>{{$t('settings.roles.role')}}</th>
                            <th>{{$t('settings.groups.description')}}</th>
                          
                            <th width="18%">{{$t('settings.settings.actions')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="role in userRoles" :key="role.id">
                            <td>{{role.name}}</td>
                            <td>{{role.description}}</td>
                            <td>
                                <a class="btn btn-xs btn-primary" @click="viewPermissions(role.id)" v-b-tooltip.hover :title="$t('settings.roles.confPermissions')" style="color:white">
                                    <i class="fa fa-user-cog"></i>
                                    </a>
                                <a class="btn btn-xs btn-primary" @click="showAssignModal(role.id)" v-b-tooltip.hover :title="$t('settings.roles.assignGroup')" style="color:white">
                                    <i class="fa fa-users"></i>
                                    </a>
                                <a class="btn btn-xs btn-primary" v-b-tooltip.hover :title="$t('settings.roles.editRole')" style="color:white" @click="showEditModal(role.id)">
                                    <i class="fa fa-edit"></i>
                                    </a>
                                <a class="btn btn-xs btn-primary" v-b-tooltip.hover  :title="$t('settings.roles.deleteRole')" @click="showDeleteModal(role.id)" style="color:white">
                                    <i class="fa fa-trash"></i>
                                    </a>
                            </td>
                        </tr>
                    
                       
                    </tbody>
                </table>
                <div class="row text-center pagerRow">
                    <div class="col">

                      
                        <Pager :meta='rolesPageMeta' :height='height' />
                    </div>
                </div>

                <b-modal id="delete-role" hide-footer>
                    <template v-slot:modal-title>
                        <h4> <b>{{$t('settings.roles.deleteRole').toUpperCase()}}[ {{ role[0].name.toUpperCase() }} ]</b></h4>
                    </template>
                    <div class="d-block">
                        <b-alert show variant="warning">{{$t('settings.roles.deleteRoleWarning')}}</b-alert>
                    </div>
                    <b-button class="mt-3" block @click="deleteRoleClicked(role[0].id)" @keydown="deleteRoleClicked(role[0].id)">
                        <i class="fa fa-trash"></i> {{$t('settings.roles.deleteRole').toUpperCase()}}</b-button>
                </b-modal>

                <b-modal id="edit-role" size="lg" hide-footer>
                    <template v-slot:modal-title>
                   <h4> <b>{{$t('settings.roles.editRole').toUpperCase()}} [ {{ role[0].name.toUpperCase() }} ]</b></h4>
                    </template>
                    <div class="d-block">
                        <div class="form-group">
                            <label>{{$t('settings.roles.role')}}</label>
                            <input type="text" class="form-control" v-model="roleName" >
                        </div>
                        <div class="form-group">
                            <label>{{$t('settings.groups.description')}}</label>
                            <textarea v-model="description" class="form-control"></textarea>
                        </div>
                    </div>
                    <b-button class="mt-3" @click="editButtonClicked(role[0].id)" block><i class="fa fa-edit"></i> {{$t('settings.roles.editRole').toUpperCase()}}</b-button>
                </b-modal>

                <b-modal id="assign-role" size="lg" hide-footer>
                    <template v-slot:modal-title>
                   <h4> <b>{{$t('settings.roles.assignGroupsToRole').toUpperCase()}} [ {{ role[0].name.toUpperCase() }} ]</b></h4>
                    </template>
                    <div class="form-group">
                        <label for="group">{{$t('settings.settings.userGroups')}}</label>
                        <select v-model="selGroup" id="group" class="form-control">
                            <option v-for="group in list" :key="group.value" :value="group.value">
                                {{ group.label }}
                            </option>

                        </select>
                    </div>
                    <b-button class="mt-3" @click="assignButtonClicked(role[0].id)" block><i class="fa fa-group"></i> 
                    {{$t('settings.roles.assignGroup').toUpperCase()}}</b-button>
                </b-modal>
                
              
  
  
  
  </div>

</template>

<script>
import { mapGetters, mapActions } from "vuex";
export default {
    data() {
            return {
                roleId: null,
                role: null,
                roleName: null,
                description: null,
                list:[],
                selGroup:[]
               
            };
    },

    props: {
        height: {
            type: Number,
            default: 0
        }
    },
    computed:{
        ...mapGetters(['userGroups','userRoles','rolesPageMeta']),
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
        '$route': 'dispatchRouting',
        userGroups(newValue,oldValue){
            if(newValue){
                newValue.forEach(single => {
                    this.list.push({
                        label: single.name,
                        value: single.id
                        })
                });
            }
        }
    },
    async mounted() {
       let page = this.$route.query.page;
        if( isNaN( page ) || parseInt( page ) < 2 ) {
            this.$route.query.page = 1;
        }
        this.fetchRoles(this.apiQueryString );

        this.fetchGroups('',{limit: 100});
        

        
    },
    methods:{
        ...mapActions(['fetchGroups','fetchRoles']),
        assignButtonClicked(roleId){
            let data = {
                roles: [roleId],
                groupId: this.selGroup
            }

            this.$emit('assignGroup', data);
 

        },
        editButtonClicked(roleId){
            let data = {
                name: this.roleName,
                description: this.description,
                roleId: roleId
            }

            this.$emit('editRole', data);


        },
        showAssignModal(roleId){
            this.role = this.userRoles.filter(role => role.id === roleId);
            this.$bvModal.show('assign-role')

        },
        showEditModal(roleId){
            this.role = this.userRoles.filter(role => role.id === roleId);
            this.roleName = this.role[0].name;
            this.description = this.role[0].description;
            this.$bvModal.show('edit-role');

        },
       dispatchRouting() {
            this.fetchRoles( this.apiQueryString );
        },
       showDeleteModal(roleId){
           this.role = this.userRoles.filter(role => role.id === roleId);
           this.$bvModal.show('delete-role');

        },
        deleteRoleClicked(roleId){
            this.$emit('deleteRole', roleId);

        },

        viewPermissions(roleId){
          this.$router.push({ name:'settings.roles.permissions', params: { roleId: roleId } });
        },
        
    }


}
</script>

<style>

</style>