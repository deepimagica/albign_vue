// resources/js/composables/useNextQue.js
import { ref, computed, onMounted, watchEffect } from 'vue'
import axios from 'axios'

export function useNextQue(props, emit) {
  const rangeAnswers = ref([])
  const numberAnswers = ref([])
  const questionHtml = ref('')
  const selectedAnswer = ref('')
  const selectedAnswerMultiple = ref([])
  const otherAnswer = ref('')
  const showExtraInput = ref(false)

  const parsedAnswers = computed(() => {
    if (!props.nextQuestion) return []
    return props.nextQuestion.answers
      ? props.nextQuestion.answers.split(',').map(a => a.trim())
      : []
  })

  function handleNext() {
    const form = document.getElementById('survey-form')
    const formData = new FormData(form)
    const params = new URLSearchParams()

    for (const [key, value] of formData.entries()) {
      params.append(key, value)
    }

    const encodedData = btoa(unescape(encodeURIComponent(params.toString())))

    axios.post('/survey/store-answer', {
      encodedData: encodedData
    }).then(res => {
      if (res.data.encodedData) {
        const decoded = JSON.parse(atob(res.data.encodedData))

        // Emit updated data back to parent (or update refs if needed)
        emit('update:nextQuestion', decoded.nextQuestion)
        emit('update:currentAnswer', decoded.currentAnswer)
        // optionally handle last question
      } else if (res.data.redirect_url) {
        window.location.href = res.data.redirect_url
      }
    }).catch(err => {
      alert('Something went wrong!')
      console.error(err)
    })
  }

  onMounted(() => {
    applyInitialAnswerStates()
  })

  function applyInitialAnswerStates() {
    const answers = props.currentAnswer?.answers?.trim()
    if (!props.nextQuestion || !answers) return

    if ([1, 3, 5].includes(props.nextQuestion.type)) {
      selectedAnswer.value = answers
    }

    if ([2, 4].includes(props.nextQuestion.type)) {
      selectedAnswerMultiple.value = answers.split(',').map(a => a.trim())
    }

    if (props.nextQuestion.type === 6) {
      const values = answers.split(',')
      rangeAnswers.value = parsedAnswers.value.map((_, i) => Number(values[i]) || 0)
    }

    if (props.nextQuestion.type === 7) {
      const values = answers.split(',')
      numberAnswers.value = parsedAnswers.value.map((_, i) => Number(values[i]) || 0)
    }

    if (props.nextQuestion.type === 5) {
      const allAnswers = answers.split(',').map(a => a.trim())
      selectedAnswerMultiple.value = allAnswers.filter(ans => parsedAnswers.value.includes(ans))
      const other = allAnswers.find(ans => !parsedAnswers.value.includes(ans))
      if (other) otherAnswer.value = other

      if (
        props.nextQuestion.id !== 0 &&
        parsedAnswers.value.includes('Yes') &&
        answers.includes('Yes')
      ) {
        showExtraInput.value = true
      }
    }
  }

  watchEffect(() => {
    applyInitialAnswerStates()
  })

  return {
    questionHtml,
    selectedAnswer,
    selectedAnswerMultiple,
    rangeAnswers,
    numberAnswers,
    otherAnswer,
    showExtraInput,
    handleNext
  }
}
