import { ref, onMounted, nextTick } from "vue";
import { router } from "@inertiajs/vue3";
import Swal from "sweetalert2";

export function useOTP(doc_id){
    const otpInputs = ref(["","","",""]);
    const otp =  ref("");
    const timerMinutes = ref(0);
    const timerSeconds = ref(30);
    const isResendDisabled = ref(true);
    let countdown;

    const updateOTP = () => {
        otp.value = otpInputs.value.join("");
    };

    const handleInput = (index, event) => {
        const value = event.target.value;
        if (value.length > 1) {
            otpInputs.value[index] = value[0];
        }
        updateOTP();

        if (value !== "" && index < otpInputs.value.length - 1) {
            nextTick(() => {
                document.querySelectorAll(".otpp")[index + 1].removeAttribute("disabled");
                document.querySelectorAll(".otpp")[index + 1].focus();
            });
        }
    };

    const handleBackspace = (index, event) => {
        if (event.key === "Backspace" && index > 0) {
            otpInputs.value[index] = "";
            nextTick(() => {
                document.querySelectorAll(".otpp")[index - 1].focus();
            });
        }
        updateOTP();
    };

    const validateForm = () => {
        if (otp.value.length !== 4) {
            Swal.fire({
                icon: "error",
                title: "Please enter a valid 4-digit OTP!",
                confirmButtonColor: "#202a44",
                confirmButtonText: "Ok",
                timer: 3000,
                timerProgressBar: true,
            });
            return false;
        }
        return true;
    };

    const submitOTP = () => {
        if (!validateForm()) return;
        router.post(`/user/verify-mobile/${doc_id}`, {
            otp: otp.value,
        }, {
            onSuccess: () => {
                Swal.fire({
                    icon: "success",
                    title: "OTP Verified Successfully!",
                    confirmButtonColor: "#202a44",
                    timer: 2000,
                });

                otpInputs.value = Array(otpInputs.value.length).fill('');
                otp.value = '';
            },
            onError: (errors) => {
                Swal.fire({
                    icon: "error",
                    title: "OTP Verification Failed!",
                    text: errors.otp || "Invalid OTP, please try again.",
                });
            }
        });
    };

    const resendOTP = () => {
        console.log("Resend OTP clicked!");
        router.post(`/resend-otp/${doc_id}`, {}, {
            onSuccess: () => {
                Swal.fire({
                    icon: "success",
                    title: "OTP Resent Successfully!",
                    confirmButtonColor: "#202a44",
                    timer: 2000,
                });

                otpInputs.value = Array(otpInputs.value.length).fill('');
                otp.value = '';
                startTimer();
            },
            onError: () => {
                Swal.fire({
                    icon: "error",
                    title: "Failed to resend OTP!",
                    text: "Please try again.",
                });

                otpInputs.value = Array(otpInputs.value.length).fill('');
                otp.value = '';
            }
        });
    };

    const startTimer = () => {
        isResendDisabled.value = true;
        timerMinutes.value = 0;
        timerSeconds.value = 30;

        countdown = setInterval(() => {
            if (timerSeconds.value > 0) {
                timerSeconds.value--;
            } else {
                clearInterval(countdown);
                isResendDisabled.value = false;
            }
        }, 1000);
    };

    onMounted(() => {
        document.querySelectorAll(".otpp")[0].focus();
        startTimer();
    });

    return {
        otpInputs,
        otp,
        timerMinutes,
        timerSeconds,
        isResendDisabled,
        handleInput,
        handleBackspace,
        validateForm,
        submitOTP,
        resendOTP
    };

}