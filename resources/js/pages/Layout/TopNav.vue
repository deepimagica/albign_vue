<script setup>
import { router } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
const auth = usePage().props.auth;
const logout = () => {
    router.post('/logout', {}, {
        replace: true, 
        preserveState: false,
        onSuccess: () => {
            localStorage.removeItem('token');
        }
    });
};
</script>
<template>
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <a class="navbar-brand" href="">
            <img src="/public/assets/img/logo.svg" style="width: 100px;margin: 0;">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto">
                <li class=" nav-item dropdown">
                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ auth.user?.name ?? 'N/A' }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item text-right" href="">Change Password</a>
                        <a v-if="auth.user?.employee_pos == 2 || auth.user?.employee_pos == 3"
                            class="dropdown-item text-right" href="">Change Signature</a>
                        <a class="dropdown-item text-right" href="">Download Excel Report</a>
                        <a class="dropdown-item text-right" href="#"><button type="button" @click.prevent="logout">Logout</button></a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
    <div class="loader" style="display: none;">
        <p id="progress-text">Please hold on! The doctor's details and documents are being submitted on the server.</p>
        <div id="progress-div">
            <div id="progress-bar"></div>
        </div>
    </div>
</template>