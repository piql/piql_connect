export default {
    methods: {
        infoToast: function( title, message, replacements = [], hideAfter = 3, toaster = 'b-toaster-bottom-right' ) {

            let _msg = message.replace(/%\w+%/g, r => {
                return `"${replacements[ r.replace(/%+/g,"") ] || r }"`;
            });
            let _title = title.replace(/%\w+%/g, r => {
                return `"${replacements[ r.replace(/%+/g,"") ] || r }"`;
            });

            this.$bvToast.toast( _msg, {
                title: _title,
                autoHideDelay: 1000 * hideAfter,
                toaster: toaster
            });
        },
        errorToast: function( title, message, replacements = [], hideAfter = 3, toaster = 'b-toaster-bottom-right' ) {

            let _msg = message.replace(/%\w+%/g, r => {
                return `"${replacements[ r.replace(/%+/g,"") ] || r }"`;
            });
            let _title = title.replace(/%\w+%/g, r => {
                return `"${replacements[ r.replace(/%+/g,"") ] || r }"`;
            });

            this.$bvToast.toast( _msg, {
                title: _title,
                autoHideDelay: 1000 * hideAfter,
                toaster: toaster,
                variant: "danger"
            });
        }

    }
}
