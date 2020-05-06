import { format } from 'date-fns';
export default {
    methods: {
        formatShortDate( apiFormattedDate ) {
            return format( new Date( apiFormattedDate ), this.$t('shortDateFormat') );
        }
    }
};
