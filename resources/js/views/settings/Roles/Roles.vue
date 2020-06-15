<template>
    <div class="w-100">
        <page-heading icon="fa-user-secret" :title="$t('settings.settings.roles')" :ingress="$t('settings.settings.rolesdesc')" />
        <div class="card">
            <div class="card-header">
        
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addModal">
                    <i class="fa fa-plus"></i>  Add Role
                </button>
            </div>
            <div class="card-body">
               <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModal" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                        
                        <form role="form" method="post">
                            <fieldset>
                                <div class="modal-body">
                                    <legend>Add Role </legend>
                                    <div class="form-group">
                                        <label>Role</label>
                                        <input type="text" class="form-control" v-model="role" >
                                    </div>
                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea v-model="description" class="form-control"></textarea>
                                    </div>
                                   
                                
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i>Add Role</button>
                                </div>
                            </fieldset>

                        </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
    </div>
</template>

<script>

    export default {
       
        data() {
            return {
                role:null,
                description:null,
            };
        },

        methods: {
        }
    }
</script>

