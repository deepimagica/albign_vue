import { ref, onMounted, nextTick } from "vue";
import SignaturePad from "signature_pad";
import { useForm, router } from "@inertiajs/vue3";

export function useSignature(doctor_id) {
    const canvas = ref(null);
    let signaturePad = null;
    const errors = ref({});
    const doc_id = doctor_id || "";

    const form = useForm({
        signature: ""
    });

    onMounted(async () => {
        await nextTick();
        resizeCanvas();
        signaturePad = new SignaturePad(canvas.value);
    });

    const resizeCanvas = () => {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.value.width = canvas.value.offsetWidth * ratio;
        canvas.value.height = canvas.value.offsetHeight * ratio;
        canvas.value.getContext("2d").scale(ratio, ratio);
    };

    const clearSignature = () => {
        if (signaturePad) signaturePad.clear();
    };

    const submitSignature = () => {
        if (!signaturePad || signaturePad.isEmpty()) {
            errors.value.signature = "Doctor signature is required!";
            return;
        }

        form.signature = signaturePad.toDataURL("image/png");

        router.post(`/user/signature/${doc_id}`, form, {
            onSuccess: () => {
                errors.value = {};
            },
            onError: (err) => {
                errors.value = err;
            }
        });
    };

    return {
        canvas,
        errors,
        form,
        clearSignature,
        submitSignature
    };
}
