<template>
    <div v-if="modal">
        <div class="row plist">
            <div class="col text-left">
                {{item.name}}
            </div>
            <div class="col-2 text-center">
                {{fileSize}}
            </div>
            <div class="col-3 text-center">
                {{creationDate}}
            </div>
            <div class="col-3 text-center">
                {{ $t('ingest.status.preparing') }}
            </div>
        </div>
    </div>
</template>

<script>
import { format } from 'date-fns';

export default {
    methods: {
        getFileSizeSI(bytes) {
            let value = 0;
            let exp = 0;
            if (bytes) {
                exp = Math.floor(Math.log(bytes) / Math.log(1000));
                value = (bytes / Math.pow(1000, exp))
            }
            return Math.ceil(value) + " " + (exp ? 'KMGTPEZY'[exp - 1] + 'B' : 'Bytes')
        },
    },
    props: {
        item: {
            type: Object,
            default: {}
        }
    },
    computed: {
        fileSize: function() {
            if(this.item.size !== undefined)
                return this.getFileSizeSI(this.item.size);
            else
                return "---";
        },
        creationDate: function() {
            return format( new Date(this.item.created_at), this.$t('shortDateFormat') );
        },
    },
    data() {
        return {
            modal: true,
        };
    },

}
</script>
