<template>
    <span>
    <i @click="toggleShowSettings()" class="fas fa-cogs clickable" ></i>
    <div class="dropdownList" v-if="showSettings">
        <i class="fas fa-times closeNotifications" @click="toggleShowSettings()" ></i>
        <strong>Configuration settings</strong>
        <ul>
          <li>Language settings</li>
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
