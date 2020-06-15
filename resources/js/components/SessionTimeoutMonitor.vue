<template>
    <div>
        <b-modal :visible="showCountDownModal" id="modal-timeout" size="xl" centered title="Session timeout" :ok-only="true" :no-refresh.boolean="true"
            @ok="stayLoggedIn" @hide="stayLoggedIn" :no-fade="true">
            <template v-slot:modal-title :no-refresh.boolean="true" class="m0">
                <div class="modalHeader">
                    {{$t("timeout.warning")}} {{visibleCountDownSeconds }} {{$t("timeout.seconds")}}.
                </div>
            </template>
            <div class="d-block text-center" :no-refresh.boolean="true">
                <p>{{$t("timeout.text1")}} {{sessionLifetimeMinutes}} {{$t("timeout.minutes")}}.</p>
                <p>{{$t("timeout.text2")}}</p>
            </div>
            <template v-slot:modal-ok > {{$t("timeout.continue")}} </template>
        </b-modal>
    </div>
</template>

<script>
import { BModal } from 'bootstrap-vue';
import serviceCall from '../mixins/serviceCall.js';
export default {
    mixins: [ serviceCall ],
    beforeDestroy() {
        clearInterval( this.checkForSessionTimeout );
    },
    components: {
        'b-modal': BModal,
    },
    data() {
        return {
            checkForSessionTimeout: null,
            countDownMs: Number.MAX_SAFE_INTEGER,
            lastActivityTime: 0,
            modalIsHidden: true,
        }
    },
    props: {
        intervalMs: {
            type: Number,
            default: 2000
        },
        sessionLifetimeMs: {
            type: Number,
            default: 7200000    /* 2 hours */
        },
        graceTimeMs: {
            type: Number,
            default: 5000       /* 5 seconds */
        },
        modalTimeMs: {
            type: Number,
            default: 60000      /* One minute */
        },
        noRefresh: {
            type: Boolean,
            default: true
        },
    },
    computed: {
        sessionLifetimeMinutes() {
            return Math.ceil( this.sessionLifetimeMs / 60000.0 );
        },
        countDownSeconds() {
            return Math.ceil( this.countDownMs / 1000.0 );
        },
        showCountDownModal() {
            return this.countDownMs < ( this.modalTimeMs + this.graceTimeMs ); /* Show modal if a minute plus grace remains */
        },
        visibleCountDownSeconds() {
            let time = ( this.countDownMs - this.graceTimeMs ) / 1000.0 ;
            return time > 0 ? time : 0;
        }
    },
    watch: {
        showCountDownModal( ) {
            if( this.showCountDownModal ) {
                this.setupVisibleCountDown();
            }
        },
        countDownMs( ) {
            if( this.countDownMs < this.graceTimeMs ) {
                this.logOutNow();
            }
        },
        sessionLifetimeMs( ) {
            if ( this.sessionLifetimeMs ) {
                this.setupCountDown();
            }
        }
    },
    methods: {
        setupCountDown() {
            this.countDownMs = this.sessionLifetimeMs;
            this.checkForSessionTimeout = setInterval( () => {
                let lastActivityTime = sessionStorage.getItem('lastActivityTime');
                if( lastActivityTime != null && lastActivityTime != this.lastActivityTime ){
                    this.lastActivityTime = lastActivityTime;
                    this.countDownMs = this.sessionLifetimeMs;
                } else {
                    this.countDownMs -= this.intervalMs;
                }
            }, this.intervalMs );
        },
        setupVisibleCountDown() {
            clearInterval( this.checkForSessionTimeout );
            this.checkForSessionTimeout = setInterval( async () => {
                await (this.get("/api/v1/system/currentUser")).data;
                this.countDownMs -= 1000;
            }, 1000 );
        },
        async stayLoggedIn() {
            clearInterval( this.checkForSessionTimeout );
            location.reload(false);
        },
        logOutNow() {
            clearInterval( this.checkForSessionTimeout );
            Vue.nextTick( () => { window.location = "/logout"; } );
        },

    },
}
</script>
