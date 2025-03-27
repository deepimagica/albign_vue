<script setup>
import { useSignature } from "@/composables/useSignature";
import TopNav from "../Layout/TopNav.vue";

const props = defineProps({
    doctor_id: String
});

const { canvas, errors, form, clearSignature, submitSignature } = useSignature(props.doctor_id);
</script>
<template>
    <TopNav />
    <main class="py-4">
        <div class="container full-height">
            <div class="row row-height">
                <div class="col-lg-12 content-right" id="start">
                    <div id="wizard_container">
                        <form @submit.prevent="submitSignature" id="add-signature">
                            <div id="middle-wizard">
                                <div class="step">
                                    <div class="nine">
                                        <h1>E Signature<span>Please sign here</span></h1>
                                    </div>
                                    <div class="dash-div">
                                        <div class="main-box custom-box">
                                            <input type="hidden" v-model="form.signature" />
                                            <div class="border-main-on text-center">
                                                <div class="wrapper border bg-light">
                                                    <canvas ref="canvas" class="signature-pad bg-light"
                                                        style="width: 100%; height: 350px;"></canvas>
                                                </div>
                                                <span v-if="errors.signature"
                                                    class="text-danger pt-3 col-md-12 control-label">
                                                    {{ errors.signature }}
                                                </span>
                                                <div class="col-md-4 mx-auto mt-2">
                                                    <button type="button" @click="clearSignature"
                                                        class="btn btn-primary btn-c-2 w-100 mt-2">CLEAR</button>
                                                </div>
                                            </div>
                                            <div class="main-row mt-2">
                                                <div class="col-md-12">
                                                    <div class="row mx-auto justify-content-center">
                                                        <div class="col-md-4">
                                                            <a href="" @click.prevent="router.get('/dashboard')"
                                                                class="btn btn-primary btn-q-2 w-100 mt-2">BACK</a>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <button type="submit"
                                                                class="btn btn-primary btn-q-1 w-100 mt-2">I
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
