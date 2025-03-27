<script setup>
import TopNav from "../Layout/TopNav.vue";
import { useOTP } from "@/composables/useOTP";

const props = defineProps({ doc_id: String });
const {
    otpInputs,
    otp,
    timerMinutes,
    timerSeconds,
    isResendDisabled,
    handleInput,
    handleBackspace,
    submitOTP,
    resendOTP
} = useOTP(props.doc_id);

</script>
<template>
    <TopNav />
    <main class="py-4">
        <div class="container full-height">
            <div class="row row-height">
                <div class="col-lg-12 content-right" id="start">
                    <div id="wizard_container">
                        <form @submit.prevent="validateForm" class="form-otp">
                            <input type="hidden" name="doctor_id" :value="docId">
                            <div id="middle-wizard">
                                <div class="nine">
                                    <h1>Agreement<span>Signature Validation</span></h1>
                                </div>
                                <div class="step dash-div">
                                    <div class="main-box text-center custom-box">
                                        <div class="otp-icon-div">
                                            <img src="" class="icon_logo" alt="">
                                        </div>
                                        <div class="form-group form-md-line-input">
                                            <h5 class="col-md-12 mt-3 mb-3 text-center">Enter OTP<span class="required">
                                                    *</span></h5>
                                            <div class="col-md-12 p-0">
                                                <div class="input-field">
                                                    <input v-for="(digit, index) in otpInputs" :key="index"
                                                        type="number" name="otpp[]" v-model="otpInputs[index]"
                                                        class="form-control otpp"
                                                        :disabled="index !== 0 && otpInputs[index - 1] === ''"
                                                        @input="handleInput(index, $event)"
                                                        @keyup.backspace="handleBackspace(index, $event)" />
                                                </div>
                                                <input type="hidden" name="otp" v-model="otp">
                                            </div>
                                            <p class="mmcounter mt-2">
                                                <span class='e-m-minutes'>{{ timerMinutes.toString().padStart(2, '0')
                                                }}</span> :
                                                <span class='e-m-seconds'>{{ timerSeconds.toString().padStart(2, '0')
                                                }}</span>
                                            </p>
                                        </div>
                                        <div class="main-row">
                                            <div class="col-md-12">
                                                <div class="row mx-auto justify-content-center">
                                                    <div class="col-md-4">
                                                        <button type="button" id="btn_resend_otp"
                                                            class="btn btn-primary w-100 mt-2"
                                                            :disabled="isResendDisabled" @click="resendOTP">Resend
                                                            OTP</button>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <button type="submit" name="submit"
                                                            class="btn btn-primary w-100 mt-2"
                                                            @click="submitOTP">SUBMIT</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>
<style scoped>
.form-control {
    margin-right: 5px;
}
</style>