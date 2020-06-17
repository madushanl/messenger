<template>
    <div class="row align-items-center">
        <div class="chat-container" :class="{ 'collapsed': chat_messages_is_visible }">
            <chat-list-component :user="auth_user" :selected-chat.sync="selected_chat"></chat-list-component>
        </div>
         <div class="chat-messages" v-if="chat_messages_is_visible">
            <chat-messages-component :user="auth_user" :chat.sync="selected_chat"></chat-messages-component>
        </div>
    </div>
</template>

<script>
    export default {
        props: [ 'authUser' ],
        components: {
            'chat-list-component': require('./ChatListComponent.vue'),
            'chat-messages-component': require('./ChatMessagesComponent.vue')
        },
        data() {
            return {
                chat_container_is_visible: true,
                chat_messages_is_visible: true,
                selected_chat: null,
                auth_user: JSON.parse(this.authUser)
            }
        }
    }
</script>
