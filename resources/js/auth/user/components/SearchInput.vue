<template>
  <div class="user-search">
    <b-input-group class="mt-3 no-border">
      <b-form-input
        class="no-border"
        v-model="search"
        placeholder="search users"
        @keydown.enter="searchForUser"
      ></b-form-input>
      <b-input-group-append class="no-border">
        <b-button
          v-if="searchInputPresent"
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
      search: "",
      error: "",
      limit: 100,
    };
  },
  computed: {
    searchInputPresent() {
      return this.search.trim().length > 0;
    },
  },
  watch: {
    search() {
      if(this.search.length == 0) this.fetchUsers();
    }
  },
  methods: {
    clearSearch() {
      this.search = "";
    },
    fetchUsers(){
      let params = {
        query: { q: this.search.trim(), limit: this.limit },
        token: this.authBearerToken,
      };
      this.fetchChatUsers(params).then(() => {});
    },
    searchForUser() {
      if (!this.searchInputPresent) return;
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
