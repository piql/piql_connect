export default {

    data () {
        return {
            deferralState: false
        }
    },

    methods: {
        deferUpdates: function() { this.deferralState = true; },
        updatesDeferred: function() { let state = this.deferralState; this.deferralState = false; return state; }
    }
};
