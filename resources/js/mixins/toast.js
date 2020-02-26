export default {
    methods: {
        infoToast: function( title, message, hideAfter = 3, toaster = 'b-toaster-bottom-right' ) {
            this.$bvToast.toast( message, {
                title: title,
                autoHideDelay: 1000 * hideAfter,
                toaster: toaster
            });
        }
    }
}
