<script setup>
const platformName = import.meta.env.VITE_PLATFORM_NAME;
const currentYear = new Date().getFullYear();
import { useLoginForm } from "@/composables/admin/useLoginForm";
const { form, togglePassword, login } = useLoginForm();
</script>
<template>
    <div class="menu-toggler sidebar-toggler"></div>
    <div class="">
        <div class="col-md-4 col-xs-12">
            <div class="logo">
            </div>
            <div class="content">
                <form class="login-form" id="wrapped">
                    <img style="width: 200px;" class="img-responsive" src="/public/assets/img/logo.svg" />
                    <h3 class="form-title">Sign In</h3>
                    <h5 class="mb-5">Sign in to Continue</h5>
                    <div class="form-group">
                        <label class="control-label visible-ie8 visible-ie9">Email</label>
                        <div class="input-icon">
                            <i class="fa fa-user"></i>
                            <input v-model="form.email" value="" type="email" autocomplete="off"
                                placeholder="Email address" name="email" @keyup.enter="login"
                                class="form-control form-control-solid placeholder-no-fix" />
                        </div>
                    </div>
                    <span v-if="form.errors.email" class="text-danger pb-5">{{ form.errors.email }}</span>
                    <div class="form-group" style="padding-top: 10px;">
                        <label class="control-label visible-ie8 visible-ie9">Password</label>
                        <div class="input-icon">
                            <i class="fa fa-lock"></i>
                            <input v-model="form.password" value="" type="password" name="password" @keyup.enter="login"
                                class="form-control form-control-solid placeholder-no-fix" autocomplete="off"
                                placeholder="Password" />
                        </div>
                    </div>
                    <span v-if="form.errors.password" class="text-danger pt-5">{{ form.errors.password }}</span>
                    <div class="form-actions" style="padding-top: 10px;">
                        <button type="button" @click="login" class="btn green uppercase">Login</button>
                    </div>

                </form>
            </div>
            <div class="copyright">{{ currentYear }} &copy; {{ platformName }}.</div>
        </div>
    </div>
    <div class="col-md-8 col-xs-12">
        <img style="margin: 0 auto;width: 80%;" class="img-responsive" src="/public/assets/img/admin/login.svg">
    </div>
</template>

<style scoped>
.text-danger {
    color: red;
    font-size: 14px;
    margin-top: 20px;
    padding-top: 20px;
}
</style>