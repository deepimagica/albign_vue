<script setup>
import { useSurvey } from '@/composables/useSurvey';
import TopNav from '../Layout/TopNav.vue';

const {
    nextQuestion,
    currentAnswer,
    isLast,
    encryptedQuestionId,
    selectedAnswer,
    selectedAnswerMultiple,
    otherAnswer,
    rangeAnswers,
    numberAnswers,
    decryptedDoctorId,
    doc_id,
    remainingQuestionsCount,
    questionHistory,
    isFirstQuestion,
    parsedAnswers,
    initializeAnswers,
    handleNext,
    handleBack,
    answerError
} = useSurvey();
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
                            <input type="hidden" name="answer" id="answer_single" :value="selectedAnswer">
                            <input type="hidden" name="answer_multiple" id="answer_multiple"
                                :value="selectedAnswerMultiple.join(',')">

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

                                                    <div v-if="nextQuestion?.type === 1 && nextQuestion.answers"
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
                                                                            v-model="otherAnswer">
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
                                                                    placeholder="Type here..." v-model="otherAnswer">
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
                                                    <p v-if="answerError" class="text-danger text-center mt-3">{{ answerError }}</p>
                                                </div>
                                                <div class="left-button-o">
                                                    <div class="col-md-12">
                                                        <div class="row mx-auto justify-content-center">
                                                            <div class="col-md-4">
                                                                <button type="button" id="prev-button"
                                                                    class="btn btn-primary btn-q-1 w-100 mt-2"
                                                                    @click="handleBack" :disabled="isFirstQuestion">
                                                                    Back
                                                                </button>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <button type="button" id="next-button"
                                                                    class="btn btn-primary btn-q-1 w-100 mt-2"
                                                                    @click="handleNext">
                                                                    {{ remainingQuestionsCount === 1 ? 'SUBMIT' : 'NEXT' }}
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