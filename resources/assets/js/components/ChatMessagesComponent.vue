<template>
	<div class="messages-component">
		<div class="action-bar-top">
			<div class="search-wrapper">
				<input type="text" class="form-control" placeholder="Search Messages">
				<i class="fa fa-search"></i>
			</div>
			<div class="action-buttons">
				<button class="btn"><i class="fa fa-ellipsis-v fa-2x"></i></button>
			</div>
		</div>
		<div class="message-container">
			<div class="col-12 text-center mb-2 mt-2" id="message-loader" v-if="busy"><i class="fa fa-spinner fa-pulse fa-fw fa-2x"></i></div>
			<message v-for="(message, index) in messages" :message="message" :chat="active_chat" :symmetric-key="symmetric_key" :key="index" :user="user"></message>
		</div>
		<form class="action-bar-bottom" @submit="sendMessage($event)">
			<div class="textarea-wrapper">
				<input class="form-control" v-model="newMessageText" />
			</div>
			<div class="action-buttons">
				<button class="btn">SEND</button>
			</div>
		</form>
	</div>
</template>

<script>
	export default {
		props: [ 'user', 'chat' ],
		components: {
			'message': require('./MessageComponent.vue')
		},
		data() {
			return {
				messages: [],
				nextPage: null,
				active_chat: this.chat,
				busy: false,
				perPage: 0,
				newMessageText: '',
				symmetric_key: null
			}
		},
		methods: {
			loadMessages(next_page) {
				var vm = this;
		        bus.$emit('show-loading-hud');
				axios.get((typeof next_page != 'undefined' && next_page) ? this.nextPage : vm.$routes.route('chat.messages', { 'id': vm.active_chat.id }))
					.then((response) => {
						if ((typeof next_page != 'undefined' && next_page)) {
							vm.messages = _.concat(_.reverse(response.data.messages.data), vm.messages);
						} else {
							vm.messages = _.reverse(response.data.messages.data);
						}
						vm.nextPage = response.data.messages.next_page_url;
						vm.perPage = response.data.messages.per_page;
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
							      text: "Retry",
							      value: true
							    }
							}
						})
						.then((value) => {
							vm.loadMessages();
						});
					})
					.then(() => {
						vm.busy = false;
						bus.$emit('hide-loading-hud');
					});
			},
			sendMessage(event) {
				event.preventDefault();
				var vm = this;
				var message = this.newMessageText;
				if (message.length == 0) return;
				message = sjcl.encrypt(vm.symmetric_key, message);
				axios.post(vm.$routes.route('chat.messages.save', { 'id': vm.active_chat.id }), {
					body: JSON.stringify(message)
				})
				.then((response) => {
					vm.newMessageText = '';
				})
				.catch((error) => {
					console.log(error);
					swal({
						title: 'Error Occured',
						text: 'Message not sent',
			  			closeOnClickOutside: false,
			  			closeOnEsc: false,
			  			dangerMode: true,
						button: 'OK'
					});
				});
			}
		},
		updated() {
			$('form input').focus();
		},
		watch: {
			chat(newVal, oldVal) {
				this.active_chat = newVal;
			},
			active_chat(newVal, oldVal) {
				var vm = this;

				if (newVal != null || (newVal!= null && oldVal != null && newVal.id != oldVal.id)) {
					this.messages = [];
					var user = collect(newVal.users).first(item => item.id == vm.user.id);
					if (user == null) return;
					var encrypted_sym_key = user.pivot.key;

					var serialized_sk = JSON.parse(sessionStorage.getItem(vm.user.email))['secret_key'];
			        var unserialized_sk = new sjcl.ecc.elGamal.secretKey(
			            sjcl.ecc.curves.c256,
			            sjcl.ecc.curves.c256.field.fromBits(sjcl.codec.base64.toBits(serialized_sk))
			        );
			        vm.symmetric_key = sjcl.decrypt(unserialized_sk, encrypted_sym_key);
			        vm.loadMessages();
				}
			},
			messages(newVal, oldVal) {
				var vm = this;

				var chatObj = vm.chat;
				chatObj.messages = vm.messages;
				vm.$emit('update:chat', chatObj);

				var options = {
					theme: 'inset',
					autoHideScrollbar: true,
					advanced:{ updateOnContentResize: true },
					callbacks: {
						onTotalScrollBack: function() {
					    	if (vm.nextPage != null) {
					    		vm.busy = true;
					    		vm.loadMessages(vm.nextPage);
					    	}
					    }
					}
				};
				
				this.$nextTick(() => {
					if (oldVal.length == 0 || (oldVal.length > 0 && oldVal[0].chat_id != vm.active_chat.id) ||  _.difference(newVal, oldVal).length == 0) {
						options['setTop'] = '-999999px';
					}
					if (vm.messages.length > vm.perPage && _.difference(newVal, oldVal).length > 0) {
						options['setTop'] = $('.message-bubble:nth-child(15)').offset().top - $('.message-container').height() + 'px';
					}
					$('.message-container').mCustomScrollbar('destroy');
					$('.message-container').mCustomScrollbar(options);
				})
			}
		}
	}
</script>