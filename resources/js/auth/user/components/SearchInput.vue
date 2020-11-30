<template>
  <div class="user-search">
    <b-input-group class="mt-3" :class="{noBorder: !showBorder}">
      <b-form-input :class="{noBorder: !showBorder}" v-model="searchText"
         :placeholder="$t(label)" @change="search" @keypress="search">
      </b-form-input>
      <b-input-group-append :class="{noBorder: !showBorder}">
        <b-button v-if="searchInputAvailable" :class="{noBorder: !showBorder}" @click="clearSearch">x</b-button>
      </b-input-group-append>
    </b-input-group>
  </div>
</template>

<script>
export default {
  props: {
    label: String,
    timeout: Number,
    showBorder: Boolean,
  },
  data() {
    return {
      searchText: "",
      defer: {
          func: null,
          timeout: 0, //set to zero to trigger search func without debounce delay
      },
    };
  },
  mounted(){
    this.defer.timeout = this.timeout || 0;
  },
  computed: {
    searchInputAvailable() {
      return this.searchText.trim().length > 0;
    },
    border(){
      return this.showBorder || false;
    }
  },
  watch: {
    searchInputAvailable() {
      this.search();
    }
  },
  methods: {
    clearDeferFunc() {
      if(this.defer.func != null) clearTimeout(this.defer.func);
      this.defer.func = null;
    },
    clearSearch() {
      this.searchText = "";
    },
    search() {
      this.clearDeferFunc();
      this.defer.func = setTimeout(() => {
        this.$emit('data', this.searchText);
      }, this.defer.timeout);
    },
  },
};
</script>

<style>
.no-border {
  border: 0;
  box-shadow: none;
}
</style>
