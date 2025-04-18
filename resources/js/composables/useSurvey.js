import { ref, computed, watchEffect, onMounted } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import { encryptData, decryptData } from "@/utils/encryption";
import axios from 'axios';

export function useSurvey() {
    const response = decryptData(usePage().props.response);
    // console.log(response,"response");

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
    const answerError = ref("");

    const questionHistory = ref([]);
    const isFirstQuestion = computed(() => questionHistory.value.length === 0);

    const parsedAnswers = computed(() => {
        if (!nextQuestion.value || !nextQuestion.value.answers) return [];
        return nextQuestion.value.answers.split(',').map(a => a.trim());
    });

    function initializeAnswers() {
        selectedAnswer.value = "";
        selectedAnswerMultiple.value = [];

        if (!nextQuestion.value) return;

        switch (nextQuestion.value.type) {
            case 1: // Single choice
            case 3: // Textarea
            case 5: // Dropdown
                selectedAnswer.value = currentAnswer.value?.answers || "";
                break;
            case 2: // Multiple choice
            case 4: // Checkboxes
                selectedAnswerMultiple.value = (currentAnswer.value?.answers || "").split(',').map(a => a.trim());
                break;
            case 6: // Range Slider
            case 7: // Number Input
                const values = (currentAnswer.value?.answers || "").split(',');
                numberAnswers.value = parsedAnswers.value.map((_, i) => Number(values[i]) || 0);
                break;
        }
    }

    function handleNext(event) {
        event.stopPropagation();
        answerError.value = "";
        // const form = document.getElementById('survey-form');
        // const formData = new FormData(form);
        // const params = new URLSearchParams();

        // let answerValue = "";

        // for (const [key, value] of formData.entries()) {
        //     if (value.trim() !== "") {
        //         if (key === "answer") {
        //             answerValue = value;
        //         } else {
        //             params.append(key, value);
        //         }
        //     }
        // }

        // params.append("answer", answerValue);
        // const encodedData = btoa(unescape(encodeURIComponent(params.toString())));

        const form = document.getElementById('survey-form');
        if (!form) {
            console.error("Survey form not found!");
            return;
        }

        const formData = new FormData(form);
        const params = new URLSearchParams();
        let answerValue = "";
        for (const [key, value] of formData.entries()) {
            if (key === "answer") {
                answerValue += value + ",";
            }
        }
        answerValue = answerValue.replace(/,$/, "");
        let formObject = {};

        for (const [key, value] of formData.entries()) {
            formObject[key] = value.trim() === "" ? null : value;
        }

        answerValue = answerValue.replace(/,$/, "");

        const hasAnswer =
            answerValue.trim() !== "" ||
            selectedAnswerMultiple.value.length > 0 ||
            numberAnswers.value.length > 0;

        if (!hasAnswer && parsedAnswers.value.length > 0) {
            answerError.value = "The answer field is required!";
            return;
        }

        const data = encryptData(formObject);
        // console.log("Encrypted Data:", data);

        axios.post('/survey/store-answer', { data })
            .then(res => {
                if (res.data.data) {
                    // console.log(res.data.data,"data");
                    // const decoded = JSON.parse(atob(res.data.data));
                    const decoded = decryptData(res.data.data);
                    remainingQuestionsCount.value = decoded.remainingQuestionsCount;
                    if (decoded.nextQuestion?.id) {
                        if (encryptedQuestionId.value) {
                            questionHistory.value.push({
                                id: encryptedQuestionId.value,
                                answer: currentAnswer.value.answers
                            });
                        }
                        encryptedQuestionId.value = decoded.nextQuestion.id;
                        nextQuestion.value = { ...decoded.nextQuestion };
                    } else {
                        nextQuestion.value = null;
                    }
                    currentAnswer.value = decoded.currentAnswer
                        ? { answers: String(decoded.currentAnswer) }
                        : { answers: "" };
                } else if (res.data.redirect_url) {
                    // window.location.href = res.data.redirect_url;
                    router.visit(res.data.redirect_url);
                }
            })
            .catch(err => {
                alert('Something went wrong!');
                console.error(err);
            });
    }

    function handleBack() {
        if (questionHistory.value.length === 0) return;
        const lastQuestion = questionHistory.value.pop();

        // const formObject = {
        //     current_question_id: lastQuestion.id,
        //     doctor_id: decryptedDoctorId.value,
        //     answer: currentAnswer.value?.answers || "",
        //     previous_que_id : encryptedQuestionId.value
        // };
        // console.log(formObject,"formObject");
        const form = document.getElementById('survey-form');
        const formData = new FormData(form);
        const params = new URLSearchParams();
        let answerValue = "";
        for (const [key, value] of formData.entries()) {
            if (key === "answer") {
                answerValue += value + ",";
            }
        }
        answerValue = answerValue.replace(/,$/, "");

        let formObject = {};

        for (const [key, value] of formData.entries()) {
            formObject[key] = value.trim() === "" ? null : value;
        }
        formObject["current_question_id"] = lastQuestion.id;
        formObject["previous_que_id"] = encryptedQuestionId.value;

        const encryptedPayload = encryptData(formObject);

        axios.post('/survey/previous-answer', {
            data: encryptedPayload
        }).then(res => {
            if (res.data.success) {
                const decoded = decryptData(res.data.data);
                // console.log(decoded, "decoded");
                encryptedQuestionId.value = decoded.question.id;
                nextQuestion.value = { ...decoded.question };
                currentAnswer.value = decoded.answer
                    ? { answers: String(decoded.answer) }
                    : { answers: "" };
            }
        }).catch(err => {
            console.error("Error fetching previous answer:", err);
        });
    }

    onMounted(() => {
        initializeAnswers();
    });

    watchEffect(() => {
        if (nextQuestion.value) {
            initializeAnswers();
        }
    });

    return {
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
    };
}
