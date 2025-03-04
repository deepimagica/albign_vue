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
            router.post(
                "/dashboard",
                { dashboard: type },
                {
                    preserveState: true,
                    preserveScroll: true,
                    only: ["doctors", "surveyType"],
                    replace: true,
                }
            );
        }
    };

    const reloadSurvey = (event) => {
        const filter = event.target.value.toUpperCase();
        const table = document.getElementById("edoc_datatable");
    
        if (!table) {
            console.warn("Table with ID 'edoc_datatable' not found.");
            return;
        }
    
        const rows = table.getElementsByTagName("tr");
    
        for (let i = 0; i < rows.length; i++) {
            const tdone = rows[i].getElementsByTagName("td")[0];
            const tdtwo = rows[i].getElementsByTagName("td")[1];
    
            if (tdone && tdtwo) {
                const txtValueOne = tdone.textContent || tdone.innerText;
                const txtValueTwo = tdtwo.textContent || tdtwo.innerText;
    
                if (
                    txtValueOne.toUpperCase().indexOf(filter) > -1 ||
                    txtValueTwo.toUpperCase().indexOf(filter) > -1
                ) {
                    rows[i].style.display = "";
                } else {
                    rows[i].style.display = "none";
                }
            }
        }
    };
    

    const fetchDoctors = debounce(() => {
        router.post(
            "/dashboard",
            { search: searchQuery.value },
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

    return {
        baseUrl,
        surveyList,
        activeTab,
        surveyType,
        doctors,
        searchQuery,
        getImagePath,
        getStatusText,
        setTab,
        fetchDoctors,
        copyText,
        reloadSurvey,
        openAgreement
    };
};