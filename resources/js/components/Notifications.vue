<template>
    <span>
    <i @click="toggleShowNotifications()" class="fas fa-bell clickable" ></i>
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
    export default {
        data() {
            return {
                showNotifications: false
            }
        },
        methods: {
          toggleShowNotifications: function(){
              return;
                this.showNotifications = !this.showNotifications;
            }
        },
        mounted() {
        }
    }
</script>
