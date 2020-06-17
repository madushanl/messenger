<template>
	<b-modal id="new-chat-modal" title="New conversation with..." no-fade @hide="clearData" @shown="$refs.searchInput.focus()">
	    <div class="container-fluid">
	    	<div class="row" v-if="error != null">
	    		<div class="col-12 alert alert-danger">Error occured while proccessing request</div>
	    	</div>
	    	<div class="row">
	    		<div class="search-wrapper">
	    			<input class="form-control" ref="searchInput" placeholder="Search users by name/email" v-model="searchQuery">
	    			<i class="fa fa-search"></i>
	    		</div>
	    		<div class="search-result pb-3 pl-3 pr-3 pt-2">
	    			<div  v-if="users.length == 0" class="row align-items-center justify-content-center">
		    			<span class="align-self-center" v-if="!busy">No results</span>
		    			<i class="fa fa-spinner fa-pulse fa-fw align-self-center" v-if="busy"></i>
	    			</div>
	    			<div class="col-12 mt-1 usr-row" v-for="user in users" @click="$emit('createConversationWithUser', user)">
	    				<div class="row">
	    					<div class="col-11">
	    						<div class="w-100">{{ user.name }}</div>
	    						<small>{{ user.email }}</small>
	    					</div>
	    					<div class="col-1 text-center align-self-center">
	    						<i class="fa fa-chevron-circle-right"></i>
	    					</div>
	    				</div>
	    			</div>
	    		</div>
	    	</div>
	    </div>
	</b-modal>
</template>

<script>
	export default {
		data() {
			return {
				users: [],
				searchQuery: '',
				error: null,
				busy: false,
			}
		},
		methods: {
			getUsers() {
				var vm = this;
				vm.error = null;
				vm.busy = true;

				axios.get(this.$routes.route('user.search'), {
					params: {
						q: vm.searchQuery
					}
				})
				.then((response) => {
					vm.users = response.data.users;
				})
				.catch((error) => {
					console.log(error);
					vm.error = error;
				})
				.then(() => {
					vm.busy = false;
				});
			},
			clearData() {
				this.users = [];
				this.searchQuery = '';
				this.error = null;
			}
		},
		mounted() {
			this.clearData();
		},
		watch: {
			searchQuery(newVal, oldVal) {
				if (newVal.length > 2) this.getUsers();
				if (newVal == 0) this.clearData();
			}
		}
	}
</script>