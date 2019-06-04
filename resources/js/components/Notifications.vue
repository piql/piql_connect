<template>
    <span>
    <i @click="toggleShowNotifications()" class="fas fa-bell clickable"  id="noti" data-count="4"></i>
    <div id="notificationsList" v-if="showNotifications">
        <i class="fas fa-times closeNotifications" @click="toggleShowNotifications()" ></i>
        <strong>notifications</strong>
        <p class="when">Today</p>
        <ul>
            <li>3 files ready for download</li>
        </ul>
        <p class="when">Older</p>
        <ul>
            <li>Something</li>
            <li>Something</li>
            <li>Something</li>
            <li>Something</li>
        </ul>
    </div>
    </span>
</template>

<script>
    export default {
        data() {
            return {
                showNotifications: false
            }
        },
        methods: {
            toggleShowNotifications: function(){
                this.showNotifications = !this.showNotifications;
            }
        },
        mounted() {
            console.log('Notifications component mounted.')
        }
    }
</script>
