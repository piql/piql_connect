<template>
    <span>
    <i @click="toggleShowSettings()" class="fas fa-cogs clickable" ></i>
    <div class="dropdownList" v-if="showSettings">
        <i class="fas fa-times closeNotifications" @click="toggleShowSettings()" ></i>
        <strong>Configuration settings</strong>
        <ul>
          <li><a href="/settings" class="dropdown-item" >Language settings</a></li>
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
        methods: {
            toggleShowSettings: function(){
                this.showSettings = !this.showSettings;
            }
        },
        mounted() {
            console.log('Notifications component mounted.')
        }
    }
</script>
