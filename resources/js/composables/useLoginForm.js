import { ref } from "vue";
import { useForm, router } from "@inertiajs/vue3";
import { encryptData } from "@/utils/encryption";

export const useLoginForm = () => {
    const form = useForm({
        employee_code: "",
        password: "",
    });

    const showPassword = ref(false);
    const togglePassword = () => {
        showPassword.value = !showPassword.value;
    };

    const login = () => {
        const encryptedPayload = encryptData(form.data());
        form.transform(() => ({ data: encryptedPayload })).post("/login", {
            preserveScroll: true,
            onSuccess: () => {
                router.visit("/dashboard", { replace: true });
            },
            onError: (errors) => {
                console.log("Validation Errors:", errors);
                clearErrors();
            },
        });
    };

    const clearErrors = () => {
        setTimeout(() => {
            form.errors = {};
        }, 3000);
    };

    return {
        form,
        showPassword,
        togglePassword,
        login,
    };
};