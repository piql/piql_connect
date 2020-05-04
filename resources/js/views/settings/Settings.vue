<template>
    <div class="w-100">
        <page-heading icon="fa-cog" :title="$t('settings.settings.header')" :ingress="$t('settings.settings.ingress')" />

        <form>
            <div class="row">
                <div class="col-10">
                    <language-picker v-bind:label="$t('Language')" :initialSelection="selectedLanguage" @selectionChanged="changedLanguage"></language-picker>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                userSettings: null
            };
        },

        async mounted() {
            this.userSettings = (await axios.get("/api/v1/system/currentUserSettings")).data;
        },

        methods: {
            async changedLanguage(language) {
                if (language && language != this.selectedLanguage) {
                    await axios.post("/api/v1/system/settings", {"interface": { "language": language }});
                    this.$router.go(0);
                }
            }
        },

        computed: {
            selectedLanguage() {
                if (this.userSettings && this.userSettings.interface && this.userSettings.interface.language) {
                    return this.userSettings.interface.language;
                }
                return '';
            }
        }
    }
</script>
