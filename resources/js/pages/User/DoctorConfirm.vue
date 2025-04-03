<script setup>
import TopNav from "../Layout/TopNav.vue";
import {  ref, defineProps } from 'vue';
import { usePage,router } from '@inertiajs/vue3';
import { encryptData,decryptData } from "@/utils/encryption";

const props = defineProps({
    data: Object,
    decodedDoctorId: String
});

const decryptedData = decryptData(props.data);
// console.log(ecryptedData?.doctor?.name,"deded");


// const response = decryptData(usePage().props.data);
// console.log(response,"res");


const form = ref({
    name: decryptedData?.doctor?.name || '',
    mobile: decryptedData?.doctor?.mobile || '',
    address: decryptedData?.doctor?.address || '',
    email: decryptedData?.doctor?.email || '',
    pin_code: decryptedData?.doctor?.pin_code || '',
    registration_no: decryptedData?.doctor?.registration_no || '',
    doctor_id: decryptedData?.doctor?.id || '',
    agree: ''
});

const errors = ref({});

const submitForm = (agreeStatus) => {
    form.value.agree = agreeStatus;

    const formData = new FormData();
    Object.entries(form.value).forEach(([key, value]) => {
        formData.append(key, value);
    });

    const payload = {
        data: Object.fromEntries(formData.entries()),
        doctor_id: props.decodedDoctorId 
    };

    const data = encryptData(payload);
    const url = `/user/confirmation/${form.value.doctor_id}`;
    console.log(data,"data");
    
    router.post(url, { data: data }, {
        onSuccess: () => {
            console.log("Form submitted successfully!");
        },
        onError: (error) => {
            errors.value = error;
        },
    });
};


</script>

<template>
    <TopNav />
    <main class="py-4">
        <div class="container full-height">
            <div class="row row-height">
                <div class="col-lg-12 content-right" id="start">
                    <div id="wizard_container">
                        <form @submit.prevent="submitForm('agree')" id="detail">
                            <input type="hidden" v-model="form.agree">
                            <input type="hidden" v-model="form.doctor_id">
                            <div id="middle-wizard">
                                <div class="step">
                                    <div class="nine">
                                        <h1>Participant's<span>Accounting Details</span></h1>
                                    </div>
                                    <div class="dash-div">
                                        <div class="main-box text-center custom-box">
                                            <h4 class="heading" style="margin-bottom:50px;"><b>I hereby confirm that the
                                                    below-mentioned details are correct.</b></h4>

                                            <div class="form-group form-md-line-input">
                                                <label class="col-md-12 control-label">
                                                    <i data-lucide="circle-fading-plus" class="icon_logo2"></i> Dr. Name
                                                    (Name required in Cheque):<span class="required"> * </span>
                                                </label>
                                                <div class="col-md-12">
                                                    <input type="text" v-model="form.name"
                                                        class="form-control input-color"
                                                        placeholder="Enter Doctor name">
                                                    <span class="text-danger pt-3 col-md-12 control-label"
                                                        v-if="errors.name">{{ errors.name
                                                        }}</span>
                                                </div>
                                            </div>

                                            <div class="form-group form-md-line-input">
                                                <label class="col-md-12 control-label">
                                                    <i data-lucide="smartphone" class="icon_logo2"></i> Mobile
                                                    Number<span class="required"> * </span>
                                                </label>
                                                <div class="col-md-12">
                                                    <input type="number" v-model="form.mobile"
                                                        class="form-control input-color"
                                                        placeholder="Enter Mobile number">
                                                    <span class="text-danger pt-3 col-md-12 control-label"
                                                        v-if="errors.mobile">{{ errors.mobile
                                                        }}</span>
                                                </div>
                                            </div>

                                            <div class="form-group form-md-line-input">
                                                <label class="col-md-12 control-label">
                                                    <i data-lucide="building" class="icon_logo2"></i> Address /
                                                    City<span class="required"> * </span>
                                                </label>
                                                <div class="col-md-12">
                                                    <input type="text" v-model="form.address"
                                                        class="form-control input-color" placeholder="Enter Address">
                                                    <span class="text-danger pt-3 col-md-12 control-label"
                                                        v-if="errors.address">{{ errors.address
                                                        }}</span>
                                                </div>
                                            </div>

                                            <div class="form-group form-md-line-input">
                                                <label class="col-md-12 control-label">
                                                    <i data-lucide="mails" class="icon_logo2"></i> Email<span
                                                        class="required"> * </span>
                                                </label>
                                                <div class="col-md-12">
                                                    <input type="text" v-model="form.email"
                                                        class="form-control input-color" placeholder="Enter Email">
                                                    <span class="text-danger pt-3 col-md-12 control-label"
                                                        v-if="errors.email">{{ errors.email
                                                        }}</span>
                                                </div>
                                            </div>

                                            <div class="form-group form-md-line-input">
                                                <label class="col-md-12 control-label">
                                                    <i data-lucide="map-pin-check" class="icon_logo2"></i> Pincode<span
                                                        class="required"> * </span>
                                                </label>
                                                <div class="col-md-12">
                                                    <input type="text" v-model="form.pin_code"
                                                        class="form-control input-color" placeholder="Enter Pincode">
                                                    <span class="text-danger pt-3 col-md-12 control-label"
                                                        v-if="errors.pin_code">{{ errors.pin_code
                                                        }}</span>
                                                </div>
                                            </div>

                                            <div class="form-group form-md-line-input">
                                                <label class="col-md-12 control-label">
                                                    <i data-lucide="heart-pulse" class="icon_logo2"></i> MCI
                                                    Registration No<span class="required">* </span>
                                                </label>
                                                <div class="col-md-12">
                                                    <input type="text" v-model="form.registration_no"
                                                        class="form-control input-color"
                                                        placeholder="Enter MCI Registration No">
                                                    <span class="text-danger pt-3 col-md-12 control-label"
                                                        v-if="errors.registration_no">{{
                                                            errors.registration_no }}</span>
                                                </div>
                                            </div>

                                            <div class="main-row">
                                                <div class="col-md-12">
                                                    <div class="row mx-auto justify-content-center">
                                                        <div class="col-md-4">
                                                            <button type="button" @click="submitForm('notagree')"
                                                                class="btn btn-primary btn-q-2 w-100 mt-2 btn-submit">I
                                                                do not agree</button>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <button type="submit"
                                                                class="btn btn-primary btn-q-1 w-100 mt-2 btn-submit">I
                                                                agree</button>
                                                        </div>
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
