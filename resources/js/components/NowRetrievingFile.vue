<template>
    <div>
        <div class="row plist">
            <div class="col-sm-6">
                {{item.filename}}
            </div>
            <div class="col-sm-2 text-center">
                {{friendlyDate}}
            </div>
            <div class="col-sm-2 text-right">
                {{item.filesize}}
            </div>
            <div class="col-sm-2 text-center">
                <div class="spinner-border" role="status">
                    <span class="sr-only">Retrieving</span>
                </div>
            </div>
        </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment';
export default {
    async mounted() {
        let bagId = this.item.id;
    },
    data() {
        return {
            bag: {},
        };
    },
    props: {
        item: Object
    },
    computed: {
        friendlyDate: function(){
            return moment(this.item.created_at).format('L');
        }
    },
}
</script>
