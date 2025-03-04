<script setup>
import { computed, ref } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { encryptData } from "@/utils/encryption";
import TopNav from "../Layout/TopNav.vue";

const response = usePage().props.response;

const { doctor, nextQuestion, currentAnswer, isLast, decryptedDoctorId, doc_id, user, encryptedQuestionId } = response;

const selectedAnswer = ref(currentAnswer?.answers || "");
console.log(nextQuestion,"nextQuestion");

</script>

<template>
    <TopNav />
    <main class="py-4">
        <div class="container full-height">
            <div class="row row-height">
                <div class="col-lg-12 content-right" id="start">
                    <div id="wizard_container">
                        <form method="POST" id="survey-form">
                            <input type="hidden" name="question" id="question-id" value="{{ $encryptedQuestionId }}">
                            <input type="hidden" name="is_last" value="{{ $isLast }}">
                            <input type="hidden" name="doc_id" value="{{ $decryptedDoctorId }}">
                            <input type="hidden" name="doctor_id" value="{{ $doc_id }}">
                            <input type="hidden" name="previous_question" id="previous_question">
                            <input type="hidden" name="answer" id="answer">
                            <div id="middle-wizard">
                                <div class="step">
                                    <div class="nine">
                                        <h1>Survey<span>Question</span></h1>
                                    </div>
                                    <div class="dash-div">
                                        <div class="main-box custom-box">
                                            <div class="step-1-form">
                                                <div id="question-container">
                                                    <h2 class="question"><b>{{ nextQuestion?.question }}</b></h2>
                                                    <div v-if="nextQuestion?.type === 1 && nextQuestion?.answers"
                                                        class="selectBoxGroup">
                                                        <div v-for="(answer, index) in nextQuestion.answers.split(',')"
                                                            :key="index" class="selectBox radio">
                                                            <input type="radio" :name="`answer-${nextQuestion.id}`"
                                                                :id="`radio-${answer}`" :value="answer"
                                                                v-model="selectedAnswer" />
                                                            <label :for="`radio-${answer}`">{{ answer }}</label>
                                                        </div>
                                                    </div>
                                                    <div v-if="nextQuestion?.type === 2 && nextQuestion?.answers">

                                                    </div>
                                                    <div v-if="nextQuestion?.type === 3 && nextQuestion?.answers">
                                                        <div class="form-group text-center">
                                                            <textarea id="answer" rows="5" class="form-control"
                                                                name="answer" required autocomplete="answer" autofocus
                                                                placeholder="Enter your answer here">{{ currentAnswer.answer }}</textarea>
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