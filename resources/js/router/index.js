import { createRouter, createWebHistory } from 'vue-router';
import Login from '../components/Auth/LoginForm.vue';

const routes = [
    { path: '/', component: Login },
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach((to, from, next) => {
    const isAuthenticated = localStorage.getItem('token');
    if (to.meta.requiresAuth && !isAuthenticated) {
        next('/');
    } else {
        next();
    }
});

export default router;