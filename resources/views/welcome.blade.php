<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @auth
            <meta name="user-email" content="{{ auth()->user()->email }}">
        @endauth
        <title>{{ env('APP_NAME') }}</title>
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    </head>
    <body>
        @auth
            <div id="app" data-logout-url="{{ route('logout') }}">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <a class="navbar-brand" href="">{{ env('APP_NAME') }}</a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNavDropdown">
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item dropdown">
                                <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" >Logout</a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                        </ul>
                    </div>
                </nav>
                <div class="container-fluid">
                    <main-component :auth-user="'{{ json_encode(auth()->user()) }}'"></main-component>
                </div>
            </div>
            <script src="{{ asset('js/app.js') }}"></script>
        @endauth

        @guest
            <div id="app-guest" class="container">
                <div class="row align-items-center">
                    <div class="col-md-8 home-content-1">
                        <div class="heading">
                            What is {{ env('APP_NAME') }}?
                        </div>
                        <div class="sub-heading">
                            {{ env('APP_NAME') }} is a proof-of-concept(POC) made to demonstate end-to-end encrypted chat application.
                        </div>
                        <p>
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque at diam cursus, hendrerit ligula eget, ultricies magna. Donec et nisi vitae mi porta viverra. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Pellentesque porta lacus quam, vel commodo nunc iaculis ac. Aliquam fermentum placerat rutrum. Fusce iaculis odio quis turpis semper, vel semper tortor tempor. Curabitur porttitor justo mi, non feugiat enim sagittis a. Nulla tempus lobortis nulla, id aliquam purus dignissim non. Proin pellentesque nibh in est bibendum, ut posuere massa tincidunt. Mauris augue diam, consequat a purus eget, tincidunt placerat mi. Etiam efficitur aliquam nisl nec dapibus.
                        </p>
                    </div>
                    <div class="col-md-4 home-content-2">
                        <h1 class="heading">~ {{ env('APP_NAME') }} ~</h1>
                        <div class="auth-container">
                            <transition name="component-fade" mode="out-in">
                                <login-component v-if="login_is_visible"
                                :register-is-visible.sync="register_is_visible"
                                :reset-pass-is-visible.sync="reset_pass_is_visible"></login-component>
                                <register-component v-if="register_is_visible"
                                :login-is-visible.sync="login_is_visible"></register-component>
                                <reset-pass-component v-if="reset_pass_is_visible"
                                :login-is-visible.sync="login_is_visible"></reset-pass-component>
                                <unsupported-browser-component  v-if="browser_error_is_visible"></unsupported-browser-component>
                            </transition>
                        </div>
                    </div>
                </div>
            </div>
            <script src="{{ asset('js/guest.js') }}"></script>
        @endguest
    </body>
</html>
