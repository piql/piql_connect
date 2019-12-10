<template>
    <span>
        <a href="#" @click="showSettingsPage"><i class="fas fa-cogs signal" ></i></a>
        <div class="dropdownList" v-if="showSettingsAsDropdown">
            <i @click="showSettingsPage" class="fas fa-times closeNotifications clickable" ></i>
            <strong>Configuration settings</strong>
            <ul>
                <li><a href="/settings" class="dropdown-item" >{{$t('Settings')}}</a></li>
            </ul>
        </div>
    </span>
</template>

<script>
    export default {
        data() {
            return {
                showSettings: false
            }
        },
        props: {
            showSettingsAsDropdown: false
        },
        methods: {
            toggleShowSettings: function(){
                this.showSettings = !this.showSettings;
            },
            showSettingsPage: function() {
                console.log("clicked");
                let settingsLink = document.createElement('a');
                settingsLink.href = "/settings";
                settingsLink.click();
            }
        },
        mounted() {
        }
    }
</script>
