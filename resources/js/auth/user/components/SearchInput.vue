<template>
  <div class="user-search">
    <b-input-group class="mt-3 no-border">
      <b-form-input
        class="no-border"
        v-model="search.new"
        :placeholder="$t('settings.user.listing.search.placeholder')"
        @change="searchForUser"
        @keypress="searchForUser"
      ></b-form-input>
      <b-input-group-append class="no-border">
        <b-button
          v-if="searchInputAvailable"
          variant="outline-light"
          class="text-danger no-border"
          @click="clearSearch"
          >x</b-button
        >
      </b-input-group-append>
    </b-input-group>
  </div>
</template>

<script>
export default {
  data() {
    return {
      defer: {
          func: null,
          timeout: 700
      },
      search: {
        old: "",
        new: ""
      },
      error: "",
    };
  },
  computed: {
    searchInputAvailable() {
      return this.search.new.trim() != this.search.old;
    },
  },
  watch: {
    searchInputAvailable() {
      this.searchForUser();
    }
  },
  methods: {
    clearDeferFunc() {
        if(this.defer.func != null) clearTimeout(this.defer.func);
        this.defer.func = null;
    },
    clearSearch() {
      this.search.new = "";
    },
    searchForUser() {
      if (!this.searchInputAvailable) return;
      this.fetchUsers();
    },
  },
};
</script>

<style>
.no-border {
  border: 0;
  box-shadow: none; /* You may want to include this as bootstrap applies these styles too */
}
</style>
