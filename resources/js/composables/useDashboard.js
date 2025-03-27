import { ref, watch } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import debounce from "lodash/debounce";
import Swal from 'sweetalert2';

export const useDashboard = () => {
    const props = usePage().props;
    const baseUrl = window.location.origin;
    const surveyList = ref(props.surveyList);
    const activeTab = ref(0);
    const surveyType = ref(props.surveyType);
    const doctors = ref(props.doctors);
    const searchQuery = ref("");
    const surveyId = ref('');


    const getImagePath = (filename) => {
        return `${baseUrl}/assets/img/${filename}`;
    };

    const copyText = (id) => {
        const inputElement = document.getElementById("doct" + id);
        if (inputElement) {
            inputElement.select();
            inputElement.setSelectionRange(0, 99999);

            navigator.clipboard
                .writeText(inputElement.value)
                .then(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Message copied Successfully',
                    });
                })
                .catch((err) => {
                    console.error('Failed to copy text: ', err);
                });
        } else {
            console.error('Input element not found');
        }
    };

    const openAgreement = (encryptedId) => {
        router.visit(`/user/agreement/${encryptedId}?is_check=1`, {
            method: 'get',
            preserveState: true,
            preserveScroll: true,
        });
    };

    const openSurvey = (encryptedId) => {
        router.visit(`/user/survey/${encryptedId}?is_check=1`, {
            method: 'get',
            preserveState: true,
            preserveScroll: true,
        });
    }

    const getStatusText = (status) => {
        const statusMap = {
            1: "AM",
            2: "RM",
            3: "DM",
            4: "SM",
            5: "Marketing",
            6: "Medical",
            7: "Account",
            8: "Super Admin",
        };
        return statusMap[status] || "Unknown";
    };

    const setTab = (type) => {
        activeTab.value = type;
        if (surveyType.value !== type) {
            surveyType.value = type;
            surveyId.value = '';
            searchQuery.value = '';
            router.post(
                "/dashboard",
                {
                    dashboard: type,
                    survey_id: '',
                    search: '',
                },
                {
                    preserveState: true,
                    preserveScroll: true,
                    only: ["doctors", "surveyList", "surveyType"],
                    replace: true,
                }
            );
        }

    };

    const reloadSurvey = (event) => {
        surveyId.value = event.target.value;
        router.post(
            "/dashboard",
            {
                survey_id: surveyId.value,
                search: searchQuery.value,
                dashboard: surveyType.value
            },
            {
                preserveState: true,
                preserveScroll: true,
                only: ["doctors", "surveyList", "surveyType"],
                replace: true,
            }
        );
    };


    const fetchDoctors = debounce(() => {
        router.post(
            "/dashboard",
            {
                search: searchQuery.value,
                dashboard: surveyType.value,
                survey_id: surveyId.value,
            },
            {
                preserveState: true,
                preserveScroll: true,
                only: ["doctors"],
                replace: true,
            }
        );
    }, 300);


    watch(
        () => usePage().props.doctors,
        (newDoctors) => {
            doctors.value = newDoctors;
        }
    );

    watch(() => usePage().props.surveyType, (newType) => {
        surveyType.value = newType;
    })

    watch(() => usePage().props.surveyList, (newSurveyList) => {
        surveyList.value = newSurveyList;
    });


    return {
        baseUrl,
        surveyList,
        activeTab,
        surveyId,
        surveyType,
        doctors,
        searchQuery,
        getImagePath,
        getStatusText,
        setTab,
        fetchDoctors,
        copyText,
        reloadSurvey,
        openAgreement,
        openSurvey
    };
};