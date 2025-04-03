<script setup>
import { ref, onMounted, nextTick, watch } from "vue";
import { router } from '@inertiajs/vue3';
import { encryptData } from "@/utils/encryption";
import Cropper from "cropperjs";
import "cropperjs/dist/cropper.css";
import TopNav from '../Layout/TopNav.vue';

const props = defineProps({
    data: Object,
    decodedDoctorId: String,
});
// console.log(props.decodedDoctorId,"props");

const form = ref({
    doctor_id: props.decodedDoctorId || "",
    pan_number: props.data?.doctor?.pan_number || "",
    account_number: props.data?.doctor?.account_number || "",
    IFSC_code: props.data?.doctor?.IFSC_code || "",
    cancel_cheque: null,
    cancel_cheque_crop: "",
    is_edit: false,
    // errors: {},
});
const errors = ref({});

// console.log(form, "form");

const isLoading = ref(false);
const isImageSelected = ref(false);
const cropper = ref(null);
const imageData = ref(null);
const croppedImage = ref(null);
const croppedImageData = ref(null);
const imagePreviewContainer = ref(null);
const showCropper = ref(false);
const showSuccessMessage = ref(false);

const handleFileUpload = async (event) => {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();

    reader.onload = async (e) => {
        imageData.value = e.target.result;
        isImageSelected.value = true;
        showCropper.value = true;
        showSuccessMessage.value = false;

        await nextTick();

        if (!imagePreviewContainer.value) {
            console.error("Image preview container is NOT READY!");
            return;
        }

        imagePreviewContainer.value.src = imageData.value;

        if (cropper.value) {
            cropper.value.destroy();
        }

        cropper.value = new Cropper(imagePreviewContainer.value, {
            aspectRatio: 16 / 9,
            viewMode: 1,
            dragMode: "move",
            zoomable: true,
            rotatable: true,
            crop() {
                updateCroppedPreview();
            },
        });

        updateCroppedPreview();
    };

    reader.readAsDataURL(file);
};

const updateCroppedPreview = () => {
    if (cropper.value) {
        const croppedCanvas = cropper.value.getCroppedCanvas();
        if (croppedCanvas) {
            croppedImage.value = croppedCanvas.toDataURL("image/png");
            croppedImageData.value = croppedImage.value;
        }
    }
};

const rotateImage = () => {
    if (cropper.value) {
        cropper.value.rotate(90);
        updateCroppedPreview();
    }
};

const zoomIn = () => {
    if (cropper.value) {
        cropper.value.zoom(0.1);
        updateCroppedPreview();
    }
};

const zoomOut = () => {
    if (cropper.value) {
        cropper.value.zoom(-0.1);
        updateCroppedPreview();
    }
};

const cropImage = () => {
    if (cropper.value) {
        const croppedCanvas = cropper.value.getCroppedCanvas();

        if (croppedCanvas) {
            croppedImage.value = croppedCanvas.toDataURL("image/png");
            croppedImageData.value = croppedImage.value;
            showCropper.value = false;
            showSuccessMessage.value = true;
        } else {
            console.error("Cropped canvas is NULL!");
        }
    } else {
        console.error("Cropper instance not found!");
    }
};

const submitForm = () => {
    // isLoading.value = true;
    form.value.cancel_cheque = croppedImageData.value;
    // console.log("Raw Form Data:", form.value);
    const encryptedPayload = encryptData(form.value);
    router.post(
        `/user/account-details/${form.value.doctor_id}`,
        { data: encryptedPayload },
        {
            onError: (error) => {
                errors.value = error;
            },
        }
    )
};
</script>

<template>
    <TopNav />
    <div class="loader-div" v-if="isLoading">
        <div class="loading"></div>
    </div>
    <main class="py-4">
        <div class="container full-height">
            <div class="row row-height">
                <div class="col-lg-12 content-right" id="start">
                    <div id="wizard_container">
                        <form @submit.prevent="submitForm" id="doctorsInfo">
                            <div id="middle-wizard">
                                <div class="step">
                                    <div class="nine">
                                        <h1>Participant's<span>Accounting Details</span></h1>
                                    </div>
                                    <div class="dash-div">
                                        <div class="main-box text-center custom-box">
                                            <h4 style="margin: 0 0 30px 0;" class="heading">
                                                <b>Please Verify Accounting Details.</b>
                                            </h4>

                                            <!-- Checkbox for editing -->
                                            <div class="mx-2" style="margin: 30px 0px;">
                                                <label class="container_check mt-3 text-left">
                                                    I want to edit my account details.
                                                    <input type="checkbox" v-model="form.is_edit" class="checkbox-one"
                                                        name="is_edit" value="1" />
                                                    <span class="checkmark"></span>
                                                </label>
                                            </div>

                                            <!-- Pan Card Number -->
                                            <div class="form-group form-md-line-input">
                                                <label class="col-md-12 control-label">
                                                    <i data-lucide="id-card" class="icon_logo2"></i> Pan Card Number
                                                </label>
                                                <div class="col-md-12">
                                                    <input type="text" v-model="form.pan_number"
                                                        class="form-control input-color"
                                                        placeholder="Enter Pan card number" :readonly="!form.is_edit"
                                                        minlength="10" maxlength="10" />
                                                    <span v-if="errors.pan_number"
                                                        class="text-danger pt-3 col-md-12 control-label">
                                                        {{ errors.pan_number }}
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Account Number -->
                                            <div class="form-group form-md-line-input">
                                                <label class="col-md-12 control-label">
                                                    <i data-lucide="credit-card" class="icon_logo2"></i> Account Number
                                                </label>
                                                <div class="col-md-12">
                                                    <input type="text" v-model="form.account_number"
                                                        class="form-control input-color"
                                                        placeholder="Enter Account Number" :readonly="!form.is_edit" />
                                                    <span v-if="errors.account_number"
                                                        class="text-danger pt-3 col-md-12 control-label">
                                                        {{ errors.account_number }}
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- IFSC Code -->
                                            <div class="form-group form-md-line-input">
                                                <label class="col-md-12 control-label">
                                                    <i data-lucide="landmark" class="icon_logo2"></i> IFSC Code
                                                </label>
                                                <div class="col-md-12">
                                                    <input type="text" v-model="form.IFSC_code"
                                                        class="form-control input-color" placeholder="Enter IFSC Code"
                                                        :readonly="!form.is_edit" />
                                                    <span v-if="errors.IFSC_code"
                                                        class="text-danger pt-3 col-md-12 control-label">
                                                        {{ errors.IFSC_code }}
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Cancel Cheque -->
                                            <div class="form-group form-md-line-input">
                                                <label class="col-md-12 control-label">
                                                    <i data-lucide="banknote" class="icon_logo2"></i> Cancel cheque
                                                </label>
                                                <div class="col-md-12">
                                                    <input type="file" name="cancel_cheque" id="cancel_cheque"
                                                        class="form-control input-color" :readonly="!form.is_edit"
                                                        @change="handleFileUpload" />
                                                    <span v-if="errors.cancel_cheque"
                                                        class="text-danger pt-3 col-md-12 control-label">
                                                        {{ errors.cancel_cheque }}
                                                    </span>
                                                    <div class="form-control-focus"></div>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>

                                            <div class="row mx-auto" v-show="showCropper">
                                                <div class="col-md-6 p-0 cropper-div">
                                                    <h4 style="margin:0 0 30px 0;"><i><b>Adjust photo.</b></i></h4>
                                                    <div class="cropper-view-div">
                                                        <img ref="imagePreviewContainer" v-if="imageData"
                                                            :src="imageData" class="img-fluid"
                                                            style="max-width:100%;height:auto;" />
                                                    </div>
                                                    <div class="justify-content-around mx-auto col-md-12 row">
                                                        <button type="button" @click="rotateImage"
                                                            class="cheque-rotate btn btn-primary w-100 btn-c-2 mt-2">Rotate</button>
                                                        <button type="button" @click="zoomIn"
                                                            class="cheque-rotate btn btn-primary w-100 btn-c-2 mt-2">Zoom
                                                            +</button>
                                                        <button type="button" @click="zoomOut"
                                                            class="cheque-rotate btn btn-primary w-100 btn-c-2 mt-2">Zoom
                                                            -</button>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 cropper-div">
                                                    <h4><i><b>Cropped preview.</b></i></h4>
                                                    <div v-if="croppedImage" class="cheque_preview">
                                                        <img :src="croppedImage"
                                                            style="max-width: 100%; height: auto;" />
                                                    </div>
                                                </div>
                                            </div>

                                            <input type="hidden" name="cancel_cheque_crop" id="cancel_cheque_crop"
                                                v-model="croppedImageData">

                                            <div class="cropper-div" v-show="showCropper">
                                                <div class="col-md-4 mx-auto mt-3">
                                                    <button @click="cropImage" type="button"
                                                        class="cropbutton border-success text-success mt-2">Crop</button>
                                                </div>
                                            </div>
                                            <div v-if="showSuccessMessage">
                                                <div class="main-container2 success">
                                                    <div class="check-container2" style="height: 9rem;width: 7.5rem;">
                                                        <div class="check-background">
                                                            <svg viewBox="0 0 65 51" fill="none"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M7 25L27.3077 44L58.5 7" stroke="white"
                                                                    stroke-width="13" stroke-linecap="round"
                                                                    stroke-linejoin="round" />
                                                            </svg>
                                                        </div>
                                                        <div class="check-shadow"></div>
                                                    </div>
                                                </div>
                                                <h5 class="success"><i><b>Cheque photo cropped successfully</b></i></h5>
                                            </div>
                                            <div class="main-row">
                                                <div class="col-md-12">
                                                    <div class="row mx-auto justify-content-center">
                                                        <div class="col-md-4">
                                                            <button type="submit" name="agree"
                                                                class="btn btn-primary btn-q-1 w-100 mt-2">I
                                                                Confirm</button>
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