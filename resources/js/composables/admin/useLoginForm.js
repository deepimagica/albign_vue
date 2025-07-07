import { ref } from "vue";
import { useForm, router } from "@inertiajs/vue3";
import { encryptData } from "@/utils/encryption";

export const useLoginForm = () => {
    const form = useForm({
        email: "",
        password: "",
    });

    const login = () => {
        const encryptedPayload = encryptData(form.data());
        form.transform(() => ({ data: encryptedPayload })).post("/admin/login", {
            preserveScroll: true,
            onSuccess: () => {
                router.visit("/admin/dashboard", { replace: true });
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
        login,
    };
};