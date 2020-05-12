<template>
    <div>
        <div class="row plist">
            <div class="col-5">
                {{item.name}}
            </div>
            <div class="col-3 text-center">
                {{formatShortDate( item.created_at )}}
            </div>

            <div class="col-4">
                <div class="text-truncate progress upload-progress bg-fill">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-brand text-left" role="progressbar" v-bind:style="progressBarStyle"
                        v-bind:aria-valuenow="progressPercentage" aria-valuemin="0" aria-valuemax="100">
                        <span class="upload-text ml-3">{{translatedStatus}}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
</template>

<script>
export default {
    data() {
        return {
        };
    },
    props: {
        item: Object
    },
    computed: {
        progressPercentage() {
            switch( this.item.status ) {
                case 'closed':
                    return 2;
                case 'bag_files':
                    return 20;
                case 'move_to_outbox':
                    return 30;
                case 'initiate_transfer':
                    return 40;
                case 'approve_transfer':
                    return 50;
                case 'transferring':
                    return 60;
                case 'ingesting':
                    return 80;
                case 'complete':
                    return 100;
                default:
                    0;
            }
        },
        progressBarStyle: function() {
            return  `width: ${this.progressPercentage}%;`;
        },
        translatedStatus: function() {
            let statusText = `ingest.processing.status.${this.item.status}`;
            return this.$t(statusText);
        }
    }
}
</script>
