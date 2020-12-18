<template>
  <b-modal id="disable-user" hide-footer>
        <template v-slot:modal-title>
        <h3><b> {{$t('settings.settings.disableUser').toUpperCase()}} : {{ fullName }} </b></h3>
        </template>
        <div class="d-block">
            <div class="alert alert-warning">
                {{$t('settings.settings.disableUserWarning')}}
            </div>
        </div>
        <b-button class="mt-3" block @click="disableUser" @keydown="disableUser">
            <i class="fa fa-ban"></i> {{$t('settings.settings.disableUser')}} </b-button>
    </b-modal>
</template>

<script>
export default {
  props: {
    user: Object
  },
  methods: {
    disableUser() {
      this.$emit('disabled-user', this.user.id)
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