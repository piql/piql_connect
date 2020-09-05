<template>
<div class="table-responsive">
    <table class="table table-hover table-sm table-bordered">
        <thead>
            <tr>
                <th>{{$t('settings.archives.archive')}}</th>
                <th>{{$t('settings.archives.archiveDesc')}}</th>
                <th>{{$t('settings.archives.createdAt')}}</th>
                <th width="20%">{{$t('settings.archives.actions')}}</th>
            </tr>
        </thead>
        <tbody>
            <tr v-if="retrievedArchives.length <= 0">
                <td colspan="4" class="columnCenter">{{$t('settings.archives.noArchives')}}</td>
            </tr>
            <tr v-else v-for="archive in retrievedArchives" :key="archive.key">
                <td>{{ archive.title }}</td>
                <td>{{ archive.description }}</td>
                <td>{{ formatShortDate(archive.created) }}</td>
                <td>
                    <a class="btn" @click="assignMeta(archive.id)" data-toggle="tooltip" :title="$t('settings.archives.assignMeta')">
                        <i class="fa fa-tags buttonIcon"></i>
                    </a>
                     <a class="btn" data-toggle="tooltip" :title="$t('settings.archives.assignMetaTemplate')">
                        <i class="fa fa-list buttonIcon"></i>
                    </a>
                    <a class="btn" data-toggle="tooltip" :title="$t('settings.archives.edit')">
                        <i class="fa fa-edit buttonIcon"></i>
                    </a>
                    <a class="btn" data-toggle="tooltip" :title="$t('settings.archives.delete')">
                        <i class="fa fa-trash buttonIcon"></i>
                    </a>
                </td>
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
        ...mapActions(['fetchArchives']),
        assignMeta(id){
            this.$emit('assignMeta', id);
        }
    }

}
</script>
