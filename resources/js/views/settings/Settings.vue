<template>
    <div class="mb-2 mt-2 container-fluid">
        <div class="row">
            <div class="col-sm-1 text-left">
              <i class="fas fa-cog titleIcon"></i>
            </div>
            <div class="col-sm-6 text-left">
                <h1>{{$t('settings.settings.header')}}</h1>
            </div>
        </div>
        <div class="row mt-0 pt-0">
            <div class="col-sm-1"></div>
            <div class="col-sm-6 text-left ingressText">
                {{$t("settings.settings.ingress")}}
            </div>
        </div>

        <form>
            <language-picker v-bind:label="$t('Language')" :languages="languages" :initialSelection="selectedLanguage" @selectionChanged="changedLanguage"></language-picker>
        </form>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                languages: [],
                selectedLanguage: {}
            };
        },
	
        async mounted() {
            await axios.get("/api/v1/system/languages").then( (response) => {
                this.langages = response.data.data;
                this.selectedLanguage = this.languages[0].code;
            });

            console.log('Settings component mounted.')
        }
    }
</script>
