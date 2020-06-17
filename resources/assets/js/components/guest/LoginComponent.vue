<template>
    <div class="row align-items-center">
        <div class="col-12">
            <div class="alert alert-danger mb-3" v-if="error != null">{{ error }}</div>
            <h3 class="text-center text-uppercase">Let's Chat, Hop in!</h3>
            <form @submit.prevent="submit" id="login-form">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="E-Mail Address" v-model="form_data.email"
                        data-parsley-required  
                        data-parsley-type="email">
                    <div class="error-block" v-if="validation_errors != null && typeof validation_errors.email != 'undefined'">{{ validation_errors.email[0] }}</div>
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" placeholder="Password" v-model="form_data.password"
                        data-parsley-required>
                    <div class="error-block" v-if="validation_errors != null && typeof validation_errors.password != 'undefined'">{{ validation_errors.password[0] }}</div>
                </div>
                <div class="form-group">
                    <button class="btn btn-block">Login <i class="fa fa-spinner fa-pulse fa-fw" v-if="busy"></i></button>
                </div>
                <div class="form-group">
                    <a href="#" @click.prevent="$emit('update:resetPassIsVisible', true)">Forgot Password?</a>
                </div>
            </form>
            <h5 class="text-center text-uppercase">Don't have an account?</h5>
            <a href="#" role="button" class="btn btn-link text-uppercase btn-block" @click.prevent="$emit('update:registerIsVisible', true)">Create an account</a>
        </div>
    </div>
</template>

<script>
    export default {
        props: [ 'registerIsVisible', 'resetPassIsVisible' ],
        data() {
            return {
                form_data: {
                    email: '',
                    password: ''
                },
                validation_errors: null,
                busy: false,
                error: null,
            }
        },
        methods: {
            submit() {
                var vm = this;
                if ($('#login-form').parsley().validate()) {
                    vm.busy = true;
                    vm.validation_errors = null;
                    vm.error = null;

                    var password = vm.form_data.password;
                    var passwordBitArray = sjcl.hash.sha256.hash(password);
                    var hashedPassword = sjcl.codec.hex.fromBits(passwordBitArray);

                    var request_data = Object.assign({}, vm.form_data);
                    request_data.password = hashedPassword;

                    axios.post(vm.$routes.route('login'), request_data)
                        .then((response) => {
                            var encrypted_user_data = response.data.user_data;
                            var user_data = sjcl.decrypt(password, JSON.stringify(encrypted_user_data));
                            sessionStorage.clear();
                            sessionStorage.setItem(vm.form_data.email, user_data);
                            window.location.reload();
                        })
                        .catch((error) => {
                            console.log(error);
                            if (error.response) {
                                if (error.response.status == 422) {
                                    vm.validation_errors = error.response.data.errors;
                                } else {
                                    vm.error = error.response.message;
                                }
                            } else {
                                vm.error = "Error occured while proccessing request."
                            }
                        })
                        .then(() => {
                            vm.busy = false;
                        });
                }
            }
        }
    }
</script>
