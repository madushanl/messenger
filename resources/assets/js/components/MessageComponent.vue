<template>
	<div class="message-bubble" :class="{ mine: (message.user_id == user.id), theirs: (message.user_id != user.id) }">
		<div class="usr-name" v-if="message.user.id != user.id">{{ sender_name }}</div>
		{{ body }}
		<div class="timestamp">{{ message.created_at }}</div>
	</div>
</template>

<script>
	export default {
		props: [ 'user', 'message', 'chat', 'symmetricKey' ],
		computed: {
			sender_name() {
				var vm = this;
				if (this.message.chat_id == this.chat.id)
					return collect(vm.chat.users)
						.first(user => user.id == vm.message.user_id).pivot.nickname;
				else return '';
			},
			body() {
				var message_body = this.message.body;
				return sjcl.decrypt(this.symmetricKey, message_body);
			}
		}
	}
</script>