<!-- <script setup>
import { ref, computed, watch, watchEffect, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import TopNav from '../Layout/TopNav.vue';
import axios from 'axios'
import { values } from 'lodash';

const props = defineProps({
    nextQuestion: Object,
    currentAnswer: Object,
})


const response = usePage().props.response;
const rangeAnswers = ref([]);
const numberAnswers = ref([]);
const questionHtml = ref('');
const isLast = ref(response.isLast);
const encryptedQuestionId = ref(response.encryptedQuestionId);


const {
    nextQuestion,
    currentAnswer,
    // isLast,
    decryptedDoctorId,
    doc_id,
    // encryptedQuestionId,
    csrf_token,
} = response;

const csrfToken = csrf_token;
const selectedAnswer = ref('');
const selectedAnswerMultiple = ref([]);
const otherAnswer = ref('');
const showExtraInput = ref(false);


const parsedAnswers = computed(() => {
    if (!nextQuestion.value) return [];
    return nextQuestion.value.answers
        ? nextQuestion.value.answers.split(',').map(a => a.trim())
        : [];
});


function handleNext() {
    const form = document.getElementById('survey-form');
    const formData = new FormData(form);
    const params = new URLSearchParams();

    let answerValue = "";

    for (const [key, value] of formData.entries()) {
        if (value.trim() !== "") {
            if (key === "answer") {
                answerValue = value;
            } else {
                params.append(key, value);
            }
        }
    }

    params.append("answer", answerValue);
    const encodedData = btoa(unescape(encodeURIComponent(params.toString())));

    axios.post('/survey/store-answer', {
        encodedData: encodedData
    }).then(res => {
        if (res.data.encodedData) {
            const decoded = JSON.parse(atob(res.data.encodedData));
            console.log(decoded, "decoded");

            if (decoded.nextQuestion?.id) {
                encryptedQuestionId.value = decoded.nextQuestion.id;
            }
            nextQuestion.value = { ...decoded.nextQuestion };
            console.log(nextQuestion.value?.type === 1,"tets");
            
            currentAnswer.value = { ...decoded.currentAnswer };
            isLast.value = decoded.isLast;
        }
        else if (res.data.redirect_url) {
            window.location.href = res.data.redirect_url
        }
    }).catch(err => {
        alert('Something went wrong!');
        console.error(err);
    });
}


onMounted(() => {
    if (!nextQuestion?.value || !currentAnswer?.answers) return;

    const answers = currentAnswer.answers.trim();

    console.log("Mounted: Current Answers:", answers);

    // For radio, textarea, dropdown
    if ([1, 3, 5].includes(nextQuestion.value.type)) {
        selectedAnswer.value = answers;
    }

    // For multiple checkboxes
    if ([2, 4].includes(nextQuestion.value.type)) {
        selectedAnswerMultiple.value = answers.split(',').map(a => a.trim());
    }

    // For sliders
    if (nextQuestion.value.type === 6) {
        const values = answers.split(',');
        rangeAnswers.value = parsedAnswers.value.map((_, i) => Number(values[i]) || 0);
    }

    // For numbers
    if (nextQuestion.value.type === 7) {
        const values = answers.split(',');
        numberAnswers.value = parsedAnswers.value.map((_, i) => Number(values[i]) || 0);
    }
});

watchEffect(() => {
    if (!nextQuestion?.value || !currentAnswer?.answers) return;

    const answers = currentAnswer.answers.trim();

    console.log("watchEffect Triggered: Next Question Updated", nextQuestion.value);
    console.log("Current Answers:", answers);

    if ([1, 3, 5].includes(nextQuestion.value.type)) {
        selectedAnswer.value = answers;
    } else if ([2, 4].includes(nextQuestion.value.type)) {
        selectedAnswerMultiple.value = answers.split(',').map(a => a.trim());
    } else if (nextQuestion.value.type === 6) {
        const values = answers.split(',');
        rangeAnswers.value = parsedAnswers.value.map((_, i) => Number(values[i]) || 0);
    } else if (nextQuestion.value.type === 7) {
        const values = answers.split(',');
        numberAnswers.value = parsedAnswers.value.map((_, i) => Number(values[i]) || 0);
    }
});


</script> -->
<script setup>
import { ref, computed, watchEffect, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import TopNav from '../Layout/TopNav.vue';
import axios from 'axios';

const props = defineProps({
    nextQuestion: Object,
    currentAnswer: Object,
});

const response = usePage().props.response;
const nextQuestion = ref(response.nextQuestion || {});
const currentAnswer = ref(response.currentAnswer || '');
const isLast = ref(response.isLast);
const encryptedQuestionId = ref(response.encryptedQuestionId);

const selectedAnswer = ref('');
const selectedAnswerMultiple = ref([]);
const otherAnswer = ref('');
const rangeAnswers = ref([]);
const numberAnswers = ref([]);
const decryptedDoctorId = ref(response.decryptedDoctorId);
const doc_id = ref(response.doc_id);
const remainingQuestionsCount = ref(null);
const csrfToken = response.csrf_token;

const parsedAnswers = computed(() => {
    if (!nextQuestion.value || !nextQuestion.value.answers) {
        // console.warn("No answers found for question:", nextQuestion.value);
        return [];
    }
    // console.log("Answers found:", nextQuestion.value.answers);
    return nextQuestion.value.answers.split(',').map(a => a.trim());
});

function handleNext() {
    const form = document.getElementById('survey-form');
    const formData = new FormData(form);
    const params = new URLSearchParams();

    let answerValue = "";

    for (const [key, value] of formData.entries()) {
        if (value.trim() !== "") {
            if (key === "answer") {
                answerValue = value;
            } else {
                params.append(key, value);
            }
        }
    }

    params.append("answer", answerValue);
    const encodedData = btoa(unescape(encodeURIComponent(params.toString())));

    axios.post('/survey/store-answer', { encodedData })
        .then(res => {
            if (res.data.encodedData) {
                const decoded = JSON.parse(atob(res.data.encodedData));
                // console.log("Decoded Response:", decoded);
                remainingQuestionsCount.value = decoded.remainingQuestionsCount;
                if (decoded.nextQuestion?.id) {
                    encryptedQuestionId.value = decoded.nextQuestion.id;
                    nextQuestion.value = { ...decoded.nextQuestion };
                } else {
                    nextQuestion.value = null;
                }
                currentAnswer.value = decoded.currentAnswer
                    ? { answers: String(decoded.currentAnswer) }
                    : { answers: "" };
            } else if (res.data.redirect_url) {
                window.location.href = res.data.redirect_url;
            }
        })
        .catch(err => {
            alert('Something went wrong!');
            console.error(err);
        });
}

onMounted(() => {
    if (!nextQuestion.value || !nextQuestion.value.answers) return;

    // console.log("🚀 Mounted: Question Loaded:", nextQuestion.value);
    // console.log("✅ Initial Answers:", nextQuestion.value.answers);

    if ([1, 3, 5].includes(nextQuestion.value.type)) {
        selectedAnswer.value = currentAnswer.value?.answers || "";
    } else if ([2, 4].includes(nextQuestion.value.type)) {
        selectedAnswerMultiple.value = (currentAnswer.value?.answers || "").split(',').map(a => a.trim());
    } else if (nextQuestion.value.type === 6) {
        const values = (currentAnswer.value?.answers || "").split(',');
        rangeAnswers.value = parsedAnswers.value.map((_, i) => Number(values[i]) || 0);
    } else if (nextQuestion.value.type === 7) {
        const values = (currentAnswer.value?.answers || "").split(',');
        numberAnswers.value = parsedAnswers.value.map((_, i) => Number(values[i]) || 0);
    }
});

watchEffect(() => {
    if (!nextQuestion.value) return;
    // console.log("watchEffect: Next Question Updated", nextQuestion.value);
    selectedAnswer.value = "";
    selectedAnswerMultiple.value = [];
    // console.log("Current Answer from Response:", currentAnswer.value.answers);

    // Ensure currentAnswer is always an object
    // if (typeof currentAnswer.value === 'string') {
    //     currentAnswer.value = { answers: currentAnswer.value };
    // }

    // console.log("✅ Current Answer from Response:", currentAnswer.value.answers);

    // Ensure the correct answer is selected based on question type
    if ([1, 5].includes(nextQuestion.value.type)) {
        selectedAnswer.value = currentAnswer.value?.answers || "";
    }
    else if ([2, 4].includes(nextQuestion.value.type)) {
        selectedAnswerMultiple.value = (currentAnswer.value?.answers || "").split(',').map(a => a.trim());
    }
    else if (nextQuestion.value.type === 3) {
        //  Ensure textarea answers are properly updated  
        selectedAnswer.value = currentAnswer.value?.answers || "";
    }
    else if (nextQuestion.value.type === 6 || nextQuestion.value.type === 7) {
        const values = (currentAnswer.value?.answers || "").split(',');
        numberAnswers.value = parsedAnswers.value.map((_, i) => Number(values[i]) || 0);
    }
});

</script>
<template>
    <TopNav />
    <main class="py-4">
        <div class="container full-height">
            <div class="row row-height">
                <div class="col-lg-12 content-right" id="start">
                    <div id="wizard_container">
                        <form method="POST" id="survey-form" @submit.prevent="handleNext">
                            <input type="hidden" name="question" id="question-id" :value="encryptedQuestionId">
                            <input type="hidden" name="is_last" :value="isLast">
                            <input type="hidden" name="doc_id" :value="decryptedDoctorId">
                            <input type="hidden" name="doctor_id" :value="doc_id">
                            <input type="hidden" name="previous_question" id="previous_question">
                            <input type="hidden" name="answer" id="answer" :value="selectedAnswer">
                            <input type="hidden" name="answer" id="answer" :value="selectedAnswerMultiple.join(',')">
                            <div id="middle-wizard">
                                <div class="step">
                                    <div class="nine">
                                        <h1>Survey<span>Question</span></h1>
                                    </div>
                                    <div class="dash-div">
                                        <div class="main-box custom-box">
                                            <div class="step-1-form">
                                                <div id="question-container">
                                                    <!-- <h2 class="question"><b>{{ nextQuestion?.question }}</b></h2> -->
                                                    <h2 class="question">
                                                        <b>{{ nextQuestion.value?.question ?
                                                            nextQuestion.value?.question : nextQuestion?.question }}</b>
                                                    </h2>
                                                    <!-- if type == 1 -->
                                                    <!-- <div v-if="nextQuestion.value?.type === 1 && nextQuestion.value?.answers"
                                                        class="selectBoxGroup">
                                                        <div v-for="(answer, index) in nextQuestion.value.answers.split(',')"
                                                            :key="index" class="selectBox radio">
                                                            <input type="radio" :name="answer" :id="`radio-${answer}`"
                                                                :value="answer" v-model="selectedAnswer" />
                                                            <label :for="`radio-${answer}`">{{ answer }}</label>
                                                        </div>
                                                    </div> -->

                                                    <div v-if="nextQuestion && nextQuestion.answers"
                                                        class="selectBoxGroup">
                                                        <div v-for="(answer, index) in nextQuestion.answers.split(',')"
                                                            :key="index" class="selectBox radio">
                                                            <input type="radio" :name="answer" :id="`radio-${answer}`"
                                                                :value="answer" v-model="selectedAnswer" />
                                                            <label :for="`radio-${answer}`">{{ answer }}</label>
                                                        </div>
                                                    </div>

                                                    <!-- if type == 2 -->
                                                    <div v-else-if="nextQuestion?.type === 2 && nextQuestion.answers"
                                                        class="selectBoxGroup">
                                                        <ul class="chargeCapturetable margin0" style="width:100%;">
                                                            <li v-for="(answer, index) in parsedAnswers" :key="index"
                                                                sequence="1" class="liEllipsis" :value="index">
                                                                <div class="selectBox radio">
                                                                    <input type="checkbox" name="answer[]"
                                                                        class="upbutton" :id="`radio-${answer}`"
                                                                        :value="answer" v-model="selectedAnswerMultiple"
                                                                        required />
                                                                    <label :for="`radio-${answer}`">{{ answer }}</label>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <!-- if type == 3 -->
                                                    <div v-else-if="nextQuestion?.type === 3">
                                                        <div class="form-group text-center">
                                                            <textarea id="answer" rows="5" class="form-control"
                                                                name="answer" v-model="selectedAnswer" required
                                                                placeholder="Enter your answer here"></textarea>
                                                        </div>
                                                    </div>
                                                    <!-- if type == 4 -->
                                                    <div v-else-if="nextQuestion?.type === 4 && nextQuestion.answers"
                                                        class="selectBoxGroup">
                                                        <div v-for="(answer, index) in parsedAnswers" :key="index"
                                                            class="selectBox radio">
                                                            <input type="checkbox" name="answer[]"
                                                                :id="`radio-${answer}`" :value="answer"
                                                                v-model="selectedAnswerMultiple" />
                                                            <label :for="`radio-${answer}`">{{ answer }}</label>
                                                        </div>
                                                    </div>
                                                    <!-- if type == 5 -->
                                                    <!-- If question type is 5 -->
                                                    <!-- <div v-else-if="nextQuestion?.type === 5"
                                                        class="selectBoxGroup">
                                                        <template v-if="nextQuestion?.id === 0">
                                                            <div v-for="(answer, index) in parsedAnswers" :key="index"
                                                                class="selectBox radio">
                                                                <input type="checkbox" name="answer"
                                                                    :id="`radio-${answer}`" :value="answer"
                                                                    v-model="selectedAnswerMultiple">
                                                                <label :for="`radio-${answer}`">{{ answer }}</label>
                                                            </div>
                                                            <div class="form-group text-center mt-4">
                                                                <label for="extras">Any other – Please mention</label>
                                                                <div class="text-center"
                                                                    style="width: 50%;margin: 0 auto;">
                                                                    <input type="text" name="other_answer"
                                                                        class="form-control mt-2 custom-input"
                                                                        id="extras" placeholder="Type here..."
                                                                        v-model="otherAnswer"
                                                                        @input="SetDefault($event.target.value)">
                                                                </div>
                                                            </div>
                                                        </template>
<template v-else>
                                                            <div v-for="(answer, index) in parsedAnswers" :key="index"
                                                                class="selectBox radio">
                                                                <input type="radio" name="answer"
                                                                    :id="`radio-${answer}`" :value="answer"
                                                                    v-model="selectedAnswer">
                                                                <label :for="`radio-${answer}`">{{ answer }}</label>
                                                            </div>

                                                            <div v-if="showExtraInput" id="extrasyes"
                                                                class="form-group text-center mt-3">
                                                                <label for="extras">If Yes, then why</label>
                                                                <div class="text-center"
                                                                    style="width: 50%;margin: 0 auto;">
                                                                    <input type="text" name="other_answer"
                                                                        class="form-control" id="extras"
                                                                        placeholder="Type here..." v-model="otherAnswer"
                                                                        @input="SetDefault($event.target.value)">
                                                                </div>
                                                            </div>
                                                            <div class="form-group text-center mt-4">
                                                                <label for="extras">Any other – Please mention</label>
                                                                <div class="text-center"
                                                                    style="width: 50%;margin: 0 auto;">
                                                                    <input type="text" name="other_answer"
                                                                        class="form-control mt-2 custom-input"
                                                                        id="extras" placeholder="Type here..."
                                                                        v-model="otherAnswer"
                                                                        @input="SetDefault($event.target.value)">
                                                                </div>
                                                            </div>
                                                        </template>
</div> -->

                                                    <div v-else-if="nextQuestion?.type === 5">
                                                        <div class="selectBoxGroup">
                                                            <!-- If question ID is 0 and has multiple checkbox answers -->
                                                            <template v-if="nextQuestion?.id === 0">
                                                                <div v-for="(answer, index) in parsedAnswers"
                                                                    :key="index" class="selectBox radio">
                                                                    <input type="checkbox" name="answer[]"
                                                                        :id="`radio-${answer}`" :value="answer"
                                                                        v-model="selectedAnswerMultiple">
                                                                    <label :for="`radio-${answer}`">{{ answer }}</label>
                                                                </div>
                                                            </template>

                                                            <!-- Else: Handle Yes/No + conditionally show reason -->
                                                            <template v-else>
                                                                <div v-for="(answer, index) in parsedAnswers"
                                                                    :key="index" class="selectBox radio">
                                                                    <input type="radio" name="answer"
                                                                        :id="`radio-${answer}`" :value="answer"
                                                                        v-model="selectedAnswer">
                                                                    <label :for="`radio-${answer}`">{{ answer }}</label>
                                                                </div>

                                                                <!-- If "Yes" is selected, show input for "why" -->
                                                                <div v-if="selectedAnswer === 'Yes'" id="extrasyes"
                                                                    class="form-group text-center mt-3">
                                                                    <label for="extras">If Yes, then why</label>
                                                                    <div class="text-center"
                                                                        style="width: 50%; margin: 0 auto;">
                                                                        <input type="text" name="other_answer"
                                                                            class="form-control" id="extras"
                                                                            placeholder="Type here..."
                                                                            v-model="otherAnswer"
                                                                            @change="SetDefault(otherAnswer)"
                                                                            @keyup="SetDefault(otherAnswer)"
                                                                            @paste="SetDefault(otherAnswer)"
                                                                            @input="SetDefault(otherAnswer)">
                                                                    </div>
                                                                </div>
                                                            </template>
                                                        </div>
                                                        <div class="form-group text-center mt-4">
                                                            <label for="extras">Any other – Please mention</label>
                                                            <div class="text-center"
                                                                style="width: 50%; margin: 0 auto;">
                                                                <input type="text" name="other_answer"
                                                                    class="form-control mt-2 custom-input" id="extras"
                                                                    placeholder="Type here..." v-model="otherAnswer"
                                                                    @change="SetDefault(otherAnswer)"
                                                                    @keyup="SetDefault(otherAnswer)"
                                                                    @paste="SetDefault(otherAnswer)"
                                                                    @input="SetDefault(otherAnswer)">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- if type == 6 -->
                                                    <div
                                                        v-else-if="nextQuestion?.type === 6 && parsedAnswers.length > 0">
                                                        <div v-for="(label, index) in parsedAnswers" :key="index"
                                                            class="form-group text-center">
                                                            <label :for="'range-' + label">{{ label }}</label>
                                                            <div class="text-center slidecontainer"
                                                                style="width: 50%; margin: 0 auto;">
                                                                <input type="range" :id="'range-' + label"
                                                                    class="form-control slider" min="0" max="10"
                                                                    step="1" v-model="rangeAnswers[index]"
                                                                    name="answer[]" required />
                                                                <output>{{ rangeAnswers[index] }}</output>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- if type == 7 -->
                                                    <div
                                                        v-else-if="nextQuestion?.type === 7 && parsedAnswers.length > 0">
                                                        <div v-for="(label, index) in parsedAnswers" :key="index"
                                                            class="form-group text-center">
                                                            <label :for="'range-' + label">{{ label }}</label>
                                                            <div class="text-center"
                                                                style="width: 50%; margin: 0 auto;">
                                                                <input :id="'range-' + label" type="number"
                                                                    class="form-control" name="answer[]"
                                                                    v-model.number="numberAnswers[index]"
                                                                    :max="label.trim().toLowerCase() === 'months' ? 12 : null"
                                                                    min="0" step="1" required />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="left-button-o">
                                                    <div class="col-md-12">
                                                        <div class="row mx-auto justify-content-center">
                                                            <div class="col-md-4">
                                                                <button type="button" id="prev-button"
                                                                    class="btn btn-primary btn-q-1 w-100 mt-2">
                                                                    Back
                                                                </button>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <button type="submit" id="next-button"
                                                                    class="btn btn-primary btn-q-1 w-100 mt-2">
                                                                    {{ remainingQuestionsCount === 1 ? 'SUBMIT' : 'NEXT'
                                                                    }}
                                                                </button>
                                                            </div>
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