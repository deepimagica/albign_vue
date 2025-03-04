<script setup>
import { useLoginForm } from "@/composables/useLoginForm";
const { form, showPassword, togglePassword, login } = useLoginForm();
</script>

<template>
    <div class="login-body">
        <div class="limiter">
            <div class="col-xl-6 col-lg-8 col-md-12 col-sm-12 col-xs-12 text-center mx-auto top-height">
                <div class="col-md-12 mx-auto ">
                    <img src="/public/assets/img/logo.svg" class="logo mt-4 mb-1" width="50%">
                </div>
                <div class="container-login100">
                    <div class="subb-div col-xl-12  col-lg-12  col-md-8 col-xs-12 col-sm-12  mx-auto">
                        <form class="login100-form validate-form flex-sb flex-w" id="loginForm">
                            <div v-if="form.errors.message" class="alert alert-danger">
                                {{ form.errors.message }}
                            </div>
                            <div class="alert alert-danger alert-block w-100" id="login_error" style="display: none;">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                            </div>
                            <div class="wrap-input100 validate-input m-b-12">
                                <input v-model="form.employee_code" class="input100" type="text" @keyup.enter="login"
                                    placeholder="Employee Code*" />
                                <span class="focus-input100"></span>
                            </div>
                            <span v-if="form.errors.employee_code" class="text-danger pb-2">{{ form.errors.employee_code
                                }}</span>
                            <div class="wrap-input100 validate-input m-b-12">
                                <span class="btn-show-pass" @click="togglePassword">
                                    <i :class="showPassword ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
                                </span>
                                <input v-model="form.password" class="input100" @keyup.enter="login"
                                    :type="showPassword ? 'text' : 'password'" placeholder="Password*" />
                                <span class="focus-input100"></span>
                            </div>
                            <span v-if="form.errors.password" class="text-danger pb-2">{{ form.errors.password }}</span>
                            <div class="container-login100-form-btn mt-4">
                                <button type="button" @click="login" id="loginButton"
                                    class="login100-form-btn">Login</button>
                            </div>
                        </form>
                        <div class="flex-sb-m w-full  justify-content-center mt-12">
                            <a class="txt3" href="">Forgot Password</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
.text-danger {
    color: red;
    font-size: 14px;
}

.top-height {
    margin-top: 7%;
}

@media (orientation: portrait) {
    .top-height {
        margin-top: 35%;
    }
}
</style>