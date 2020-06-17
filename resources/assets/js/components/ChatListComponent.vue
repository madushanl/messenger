<template>
	<div class="chat-list-wrapper">
		<div class="action-bar">
			<div class="search-wrapper">
				<input class="form-control" placeholder="Search conversations">
				<i class="fa fa-search"></i>
			</div>
			<div class="action-buttons">
				<button class="btn" v-b-modal.new-chat-modal><i class="fa fa-edit"></i></button>
			</div>
		</div>
		<div class="chat-container-inner">
			<div v-for="(chat, key) in chats" class="chat-list-item" :class="{ selected: (selected_chat.id == chat.id), 'mt-1' : (key != 0) }" @click="selectChat(chat)">
				<span class="usr-names">{{ chatUsers(chat) }}</span>
				<!-- <span class="online-status">Online</span> -->
				<a href="#" role="button" class="btn-actions"><span class="fa fa-cog"></span></a>
				<span class="last-msg mt-2" v-html="lastMessage(chat)"></span>
			</div>
		</div>
        <new-chat-modal @createConversationWithUser="getOrCreateConversation"></new-chat-modal>
	</div>
</template>

<script>
	export default {
		props: [ 'user', 'selectedChat' ],
		components: {
			'new-chat-modal': require('./NewChatModalComponent.vue')
		},
		data() {
			return {
				chats: [],
				nextPage: null,
				selected_chat: null,
				listning_channels: null
			}
		},
		methods: {
			loadChats(next_page) {
				var vm = this;
				bus.$emit('show-loading-hud');
				axios.get((typeof next_page != 'undefined' && next_page) ? this.nextPage : this.$routes.route('user.chat_list'))
					.then((response) => {
						if ((typeof next_page != 'undefined' && next_page)) {
							vm.chats = _.concat(vm.chats, response.data.chats.data);
						} else {
							vm.chats = response.data.chats.data;
							if (vm.chats.length > 0) {
								vm.selected_chat = _.head(vm.chats);
							}
						}
						vm.nextPage = response.data.chats.next_page_url;
					})
					.catch((error) => {
						console.log(error);
						swal({
							title: 'Error Occured',
							text: 'Error Occured while processing request',
				  			closeOnClickOutside: false,
				  			closeOnEsc: false,
			  				dangerMode: true,
							buttons: {
								confirm: {
							      text: "Reload",
							      value: true
							    }
							}
						})
						.then((value) => {
							window.location.reload();
						});
					})
					.then(() => {
						bus.$emit('hide-loading-hud');
					});
			},
			chatUsers(chat) {
				return _.join(collect(chat.users).where('id', '!=', this.user.id).pluck('pivot.nickname').all(), ', ');
			},
			lastMessage(chat) {
				var vm = this;
				var encrypted_sym_key = collect(chat.users).first(item => item.id == vm.user.id).pivot.key;

				var serialized_sk = JSON.parse(sessionStorage.getItem(vm.user.email))['secret_key'];
		        var unserialized_sk = new sjcl.ecc.elGamal.secretKey(
		            sjcl.ecc.curves.c256,
		            sjcl.ecc.curves.c256.field.fromBits(sjcl.codec.base64.toBits(serialized_sk))
		        );
		        vm.symmetric_key = sjcl.decrypt(unserialized_sk, encrypted_sym_key);

				var message = collect(chat.messages).last();
				if (message != null && typeof message != 'undefined') {
					var message_body = message.body;
					var message_body = sjcl.decrypt(vm.symmetric_key, message_body);
					return '<b>' + message.user.name + ':</b> ' + ((message_body.length > 50) ? message_body.substring(0, 75) + '...' : message_body);
				}
				else return '<i>no messages yet</i>';
			},
			selectChat(chat) {
				this.selected_chat = chat;
			},
			initUserSearch() {
				var vm = this;
			},
			getOrCreateConversation(user) {
				var vm = this;
				this.$root.$emit('bv::hide::modal', 'new-chat-modal');
				if (user.id == vm.user.id) return;
				bus.$emit('show-loading-hud');
				axios.post(vm.$routes.route('chat.get_by_users'), { 
						'user_ids': [ user.id ] 
					})
					.then((response) => {
						if (response.data.chat != null) {
							var chat = response.data.chat;
							var index_in_current_list = _.findIndex(vm.chats, item => item.id == chat.id);
							if (index_in_current_list >= 0) {
								vm.chats = _.concat(_.pullAt(vm.chats, [ index_in_current_list ]), vm.chats);
							} else {
								vm.chats = _.concat([ chat ], vm.chats);
							}
							vm.selectChat(chat);
						} else {
							vm.createChatWithUser(user);
						}
					})
					.catch((error) => {
						console.log(error);
						if (error.response.status == 422) {
							swal('Error', error.response.data.errors['user_ids'][0], 'error');
						} else {
							swal('Sorry', 'Error occured while proccessing request', 'error');
						}
					})
					.then(() => {
						bus.$emit('hide-loading-hud');
					});
			},
			createChatWithUser(user) {
				var vm = this;
				if (user.id == vm.user.id) return;

				var my_public_key = new sjcl.ecc.elGamal.publicKey(
	                sjcl.ecc.curves.c256,
	                sjcl.codec.base64.toBits(vm.user.public_key)
	            );
	            var other_public_key = new sjcl.ecc.elGamal.publicKey(
	                sjcl.ecc.curves.c256,
	                sjcl.codec.base64.toBits(user.public_key)
	            );

	            var secret_sym_key = sjcl.codec.base64.fromBits(sjcl.random.randomWords(8, 10));
                var sk_sym_1 = sjcl.encrypt(my_public_key, secret_sym_key);
                var sk_sym_2 = sjcl.encrypt(other_public_key, secret_sym_key);

				bus.$emit('show-loading-hud');
				axios.post(vm.$routes.route('chat.create'), { 
						'sym_key': sk_sym_1,
						'users': [
							{ 'id': user.id, 'sym_key': sk_sym_2 }
						]
					})
					.then((response) => {
						if (response.data.chat != null) {
							var chat = response.data.chat;
							vm.chats = _.concat([ chat ], vm.chats);
							vm.selectChat(chat);
						}
					})
					.catch((error) => {
						console.log(error);
						swal('Sorry', 'Error occured while proccessing request', 'error');
					})
					.then(() => {
						bus.$emit('hide-loading-hud');
					});
			},
			addChatToList(chat_id) {
				var vm = this;
				axios.get(vm.$routes.route('chat.by_id', { 'id': chat_id }))
					.then((response) => {
						var chat = response.data.chat;
						vm.chats.push(chat);
					})
					.catch((error) => {
						console.log(error)
					});
			}
		},
		mounted() {
			var vm = this;
			this.loadChats();
			this.initUserSearch();

			Echo.channel('user-notifications.' + vm.user.id)
			    .listen('.new-chat-made', (e) => {
			        vm.addChatToList(e.chat_id);
			    });
		},
		watch: {
			selected_chat(newVal, oldVal) {
				this.$emit('update:selectedChat', newVal);
			},
			chats(newVal, oldVal) {
				var vm = this;
				var available_channels = collect(newVal).pluck('channel_identifier', 'id');
				if (vm.listning_channels != null) {
					vm.listning_channels.filter((val, key) => { 
						return (typeof available_channels[key] != 'undefined' && available_channels[key] != null);
					}).each((channel) => {
						Echo.leave(channel);
					});
				}

				available_channels.each((channel, chat_id) => {
					Echo.channel(channel)
					    .listen('.new-message', (e) => {
					        var chat = collect(vm.chats).first((item) => { return item.id == chat_id; });
					        if (typeof chat != 'undefined' && chat != null) {
					        	chat.messages.push(e);
					        	if (chat.id != vm.selected_chat.id) {
					        		vm.chats = _.concat([ vm.selected_chat, chat ], collect(vm.chats).filter((item) => {
					        			return (item.id != vm.selected_chat.id && item.id != chat.id);
					        		}).all());
					        	}
					        }
					    });
				});
				vm.listning_channels = available_channels;

				this.$nextTick(() => {
					$('.chat-container-inner').mCustomScrollbar('destroy');
					$('.chat-container-inner').mCustomScrollbar({
						theme: 'inset',
						advanced:{ updateOnContentResize: true }
					});
				})
			}
		}
	}
</script>