<template>
    <div>
      <b-form ref="add_user_form" @submit.stop.prevent="submitForm">
        <b-form-group id="input-firstname" label-for="input-firstname" :label="$t('settings.listing.firstname')">
            <b-form-input id="input-firstname" class="mb-4"
                :placeholder="$t('settings.listing.firstname')"
                v-model="form.firstname" required>
            </b-form-input>
        </b-form-group>

        <b-form-group id="input-lastname" label-for="input-lastname" :label="$t('settings.listing.lastname')">
            <b-form-input id="input-lastname" class="mb-4"
                :placeholder="$t('settings.listing.lastname')"
                v-model="form.lastname" required>
            </b-form-input>
        </b-form-group>

        <b-form-group id="input-username" label-for="input-username" :label="$t('settings.listing.username')">
            <b-form-input id="input-username" class="mb-4"
                :placeholder="$t('settings.listing.username')"
                v-model="form.username" required>
            </b-form-input>
        </b-form-group>

        <b-form-group id="input-email" label-for="input-email" :label="$t('settings.listing.email')">
            <b-form-input  id="input-email" class="mb-4"
                :placeholder="$t('settings.listing.email')"
                v-model="form.email" required>
            </b-form-input>
        </b-form-group>

        <b-form-group id="input-onCreateAction" label-for="input-onCreateAction" :label="$t('user.form.post-actions')">
            <b-form-select id="input-onCreateAction" class="mb-4"
                :placeholder="$t('user.form.post-actions')"
                v-model="form.requiredActions" :options="actionOptions"
                :select-size="3" multiple required>
            </b-form-select>
            <span right class="hint">{{$t('user.form.post-actions.hint')}}</span>
        </b-form-group>

        <b-form-group class="ml-1" label-for="input-attributes" label="">
          <b-button right @click="showAttributeForm">+ {{$t('modal.user.title.attributes')}}</b-button>
          <!-- TODO: support for deleting/editing form attributes in table-->
          <b-table id="input-attributes" hover :items="form.attributes"></b-table>
        </b-form-group>

        <b-button type="submit" variant="primary">{{$t('settings.settings.addUser')}}</b-button>
      </b-form>

      <CollectorModal title="modal.user.title.attributes" :attr="attributeLabels" @data="appendAttributes" />
    </div>
</template>

<script>
import CollectorModel from './CollectorModal';

export default {
  components:{
    CollectorModel
  },
  props: {
    actions: Array,
    organization: String,
    language: String,
    formLoaded: Boolean,
  },
  data(){
    return {
      actionOptions: [],
      attributeLabels: [
        // attributes consumed by the generic form modal
        'user.label.attribute',
        'user.label.value'
      ],
      form: {
        requiredActions: [],
      }
    }
  },
  mounted(){
    this.resetForm();
    this.updateActions();
  },
  watch: {
    actions() {
      this.updateActions();
    },
    formLoaded() {
      //we need to do away with this by creating a route for create user page
      if(this.formLoaded) this.resetForm();
    }
  },
  methods:{
    updateActions() {
      this.actionOptions = [];
      this.actions.forEach(a => {
        this.actionOptions.push({
          value: a.value,
          text: this.$t(a.text)
      })});
    },
    resetForm() {
      this.form = {
        firstname: '',
        lastname: '',
        email: '',
        username: '',
        requiredActions: [],
        attributes: [
          { attribute: 'organisation', value: this.organization },
          { attribute: 'rows-per-page', value: 8 },
          { attribute: 'language', value: this.language },
        ],
      };
    },
    showAttributeForm(){
      this.$bvModal.show('collector-modal-user-title-attributes')
    },
    appendAttributes(data){
      this.form.attributes.push(data);
    },
    submitForm(e){
      e.preventDefault();
      let data = {
        firstName: this.form.firstname,
        lastName: this.form.lastname,
        email: this.form.email,
        username: this.form.username,
        attributes: [],
        requiredActions: [],
      }
      this.form.attributes.forEach(a => data.attributes.push({attribute: a.attribute, value: a.value}))
      this.form.requiredActions.forEach(a => data.requiredActions.push(a))
      this.$emit('addUser', data);
    }
  }
}
</script>

<style scoped>
.hint {
  color: #6e6e6e;
  font-size: 72%;
  margin-bottom: 1.5rem;
}
#input-onCreateAction{
  margin-bottom: 0.5rem!important;
}
</style>
