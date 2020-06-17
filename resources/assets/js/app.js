/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
import swal from 'sweetalert';

window.bus = new Vue({});
const app = new Vue({
    el: '#app',
    data() {
    	return {
    		'user_data': null,
    		'auto_logout_warning_timer': null,
    		'auto_logout_timer': null,
            spinner: null
    	};
    },
    components: {
        'main-component': require('./components/MainComponent.vue')
    },
    methods: {
    	logout() {
    		axios.post($('#app').data('logout-url'), {})
    		    .then(function (response) {
    		        var token = response.data.token;
    		        $('meta[name="csrf-token"]').attr('content', token);
    		        window.location.reload();
    		    })
    		    .catch(function (error) { 
    		    	console.log(error);
    		    	swal("Error occured: " + error + " Press OK to reload the page.")
    		    		.then(function () {
    		    			window.location.reload();
    		    		});
    		    });
    	},
    	initAutoLogout() {
    		var ctx = this;
    		if (ctx.auto_logout_warning_timer != null) clearTimeout(ctx.auto_logout_warning_timer);
    		if (ctx.auto_logout_timer != null) clearTimeout(ctx.auto_logout_timer);

    		ctx.auto_logout_warning_timer - setTimeout(function () {
    			swal("You session is about to timeout. Press OK to keep the session.")
    				.then(function () {
    					ctx.initAutoLogout();
    				});
    			ctx.auto_logout_timer = setTimeout(function () {
    				ctx.logout();
    			}, 300000);
    		}, 1500000)
    	}
    },
    created() {
        var ctx = this;
        ctx.spinner = new Spinner.Spinner({
                color: '#ffffff'
            })
        bus.$on('show-loading-hud', function () {
            $('body').append('<div class="overlay"></div>');
            ctx.spinner.spin(document.getElementsByTagName('body')[0]);
        });

        bus.$on('hide-loading-hud', function () {
            $('.overlay').remove();
            ctx.spinner.stop();
        });
    },
    mounted() {
    	var ctx = this;
        /**
         * Logout when reload
         * Commented for testing
         */
    	// $(window).bind('unload', function(e) {
    	// 	e.preventDefault();
    	// 	sessionStorage.clear();
    	// });

    	ctx.user_data = sessionStorage.getItem($('meta[name="user-email"]').attr('content'));
    	if (typeof ctx.user_data == 'undefined' || ctx.user_data == null) {
    		ctx.logout();
    	}

    	ctx.initAutoLogout();
    }
});