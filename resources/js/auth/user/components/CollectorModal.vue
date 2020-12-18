<template>
  <b-modal class="collector-modal" :id="glue('collector', title)" :title="$t(title)" @show="resetModal" @hidden="resetModal" @ok="handleOk">
    <div>
      <div>
        <strong class="text-danger center error">{{error}}</strong>
      </div>
      <b-form @submit.stop.prevent="handleSubmit" :ref="glue('collector-form', title)">
        <b-form-group v-for="a in attr" :key="glue('input-', a)" :id="glue('input', a)" :label-for="glue('input', a)" :label="$t(a)">
          <b-form-input :id="glue('input', a)" class="mb-4" :placeholder="$t(a)" v-model="form[fieldName(a)]" required></b-form-input>
        </b-form-group>
      </b-form>
    </div>
    <template #modal-footer="{ ok, cancel }">
      <b-button variant="success" @click="ok()">{{$t('OK')}}</b-button>
      <b-button variant="default" @click="cancel()">{{$t('Cancel')}}</b-button>
    </template>
  </b-modal>
</template>

<script>
export default {
  props: {
    attr: Array,
    title: String,
  },
  data(){
    return {
      error: null,
      form: {}
    }
  },
  mounted(){
    this.resetModal();
  },
  methods:{
    glue(...text){
      return text.join('-').replace(/[,. ]/gi, '-');
    },
    fieldName(label){
      let index = label.lastIndexOf('.');
      if(index == -1) return label;
      return label.substring(index+1)
    },
    resetModal() {
      this.error = null;
      this.form = {};
      this.attr.forEach(a => {
        this.form[this.fieldName(a)] = '';
      })
    },
    handleOk(e) {
      e.preventDefault();
      this.handleSubmit();
    },
    handleSubmit() {
      this.error = null;
      let ref = this.glue('collector-form', this.title);
      if (!this.$refs[ref].checkValidity()) {
        this.error = this.$t('form.validation.error');
        return;
      }
      this.$emit('data', this.form);
      this.$nextTick(() => {
        this.$bvModal.hide(this.glue('collector', this.title));
        this.resetModal();
      });
    },
  }
}
</script>

<style scoped>
h5 {
  font-size: 1.8rem;
  margin-left: 0px;
}

strong.error {
  margin-bottom: 10px;
}
</style>