<template>
    <div class="w-100">
        <page-heading icon="fa-users" :title="$t('settings.settings.groups')" :ingress="$t('settings.settings.groupsdesc')" />
        <div class="card">
            <div class="card-header">
        
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addModal">
                    <i class="fa fa-plus"></i>  Add Group
                </button>
            </div>
            <div class="card-body">
                <usergroups />
               <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModal" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                        
                        <div role="form">
                            <fieldset>
                                <div class="modal-body">
                                    <legend>Add Group </legend>
                                    <div class="form-group">
                                        <label>Group</label>
                                        <input type="text" class="form-control" v-model="group" >
                                    </div>
                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea v-model="description" class="form-control"></textarea>
                                    </div>
                                   
                                
                                </div>
                                <div class="modal-footer">
                                    <button @click="addGroup" @keydown.enter="addGroup" class="btn btn-primary"><i class="fa fa-plus"></i>Add Group</button>
                                </div>
                            </fieldset>

                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
    </div>
</template>

<script>
import usergroups from "./components/usergroups";

    export default {
        components:{
            usergroups
        },
       
        data() {
            return {
                group:null,
                description:null,
                response: null
            };
        },

        methods: {
            async addGroup(){
                 this.response = (await axios.post("/api/v1/admin/permissions/groups", {
                    name: this.group,
                    description: this.description
                },{
                    headers:{
                        'content-type': 'application/json'
                    }
                })).data;
            }
        }
    }
</script>

