<template>
  <b-modal id="edit-user" hide-footer>
        <template v-slot:modal-title>
        <h4>{{$t('settings.settings.editUser').toUpperCase()}} </h4>
        </template>
        <div class="d-block">
            <div class="form-group">
                <label>{{$t('settings.listing.fullname')}}</label>
                <input type="text" class="form-control" v-model="name" required>
            </div>
            <div class="form-group">
                <label>{{$t('settings.listing.username')}}</label>
                <input type="text" class="form-control" v-model="username" disabled>
            </div>
            <div class="form-group">
                <label>{{$t('settings.listing.email')}}</label>
                <input type="email" class="form-control" v-model="email" required>
            </div>
        </div>
        <b-button class="mt-3" block @click="editUser" @keydown="editUser">
            <i class="fa fa-edit"></i> {{$t('settings.settings.editUser')}} </b-button>
    </b-modal>
</template>

<script>
export default {
  props: {
    user: Object
  },
  data(){
    return {
      name:null,
      email:null,
      username:null,
    }
  },
  watch:{
    user(){
      this.name = ""
      if(this.user.firstName !== '') this.name += this.user.firstName;
      if(this.user.lastName !== '') this.name += ' ' + this.user.lastName;
      this.name = this.name.trim();
      this.email = this.user.email
      this.username = this.user.username
    }
  },
  methods: {
    editUser() {
      this.$emit('edited-user', {
        fullname: this.name,
        email: this.email,
        username: this.username,
      })
    }
  },
  computed: {
    fullName(){
      return `${this.user.firstName} ${this.user.lastName}`
    }
  }
}
</script>

<style>

</style>