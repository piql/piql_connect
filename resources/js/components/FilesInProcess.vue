<template>
  <div>
      <table class="table table-hover">
          <thead>
              <tr>
                  <th width="40%">{{$t('ingest.processing.sip')}}</th>
                  <th width="20%">{{$t('ingest.processing.ingestDate')}}</th>
                  <th width="40%">{{$t('ingest.processing.status')}}</th>
              </tr>
          </thead>
          <tbody>
              <tr v-if="currentlyIdle"><td colspan="3" style="text-align:center">{{$t('ingest.processing.noItems')}}</td></tr>
               <tr v-else v-for="item in items" :key="item.id">
                        <td>{{item.name}}</td>
                    <td>
                        {{formatShortDate( item.created_at )}}
                    </td>
                    <td>
                        <div class="text-truncate progress upload-progress bg-fill">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-brand text-left" role="progressbar" v-bind:style="item.progressBarStyle"
                                v-bind:aria-valuenow="item.progressPercentage" aria-valuemin="0" aria-valuemax="100">
                                <span class="upload-text ml-3">{{item.translatedStatus}}</span>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
  </div>
</template>

<script>
export default {
    data() {
        return {
        };
    },
    props: {
        items: Array,
        currentlyIdle: Boolean
    }

}
</script>
