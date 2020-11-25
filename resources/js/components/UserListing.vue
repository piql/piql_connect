<template>
  <div>
    <table class="table table-hover table-sm table-bordered">
        <thead>
            <tr>
                <th>{{$t('settings.listing.fullname')}}</th>
                <th>{{$t('settings.listing.username')}}</th>
                <th>{{$t('settings.listing.email')}}</th>
                <th>{{$t('settings.listing.status')}}</th>
                <th>{{$t('settings.listing.created')}}</th>
                <th>{{$t('settings.settings.actions')}}</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="staff in users" :key="staff.id">
                <td>{{staff.firstName}} {{staff.lastName}}</td>
                <td>{{staff.username}}</td>
                <td>{{staff.email}}</td>
                <td v-if="staff.disabled === true"> <b-badge variant="danger"> Disabled</b-badge></td>
                <td v-else> <b-badge variant="success"> Active</b-badge></td>
                <td>{{ formatDate(staff.createdTimestamp) }}</td>
                <td>
                    <a class="btn btn-xs btn-primary" data-toggle="tooltip" :title="$t('settings.settings.editUser')" style="color:white" @keydown="showEditModal(staff.id)" @click="showEditModal(staff.id)">
                        <i class="fa fa-edit"></i>
                    </a>
                    <a v-if="staff.disabled === true" class="btn btn-xs btn-primary" data-toggle="tooltip" :title="$t('settings.settings.enableUser')" style="color:white" @keydown="showEnableModal(staff.id)" @click="showEnableModal(staff.id)">
                        <i class="fa fa-plug"></i>
                    </a>
                    <a v-else class="btn btn-xs btn-primary" data-toggle="tooltip" :title="$t('settings.settings.disableUser')" style="color:white" @keydown="showDisableModal(staff.id)" @click="showDisableModal(staff.id)">
                        <i class="fa fa-ban"></i>
                    </a>
                </td>
            </tr>
        </tbody>
    </table>

    <DisableUserModal :user="user" @disabled-user="disableButtonClicked"/>
    <EnableUserModal :user="user" @enabled-user="enableButtonClicked"/>
    <EditUserModal :user="user" @edited-user="editButtonClicked"/>

  </div>
</template>

<script>
import DisableUserModal from './../auth/user/settings/components/DisableUserModal';
import EnableUserModal from './../auth/user/settings/components/EnableUserModal';
import EditUserModal from './../auth/user/settings/components/EditUserModal';
export default {
    data() {
        return {
            user:null
        };
    },
    props:{
        users: Array
    },
    components:{
        DisableUserModal, EnableUserModal, EditUserModal
    },
    methods:{
        setUser(id){
            this.user = {}
            let users = this.users.filter(user => user.id === id);
            if(users.length > 0) this.user = users[0];
        },
        showDisableModal(id){
            this.setUser(id);
            this.$bvModal.show('disable-user');
        },
        showEnableModal(id){
            this.setUser(id);
            this.$bvModal.show('enable-user');
        },
        showEditModal(id){
            this.setUser(id);
            this.$bvModal.show('edit-user');
        },
        disableButtonClicked(id){
            this.$emit('disableUser', {users: [id]});
        },
        enableButtonClicked(id){
            this.$emit('enableUser', {users: [id]});
        },
        editButtonClicked(){
            //some data will be passed here before emitting
            this.$emit('editUser');
        },
        formatDate(ISOdate){
            let date = new Date(ISOdate);
            let year = date.getFullYear();
            let month = date.getMonth()+1;
            let dt = date.getDate();
            let time = date.getHours() + ':'+ date.getMinutes();

            if (dt < 10) {
                dt = '0' + dt;
            }
            if (month < 10) {
                month = '0' + month;
            }
            return year+'-' + month + '-'+dt + ' '+time;
        },
    }
}
</script>

<style>

</style>
