<template>
    <span>
    <i @click="toggleShowNotifications()" title='Notifications' class="plistIcon fas fa-bell clickable" ></i>
    <div class="dropdownList" v-if="false">
        <i class="fas fa-times closeNotifications" @click="toggleShowNotifications()" ></i>
        <strong>notifications</strong>
        <p class="when">Today</p>
        <ul>
            <li>No new messages</li>
        </ul>
        <p class="when">Message log</p>
        <ul>
            <li></li>
        </ul>
    </div>
    </span>
</template>

<script>
import serviceCall from '../mixins/serviceCall';

export default {
        mixins: [ serviceCall ],
        data() {
            return {
                showNotifications: false
            }
        },
        methods: {
          toggleShowNotifications: function(){
                this.showNotifications = !this.showNotifications;
            },
            async listen() {
                // get user ID
                let userId = (await this.get("/api/v1/system/statuses/current-user")).data;

                // todo: remove this when toasts are implemented
                this.echo.private('User.' + userId + '.Events').listen('.Info', (event) => {


                    if( event.properties.type == "InformationPackageUploaded" )
                    {
                        this.infoToast(
                            this.$t('upload.toasts.ingestComplete.title'),
                            this.$t('upload.toasts.ingestComplete.message'),
                            {'BAGNAME': event.properties.name }
                        );
                    }


                    if( event.properties.bag ) {
                        console.log(
                            "%cInfo (bag: %s) : %s%c - %s",
                            "color: Blue", event.properties.bag.id, event.properties.type,
                            "color: black", ""
                        );
                    }
                });
                this.echo.private('User.' + userId + '.Events').listen('.Error', (event) => {

                    if( event.properties.type == "App\\Events\\ArchivematicaIngestError"
                        || event.properties.type == "App\\Events\\ArchivematicaTransferError")
                    {
                        this.errorToast(
                            this.$t('upload.toasts.ingestFailed.title'),
                            this.$t('upload.toasts.ingestFailed.message'),
                            {'BAGNAME': event.properties.bag.name }
                        );
                    }


                    if( event.properties.bag ) {
                        console.log(
                            "%cError (bag: %s) : %s%c - %s",
                            "color: Red", event.properties.bag.id, event.properties.type,
                            "color: black", event.properties.message
                        );
                    }
                });
            }
        },
        async mounted() {
            this.listen();
        },
        computed: {
            echo() { return window.Echo; }
        }
    }
</script>
