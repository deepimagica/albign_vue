<script setup>
import { ref, computed, watch, watchEffect, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import TopNav from '../Layout/TopNav.vue';
import axios from 'axios'

const props = defineProps({
    nextQuestion: Object,
    currentAnswer: Object,
})

const response = usePage().props.response;
const rangeAnswers = ref([]);
const numberAnswers = ref([]);
const questionHtml = ref('')

const {
    nextQuestion,
    currentAnswer,
    isLast,
    decryptedDoctorId,
    doc_id,
    encryptedQuestionId,
    csrf_token,
} = response;

const csrfToken = csrf_token;
const selectedAnswer = ref('');
const selectedAnswerMultiple = ref([]);
const otherAnswer = ref('');
const showExtraInput = ref(false);

const parsedAnswers = computed(() => {
    if (!props.nextQuestion) return []
    return props.nextQuestion.answers
        ? props.nextQuestion.answers.split(',').map(a => a.trim())
        : []
})

function handleNext() {
    const form = document.getElementById('survey-form');
    const formData = new FormData(form);
    const params = new URLSearchParams();

    for (const [key, value] of formData.entries()) {
        params.append(key, value);
    }

    const encodedData = btoa(unescape(encodeURIComponent(params.toString())));

    axios.post('/survey/store-answer', {
        encodedData: encodedData
    }).then(res => {
        if (res.data.encodedData) {
            const decoded = JSON.parse(atob(res.data.encodedData))
            nextQuestion.value = decoded.nextQuestion
            currentAnswer.value = decoded.currentAnswer
            isLast.value = decoded.remainingQuestionsCount === 1
        } else if (res.data.redirect_url) {
            window.location.href = res.data.redirect_url
        }
    }).catch(err => {
        alert('Something went wrong!');
        console.error(err);
    });
}


onMounted(() => {

    if (!nextQuestion || !currentAnswer?.answers) return;

    const answers = currentAnswer.answers.trim();

    // For radio / textarea questions
    if ([1, 3, 5].includes(nextQuestion.type)) {
        selectedAnswer.value = answers;
    }

    // For multiple checkboxes
    if ([2, 4].includes(nextQuestion.type)) {
        selectedAnswerMultiple.value = answers.split(',').map(a => a.trim());
    }

    // For sliders
    if (nextQuestion.type === 6) {
        const values = answers.split(',');
        rangeAnswers.value = parsedAnswers.value.map((_, i) => Number(values[i]) || 0);
    }

    // For numbers
    if (nextQuestion.type === 7) {
        const values = answers.split(',');
        numberAnswers.value = parsedAnswers.value.map((_, i) => Number(values[i]) || 0);
    }

    // Special case: type 5 with "Any other"
    if (nextQuestion.type === 5) {
        const allAnswers = answers.split(',').map(a => a.trim());

        selectedAnswerMultiple.value = allAnswers.filter(ans => parsedAnswers.value.includes(ans));
        const other = allAnswers.find(ans => !parsedAnswers.value.includes(ans));
        if (other) otherAnswer.value = other;

        // If ID is not 0 and selected is "Yes"
        if (nextQuestion.id !== 0 && parsedAnswers.value.includes("Yes") && answers.includes("Yes")) {
            showExtraInput.value = true;
        }
    }
});


watchEffect(() => {
    const { nextQuestion, currentAnswer } = props
    if (!nextQuestion || !currentAnswer?.answers) return

    const answer = currentAnswer.answers.trim()

    if ([1, 3].includes(nextQuestion.type)) {
        selectedAnswer.value = answer
    } else if ([2, 4].includes(nextQuestion.type)) {
        selectedAnswerMultiple.value = answer.split(',').map(a => a.trim())
    } else if (nextQuestion.type === 6) {
        const values = answer.split(',')
        rangeAnswers.value = parsedAnswers.value.map((_, i) => Number(values[i]) || 0)
    } else if (nextQuestion.type === 7) {
        const values = answer.split(',')
        numberAnswers.value = parsedAnswers.value.map((_, i) => Number(values[i]) || 0)
    }
});
</script>
<template>
    <span id="name_error" class="error-message d-none text-danger">This Answer field is required.</span>
    <div class="step-1-form">
        <div id="question-container">
            <h2 class="question"><b>{{ nextQuestion?.question }}</b></h2>
            <!-- if type == 1 -->
            <div v-if="nextQuestion?.type === 1 && nextQuestion?.answers" class="selectBoxGroup">
                <div v-for="(answer, index) in nextQuestion.answers.split(',')" :key="index" class="selectBox radio">
                    <input type="radio" :name="`answer-${nextQuestion.id}`" :id="`radio-${answer}`" :value="answer"
                        v-model="selectedAnswer" />
                    <label :for="`radio-${answer}`">{{ answer }}</label>
                </div>
            </div>
            <!-- if type == 2 -->
            <div v-else-if="nextQuestion?.type === 2 && nextQuestion.answers" class="selectBoxGroup">
                <ul class="chargeCapturetable margin0" style="width:100%;">
                    <li v-for="(answer, index) in parsedAnswers" :key="index" sequence="1" class="liEllipsis"
                        :value="index">
                        <div class="selectBox radio">
                            <input type="checkbox" name="answer[]" class="upbutton" :id="`radio-${answer}`"
                                :value="answer" v-model="selectedAnswerMultiple" required />
                            <label :for="`radio-${answer}`">{{ answer }}</label>
                        </div>
                    </li>
                </ul>
            </div>
            <!-- if type == 3 -->
            <div v-else-if="nextQuestion?.type === 3">
                <div class="form-group text-center">
                    <textarea id="answer" rows="5" class="form-control" name="answer" v-model="selectedAnswer" required
                        placeholder="Enter your answer here"></textarea>
                </div>
            </div>
            <!-- if type == 4 -->
            <div v-else-if="nextQuestion?.type === 4 && nextQuestion.answers" class="selectBoxGroup">
                <div v-for="(answer, index) in parsedAnswers" :key="index" class="selectBox radio">
                    <input type="checkbox" name="answer[]" :id="`radio-${answer}`" :value="answer"
                        v-model="selectedAnswerMultiple" />
                    <label :for="`radio-${answer}`">{{ answer }}</label>
                </div>
            </div>
            <!-- if type == 5 -->
            <!-- If question type is 5 -->
            <div v-else-if="nextQuestion?.type === 5" class="selectBoxGroup">
                <!-- If question ID is 0 and has multiple checkbox answers -->
                <template v-if="nextQuestion?.id === 0">
                    <div v-for="(answer, index) in parsedAnswers" :key="index" class="selectBox radio">
                        <input type="checkbox" name="answer[]" :id="`radio-${answer}`" :value="answer"
                            v-model="selectedAnswerMultiple">
                        <label :for="`radio-${answer}`">{{ answer }}</label>
                    </div>

                    <!-- Extra "Any other – Please mention" field -->
                    <div class="form-group text-center mt-4">
                        <label for="extras">Any other – Please mention</label>
                        <div class="text-center" style="width: 50%;margin: 0 auto;">
                            <input type="text" name="other_answer" class="form-control mt-2 custom-input" id="extras"
                                placeholder="Type here..." v-model="otherAnswer"
                                @input="SetDefault($event.target.value)">
                        </div>
                    </div>
                </template>
                <!-- Else: handle Yes/No + conditionally show reason -->
                <template v-else>
                    <div v-for="(answer, index) in parsedAnswers" :key="index" class="selectBox radio">
                        <input type="radio" name="answer" :id="`radio-${answer}`" :value="answer"
                            v-model="selectedAnswer">
                        <label :for="`radio-${answer}`">{{ answer }}</label>
                    </div>

                    <!-- If selected answer is "Yes", show input box for "why" -->
                    <div v-if="showExtraInput" id="extrasyes" class="form-group text-center mt-3">
                        <label for="extras">If Yes, then why</label>
                        <div class="text-center" style="width: 50%;margin: 0 auto;">
                            <input type="text" name="other_answer" class="form-control" id="extras"
                                placeholder="Type here..." v-model="otherAnswer"
                                @input="SetDefault($event.target.value)">
                        </div>
                    </div>
                    <!-- Always show "Any other – please mention" field -->
                    <div class="form-group text-center mt-4">
                        <label for="extras">Any other – Please mention</label>
                        <div class="text-center" style="width: 50%;margin: 0 auto;">
                            <input type="text" name="other_answer" class="form-control mt-2 custom-input" id="extras"
                                placeholder="Type here..." v-model="otherAnswer"
                                @input="SetDefault($event.target.value)">
                        </div>
                    </div>
                </template>
            </div>
            <!-- if type == 6 -->
            <div v-else-if="nextQuestion?.type === 6 && parsedAnswers.length > 0">
                <div v-for="(label, index) in parsedAnswers" :key="index" class="form-group text-center">
                    <label :for="'range-' + label">{{ label }}</label>
                    <div class="text-center slidecontainer" style="width: 50%; margin: 0 auto;">
                        <input type="range" :id="'range-' + label" class="form-control slider" min="0" max="10" step="1"
                            v-model="rangeAnswers[index]" name="answer[]" required />
                        <output>{{ rangeAnswers[index] }}</output>
                    </div>
                </div>
            </div>
            <!-- if type == 7 -->
            <div v-else-if="nextQuestion?.type === 7 && parsedAnswers.length > 0">
                <div v-for="(label, index) in parsedAnswers" :key="index" class="form-group text-center">
                    <label :for="'range-' + label">{{ label }}</label>
                    <div class="text-center" style="width: 50%; margin: 0 auto;">
                        <input :id="'range-' + label" type="number" class="form-control" name="answer[]"
                            v-model.number="numberAnswers[index]"
                            :max="label.trim().toLowerCase() === 'months' ? 12 : null" min="0" step="1" required />
                    </div>
                </div>
            </div>
        </div>
        <div class="left-button-o">
            <div class="col-md-12">
                <div class="row mx-auto justify-content-center">
                    <div class="col-md-4">
                        <button type="button" id="prev-button" class="btn btn-primary btn-q-1 w-100 mt-2">Back
                        </button>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" id="next-button" class="btn btn-primary btn-q-1 w-100 mt-2">
                            {{ isLast == 1 ? 'SUBMIT' : 'NEXT' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>