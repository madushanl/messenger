<template>
    <div class="row align-items-center">
        <div class="col-12">
            <div class="alert alert-danger mb-3" v-if="error != null">{{ error }}</div>
            <h3 class="text-center text-uppercase">Welcome, Join with us!</h3>
            <form @submit.prevent="submit" id="register-form">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Name" v-model="form_data.name"
                        data-parsley-required>
                    <div class="error-block" v-if="validation_errors != null && typeof validation_errors.name != 'undefined'">{{ validation_errors.name[0] }}</div>
                </div>
                <div class="form-group">
                    <input type="email" class="form-control" placeholder="E-Mail Address" v-model="form_data.email"
                        data-parsley-required
                        data-parsley-type="email">
                    <div class="error-block" v-if="validation_errors != null && typeof validation_errors.email != 'undefined'">{{ validation_errors.email[0] }}</div>
                </div>
                <div class="form-group">
                    <input type="password" id="password" class="form-control" placeholder="Password" v-model="form_data.password"
                        data-parsley-required 
                        data-parsley-minlength="8"
                        data-parsley-uppercase="1"
                        data-parsley-lowercase="1"
                        data-parsley-number="1"
                        data-parsley-special="1">
                    <div class="error-block" v-if="validation_errors != null && typeof validation_errors.password != 'undefined'">{{ validation_errors.password[0] }}</div>
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" placeholder="Re-Type Password"
                        data-parsley-required
                        data-parsley-equalto="#password"
                        data-parsley-equalto-message="Value does not match with Password">
                </div>
                <div class="form-group">
                    <button :disabled="busy" class="btn btn-block">
                        Register <i class="fa fa-spinner fa-pulse fa-fw" v-if="busy"></i>
                    </button>
                </div>
            </form>
            <a href="#" role="button" class="btn btn-link text-uppercase btn-block" @click.prevent="$emit('update:loginIsVisible', true)">
                <i class="fa fa-arrow-left" aria-hidden="true"></i> Back to login
            </a>
        </div>
    </div>
</template>

<script>
    export default {
        props: [ 'loginIsVisible' ],
        data() {
            return {
                form_data: {
                    email: '',
                    name: '',
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
                if ($('#register-form').parsley().validate()) {
                    vm.busy = true;
                    vm.validation_errors = null;
                    vm.error = null;

                    var keyPair = sjcl.ecc.elGamal.generateKeys(256);
                    var publicKey = keyPair.pub.get();
                    var secretKey = keyPair.sec.get();
                    var publicKeySerialized = sjcl.codec.base64.fromBits(publicKey.x.concat(publicKey.y));
                    var secretKeySerialized = sjcl.codec.base64.fromBits(secretKey);

                    var password = vm.form_data.password;
                    var passwordBitArray = sjcl.hash.sha256.hash(password);
                    var hashedPassword = sjcl.codec.hex.fromBits(passwordBitArray);

                    var request_data = Object.assign({}, vm.form_data);
                    request_data.password = hashedPassword;
                    request_data['public_key'] = publicKeySerialized;

                    var user_data = JSON.stringify({
                        email: request_data.email,
                        public_key: publicKeySerialized,
                        secret_key: secretKeySerialized
                    });
                    request_data['data'] = sjcl.encrypt(password, user_data);

                    axios.post(vm.$routes.route('users.store'), request_data)
                        .then((response) => {
                            sessionStorage.clear();
                            sessionStorage.setItem(request_data.email, user_data);
                            window.location.reload();
                        })
                        .catch((error) => {
                            console.log(error)
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
