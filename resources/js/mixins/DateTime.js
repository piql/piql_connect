import { format, isValid } from 'date-fns';
export default {
    methods: {
        formatShortDate( apiFormattedDate ) {
            let date = new Date( apiFormattedDate );
            return isValid( date ) ? format( date, this.$t('shortDateFormat') ) : '';
        },
        isValidDateString: function( dateString ) {
            let jsDate = new Date( dateString );
            return isValid( jsDate );
        }

    }
};
