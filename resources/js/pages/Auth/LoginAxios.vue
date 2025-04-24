<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();
const email = ref('');
const password = ref('');
const errorMessage = ref('');

const login = async () => {
    errorMessage.value = '';
    try {
        const response = await axios.post('/api/login', {
            email: email.value,
            password: password.value
        });
        localStorage.setItem('token', response.data.token);
        axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
        router.push('/dashboard');
    } catch (error) {
        if (error.response?.status === 422) {
            errorMessage.value = error.response.data.errors;
        } else {
            errorMessage.value = { general: error.response?.data?.message || 'Login failed' };
        }
    }
};
</script>


<template>
    <div class="login-body">
        <div class="limiter">
            <div class="col-xl-6 col-lg-8 col-md-12 col-sm-12 col-xs-12 text-center mx-auto">
                <div class="col-md-12 mx-auto ">
                    <img src="/assets/img/logo.svg" class="logo mt-4 mb-1" width="50%">
                </div>
                <div class="container-login100">
                    <div class="subb-div col-xl-12  col-lg-12  col-md-8 col-xs-12 col-sm-12  mx-auto">
                        <form class="login100-form validate-form flex-sb flex-w" id="loginForm">
                            <div class="alert alert-danger alert-block w-100" v-if="errorMessage.general">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                {{ errorMessage.general }}
                            </div>
                            <div class="wrap-input100 validate-input m-b-12">
                                <input v-model="employee_code" class="input100" type="text"
                                    placeholder="Employee Code*" />
                                <span class="focus-input100"></span>
                            </div>
                            <span v-if="errorMessage.employee_code" class="text-danger pb-4">
                                {{ errorMessage.employee_code[0] }}
                            </span>
                            <div class="wrap-input100 validate-input m-b-12">
                                <span class="btn-show-pass">
                                    <i class="fa fa-eye"></i>
                                </span>
                                <input v-model="password" class="input100" type="password" placeholder="Password*" />
                                <span class="focus-input100"></span>
                            </div>
                            <span v-if="errorMessage.password" class="text-danger pb-4">
                                {{ errorMessage.password[0] }}
                            </span>
                            <div class="container-login100-form-btn mt-4">
                                <button type="button" @click="login" id="loginButton" class="login100-form-btn">
                                    Login
                                </button>
                            </div>
                        </form>
                        <div class="flex-sb-m w-full justify-content-center mt-12">
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
</style>