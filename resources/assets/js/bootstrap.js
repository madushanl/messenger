window._ = require('lodash');
window.Popper = require('popper.js').default;
window.sjcl = require('./sjcl');
window.collect = require('collect.js');
window.Spinner = require('spin.js');
window.moment = require('moment');

window.Vue = require('vue');
import VueLaroute from 'vue-laroute';
import routes from './laroute.js';
import BootstrapVue from 'bootstrap-vue'


Vue.use(VueLaroute, {
  routes
});
Vue.use(BootstrapVue);

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
	require('parsleyjs');
	require('twemoji');
	require("jquery-mousewheel");
	require('malihu-custom-scrollbar-plugin');
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

window.Parsley.addValidator('uppercase', {
  requirementType: 'number',
  validateString: function(value, requirement) {
    var uppercases = value.match(/[A-Z]/g) || [];
    return uppercases.length >= requirement;
  },
  messages: {
    en: 'Your password must contain at least (%s) uppercase letter.'
  }
});

//has lowercase
window.Parsley.addValidator('lowercase', {
	requirementType: 'number',
	validateString: function(value, requirement) {
		var lowecases = value.match(/[a-z]/g) || [];
		return lowecases.length >= requirement;
	},
	messages: {
		en: 'Your password must contain at least (%s) lowercase letter.'
	}
});

//has number
window.Parsley.addValidator('number', {
	requirementType: 'number',
	validateString: function(value, requirement) {
		var numbers = value.match(/[0-9]/g) || [];
		return numbers.length >= requirement;
	},
	messages: {
		en: 'Your password must contain at least (%s) number.'
	}
});

//has special char
window.Parsley.addValidator('special', {
	requirementType: 'number',
	validateString: function(value, requirement) {
		var specials = value.match(/[^a-zA-Z0-9]/g) || [];
		return specials.length >= requirement;
	},
	messages: {
		en: 'Your password must contain at least (%s) special characters.'
	}
});

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo'

window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    encrypted: true
});