<template>
  <b-modal id="enable-user" hide-footer>
        <template v-slot:modal-title>
        <h3><b> {{$t('settings.settings.enableUser').toUpperCase()}} : {{ fullName }} </b></h3>
        </template>
        <div class="d-block">
            <div class="alert alert-info">
                {{$t('settings.settings.enableUserWarning')}}
            </div>
        </div>
        <b-button class="mt-3" block @click="enableUser" @keydown="enableUser">
            <i class="fa fa-plug"></i> {{$t('settings.settings.enableUser')}} </b-button>
    </b-modal>
</template>

<script>
export default {
  props: {
    user: Object
  },
  methods: {
    enableUser() {
      this.$emit('enabled-user', this.user.id)
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