<template>
    <div>
      <b-form @submit="submitForm">

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
                v-model="form.requiredActions" :options="actions" :select-size="3" multiple required>
            </b-form-select>
        </b-form-group>

        <b-form-group class="ml-1" label-for="input-attributes" label="">
          <b-button right @click="showAttributeForm">+ {{$t('modal.user.title.attributes')}}</b-button>
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
    user: Object,
    actions: Array,
    organization: String,
    language: String,
  },
  data(){
    return {
      attributeLabels: [
        'user.label.attribute',
        'user.label.value'
      ],
      form: {
        firstname: '',
        lastname: '',
        email: '',
        username: '',
        requiredActions: [],
        attributes: [],
      }
    }
  },
  mounted(){
    this.resetForm();
  },
  methods:{
    resetForm() {
      this.form.firstname='';
      this.form.lastname='';
      this.form.email='';
      this.form.username='';
      this.form.requiredActions=[];
      this.form.attributes=[
        { attribute: 'organisation', value: this.organization },
        { attribute: 'rows-per-page', value: 8 },
        { attribute: 'language', value: this.language },
      ];
    },
    showAttributeForm(){
      this.$bvModal.show('collector-modal-user-title-attributes')
    },
    appendAttributes(data){
      this.form.attributes.push(data);
    },
    submitForm(e){
      e.preventDefault();
      console.log(this.form);
      this.$emit('addUser', this.form);
      this.resetForm();
    }
  }
}
</script>
