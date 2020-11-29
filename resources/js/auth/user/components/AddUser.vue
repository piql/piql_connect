<template>
    <div>
      <div>
        <strong class="text-danger center error" id="add-user-modal-error">{{errorMessage}}</strong>
      </div>
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
          <b-table id="input-attributes" :fields="attributes.fields" :items="form.attributes" hover>
            <template #cell(actions)="data">
              <span class="action"
                @click="removeAttribute(data.item.id)"
                :title="$t('text.remove') + ' ' + data.item.attribute"
              >x</span>
            </template>
          </b-table>
        </b-form-group>

        <b-button type="submit" variant="primary">{{$t('settings.settings.addUser')}}</b-button>
      </b-form>

      <CollectorModal title="modal.user.title.attributes" :attr="attributes.dataLabels" @data="addAttribute" />
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
    error: String
  },
  data(){
    return {
      errorMessage: null,
      actionOptions: [],
      attributes:{
        autoId: 0,
        fields: [
          {key: 'attribute', label: this.$t('user.label.attribute')},
          {key: 'value', label: this.$t('user.label.value')},
          {key: 'actions', label: this.$t('user.label.actions')},
        ],
        dataLabels: [
          'user.label.attribute',
          'user.label.value'
        ]
      },
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
    },
    error(){
      this.errorMessage = this.error || null;
      if(this.errorMessage != null) {
        document.getElementById('add-user-modal-error').scrollIntoView({behavior: "smooth"});
      }
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
      this.error = null;
      this.form = {
        firstname: '',
        lastname: '',
        email: '',
        username: '',
        requiredActions: [],
        attributes: [],
      };
      this.addAttribute({ attribute: 'organisation', value: this.organization })
      this.addAttribute({ attribute: 'rows-per-page', value: 8 })
      this.addAttribute({ attribute: 'language', value: this.language })
    },
    showAttributeForm(){
      this.$bvModal.show('collector-modal-user-title-attributes')
    },
    addAttribute(data){
      let id = this.attributes.autoId + 1;
      this.form.attributes.push({id, ...data});
      this.attributes.autoId++
    },
    removeAttribute(id) {
      let attr = this.form.attributes.find(a => a.id == id);
      if(!confirm(`${this.$t('text.remove')} ${attr.attribute}`)) return;
      let index = this.form.attributes.findIndex(a => a.id == id);
      this.form.attributes.splice(index, 1);
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
      this.form.requiredActions.forEach(a => data.requiredActions.push(a))
      this.form.attributes.forEach(a => data.attributes.push({attribute: a.attribute, value: a.value}))
      this.$emit('dataAvailable', data);
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
span.action {
  align-self: center;
  color: red;
}
span.action:hover {
  font-weight: bold;
  font-size: 110%;
  cursor: pointer;
}
strong.error {
  margin-bottom: 10px;
}
</style>
