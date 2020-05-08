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
                        <span class="upload-text ml-3">{{item.status}}</span>
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
                case this.$t('ingest.processing.status.closed'):
                    return 2;
                case this.$t('ingest.processing.status.bag_files'):
                    return 20;
                case this.$t('ingest.processing.status.move_to_outbox'):
                    return 50;
                case this.$t('ingest.processing.status.initiate_transfer'):
                    return 54;
                case this.$t('ingest.processing.status.approve_transfer'):
                    return 58;
                case this.$t('ingest.processing.status.transferring'):
                    return 60;
                case this.$t('ingest.processing.status.ingesting'):
                    return 80;
                case this.$t('ingest.processing.status.complete'):
                    return 90;
                case this.$t('ingest.processing.status.creatingStatus'):
                    return 100;
                default:
                    0;
            }
        },
        progressBarStyle: function() {
            return  `width: ${this.progressPercentage}%;`;
        }
    }
}
</script>
