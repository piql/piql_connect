<template>
<div class="table-responsive">
    <table class="table table-hover table-sm table-bordered">
        <thead>
            <tr>
                <th>{{$t('settings.archives.archive')}}</th>
                <th>{{$t('settings.archives.createdAt')}}</th>
                <th>{{$t('settings.archives.actions')}}</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="archive in retrievedArchives" :key="archive.key">
                <td>{{ archive.title }}</td>
                <td>{{ archive.created }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

</div>
  
</template>

<script>
import { mapGetters, mapActions } from "vuex"
export default {
    async mounted(){
        this.fetchArchives();

    },
    computed: {
        ...mapGetters(['retrievedArchives']),
    },
    methods: {
        ...mapActions(['fetchArchives'])
    }

}
</script>
