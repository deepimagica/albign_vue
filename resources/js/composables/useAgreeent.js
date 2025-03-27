import { computed, ref } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { encryptData } from "@/utils/encryption";

export const useAgreement = () => {
    const props = usePage().props;
    const response = computed(() => props.response || {});
    const decodedDoctorId = computed(() => response.value?.doc_enc_id || "");
    const csrfToken = computed(() => response.value.csrf?.name || "");
    const errors = ref({});
    const isAgree = ref(!!response.value?.doctor?.is_agreement_verified);

    const formattedAgreementDate = computed(() => {
        const dateString = response.value?.survey?.agreement_date;
        if (!dateString) return "N/A";

        return new Date(dateString).toLocaleDateString("en-IN", {
            day: "2-digit",
            month: "long",
            year: "numeric",
        });
    });

    const submitForm = (event) => {
        event.preventDefault();
        errors.value = {};
        const formData = Object.fromEntries(new FormData(event.target).entries());
        console.log(formData,"forn");
        
        const encryptedDoctorId = encryptData({ doctor_id: formData.doctor_id });
        const base64EncryptedDoctorId = btoa(encryptedDoctorId);
        const encodedEncryptedDoctorId = encodeURIComponent(base64EncryptedDoctorId);
        const encryptedPayload = encryptData(formData);
        router.post(`/user/agreement/${decodedDoctorId.value}`, { data: encryptedPayload }, {
            onError: (err) => errors.value = err
        });
    };

    return {
        response,
        decodedDoctorId,
        csrfToken,
        errors,
        isAgree,
        formattedAgreementDate,
        submitForm
    };
};