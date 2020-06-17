<template>
    <div class="row align-items-center">
        <div class="col-12">
            <h3 class="text-center text-uppercase">We got your back!</h3>
            <form @submit.prevent="submit" id="forgot-pass-form">
                <div class="form-group">
                    <input type="email" class="form-control" placeholder="E-Mail Address" v-model="form_data.email"
                        data-parsley-required
                        data-parsley-type="email">
                </div>
                <div class="form-group">
                    <button class="btn btn-block">Send me the link</button>
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
                    email: ''
                }
            }
        },
        methods: {
            submit() {
                if ($('form').parsley().validate()) {
                    console.log(this.form_data);
                }
            }
        }
    }
</script>
