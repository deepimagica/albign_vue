<script setup>
import TopNav from '../Layout/TopNav.vue';
import { router } from "@inertiajs/vue3";

const propes = defineProps({
    response: Object,
});
</script>
<template>
    <TopNav />
    <main class="py-4">
        <div class="container full-height">
            <div class="row row-height">
                <div class="col-lg-12 content-right" id="start">
                    <div id="wizard_container">
                        <div id="middle-wizard">
                            <div class="step dash-div">
                                <div class="main-box text-center custom-box ">
                                    <div class="main-container2">
                                        <div class="check-container2">
                                            <div class="check-background">
                                                <svg viewBox="0 0 65 51" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M7 25L27.3077 44L58.5 7" stroke="white" stroke-width="13"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </div>
                                            <div class="check-shadow"></div>
                                        </div>
                                    </div>
                                    <div class="tq-container">
                                        <h1 class="text-center thankYou">Thank You</h1>
                                        <h5 v-if="!response.survey?.agreement_type || response.survey.agreement_type == 0">
                                            for participating in the study...!!
                                        </h5>
                                        <h5 v-else>
                                            for completing the agreement process...!!
                                        </h5>
                                    </div>
                                    <div class=" main-row">
                                        <div class="col-md-12">
                                            <div class="row mx-auto justify-content-center">
                                                <div class="col-md-4">
                                                    <a href="" @click.prevent="router.get('/dashboard')"
                                                        class="btn btn-primary btn-q-1 w-100 mt-2">Home</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>