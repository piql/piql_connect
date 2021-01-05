<template>
  <b-modal id="edit-user" hide-footer>
    <template v-slot:modal-title>
      <h4>{{$t('settings.settings.editUser').toUpperCase()}} </h4>
    </template>
    <div class="d-block">
        <div class="ml-1 text-danger">
          <strong>{{error}}</strong>
        </div>
        <div class="form-group">
            <label>{{$t('settings.listing.firstname')}}</label>
            <input type="text" class="form-control" v-model="firstname" required>
        </div>
        <div class="form-group">
            <label>{{$t('settings.listing.lastname')}}</label>
            <input type="text" class="form-control" v-model="lastname" required>
        </div>
        <div class="form-group">
            <label>{{$t('settings.listing.username')}}</label>
            <input type="text" class="form-control" v-model="username" disabled>
        </div>
        <div class="form-group">
            <label>{{$t('settings.listing.email')}}</label>
            <input type="email" class="form-control" v-model="email" disabled>
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
      firstname:null,
      lastname:null,
      email:null,
      username:null,
      error: null,
    }
  },
  watch:{
    user(){
      this.firstname = this.user.firstName;
      this.lastname = this.user.lastName;
      this.email = this.user.email;
      this.username = this.user.username;
      this.error = this.user.error;
    }
  },
  methods: {
    editUser() {
      this.$emit('edited-user', {
        id: this.user.id,
        firstName: this.firstname,
        lastName: this.lastname,
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