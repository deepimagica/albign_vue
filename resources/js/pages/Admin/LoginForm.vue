<script setup>
const platformName = import.meta.env.VITE_PLATFORM_NAME;
const currentYear = new Date().getFullYear();
</script>
<template>
    <div class="menu-toggler sidebar-toggler"></div>
    <div class="">
        <div class="col-md-4 col-xs-12">
            <div class="logo">
            </div>
            <div class="content">
                <form action="" class="login-form" id="wrapped">
                    <img style="width: 200px;" class="img-responsive" src="/public/assets/img/logo.svg" />
                    <h3 class="form-title">Sign In</h3>
                    <h5 class="mb-5">Sign in to Continue</h5>
                    <div class="alert alert-danger display-hide">
                        <button class="close" data-close="alert"></button>
                    </div>
                    <div class="alert alert-success display-hide">
                        <button class="close" data-close="alert"></button>
                    </div>
                    <div class="form-group">
                        <label class="control-label visible-ie8 visible-ie9">Email</label>
                        <div class="input-icon">
                            <i class="fa fa-user"></i>
                            <input value="" type="text" autocomplete="off" placeholder="Email address" name="email"
                                class="form-control form-control-solid placeholder-no-fix" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label visible-ie8 visible-ie9">Password</label>
                        <div class="input-icon">
                            <i class="fa fa-lock"></i>
                            <input value="" type="password" name="password"
                                class="form-control form-control-solid placeholder-no-fix" autocomplete="off"
                                placeholder="Password" />
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn green uppercase">Login</button>
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