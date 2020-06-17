/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
require('./modernizr-session-storage');

const app = new Vue({
    el: '#app-guest',
    data() {
    	return {
    		login_is_visible: true,
    		register_is_visible: false,
    		reset_pass_is_visible: false,
            browser_error_is_visible: false,
    	}
    },
    components: {
    	'login-component': require('./components/guest/LoginComponent.vue'),
    	'register-component': require('./components/guest/RegisterComponent.vue'),
        'reset-pass-component': require('./components/guest/ResetPassComponent.vue'),
    	'unsupported-browser-component': require('./components/guest/UnsupportedBrowserComponent.vue'),
    },
    watch: {
    	login_is_visible(newVal, oldVal) {
    		if (newVal) {
    			this.register_is_visible = this.reset_pass_is_visible = false;
    		}
    	},
    	register_is_visible(newVal, oldVal) {
    		if (newVal) {
    			this.login_is_visible = this.reset_pass_is_visible = false;
    		}
    	},
    	reset_pass_is_visible(newVal, oldVal) {
    		if (newVal) {
    			this.login_is_visible = this.register_is_visible = false;
    		}
    	},
        browser_error_is_visible(newVal, oldVal) {
            if (newVal) {
                this.login_is_visible = this.register_is_visible = this.reset_pass_is_visible = false;
            }
        }, 
    },
    mounted() {
        if (!Modernizr.sessionstorage) {
            this.browser_error_is_visible = false;
        }
    }
});