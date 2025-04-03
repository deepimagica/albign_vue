<script setup>
import TopNav from "../Layout/TopNav.vue";
import { useDashboard } from "@/composables/useDashboard";

const {
    surveyList,
    activeTab,
    doctors,
    searchQuery,
    getImagePath,
    getStatusText,
    setTab,
    baseUrl,
    fetchDoctors,
    copyText,
    reloadSurvey,
    openAgreement,
    openSurvey,
    openPDF
} = useDashboard();
</script>

<template>
    <TopNav />
    <div id="app">
        <main class="py-4">
            <div class="container full-height">
                <div class="row row-height">
                    <div class="col-lg-12 content-right" id="start">
                        <div id="wizard_container">
                            <div id="middle-wizard">
                                <div class="step">
                                    <div class="table_main">
                                        <div class="login">
                                            <div class="filter">
                                                <div class="row mx-auto">
                                                    <div class="nine">
                                                        <h1>Survey<span>Dashboard</span></h1>
                                                    </div>
                                                    <div class="col-md-12 text-center" style="margin-bottom: 25px;">
                                                        <div class="btn-switch-container">
                                                            <a class="btn-switch" :class="{ active: activeTab === 0 }"
                                                                @click.prevent="setTab(0)" href="#">
                                                                Survey
                                                            </a>
                                                            <a class="btn-switch" :class="{ active: activeTab === 1 }"
                                                                @click.prevent="setTab(1)" href="#">
                                                                Non-Survey
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="text" v-model="searchQuery" class="form-control"
                                                            placeholder="Search for names..." @input="fetchDoctors" />
                                                    </div>
                                                    <div class="col-md-6">
                                                        <select class="form-control" name="surveyType"
                                                            @change="reloadSurvey" v-model="surveyId">
                                                            <option value="">Select Topic</option>
                                                            <option v-for="survey in surveyList" :key="survey.survey_id"
                                                                :value="survey.survey_id">
                                                                {{ survey.title }}
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class=" table-form form-off dash-div">
                                                <table class="table text-center table-bordered" id="edoc_datatable">
                                                    <thead class="main_text">
                                                        <tr>
                                                            <th class="name">Doctor Name</th>
                                                            <th class="title">Topic</th>
                                                            <th class="survey">Survey</th>
                                                            <th class="send">Invite by Link</th>
                                                            <th class="pdf">Agreement</th>
                                                            <th class="Status">Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="(doctor, index) in doctors" :key="doctor.id"
                                                            :class="'survey_id' + doctor.survey_title">
                                                            <td class="name-of-dr">{{ doctor.name }}</td>
                                                            <td class="name-of-dr">
                                                                <div :class="(index % 2 === 0) ? 'titleodd' : 'titleeven'">
                                                                    {{ doctor.survey_title }}
                                                                </div>
                                                            </td>
                                                            <td class="agree agree-three">
                                                                <template
                                                                    v-if="doctor.is_accept === 0 && doctor.otp_verified === 0">
                                                                    <template v-if="doctor.is_survey_completed">
                                                                        <a href=""
                                                                            title="Click to get survey">
                                                                            <img :src="getImagePath('accept.svg')"
                                                                                class="icon_logo" alt="Dynamic Icon" />
                                                                        </a>
                                                                    </template>
                                                                    <template v-else-if="doctor.is_agreement_verified">
                                                                        <a href="javascript:;" @click.prevent="openSurvey(doctor.encrypted_id)"
                                                                            title="Click to get survey">
                                                                            <!-- <img :src="`${baseUrl}/assets/img/progress.svg`"
                                                                                class="icon_logo" alt="Dynamic Icon" /> -->
                                                                            <img :src="getImagePath('progress.svg')"
                                                                                class="icon_logo" alt="Dynamic Icon" />
                                                                        </a>
                                                                    </template>
                                                                    <template v-else>
                                                                        <a href="javascript:;"
                                                                            @click.prevent="openAgreement(doctor.encrypted_id)"
                                                                            title="Sign an agreement">
                                                                            <img :src="getImagePath('pending.svg')"
                                                                                class="icon_logo" />
                                                                        </a>
                                                                    </template>
                                                                </template>
                                                                <template v-else>
                                                                    <template v-if="doctor.is_agreement_verified">
                                                                        <img :src="getImagePath('accept.svg')"
                                                                            class="icon_logo"
                                                                            title="Survey has been completed and locked for editing." />
                                                                    </template>
                                                                    <template v-else>
                                                                        <img :src="getImagePath('pending.svg')"
                                                                            class="icon_logo"
                                                                            title="Survey has been completed and locked for editing." />
                                                                    </template>
                                                                </template>
                                                            </td>
                                                            <td class="agree agree-three">
                                                                <template
                                                                    v-if="doctor.is_accept === 0 && doctor.is_document_received === 0">
                                                                    <template>
                                                                        <textarea class="textarehid"
                                                                            :id="'doct' + doctor.id">
                                                                            Dear {{ doctor.name }} Alembic invites you to sign the agreement for educational services provided by you. 
                                                                            Click the link below to view and sign.Thank you, Regards {{ doctor.name }}
                                                                        </textarea>
                                                                    </template>
                                                                    <img :src="getImagePath('link_1.svg')"
                                                                        class="icon_logo" @click="copyText(doctor.id)"
                                                                        :id="'doct' + doctor.id" />
                                                                </template>
                                                                <template v-else>
                                                                    <img :src="getImagePath('pending.svg')"
                                                                        class="icon_logo opacity-50" />
                                                                </template>
                                                            </td>
                                                            <td class="agree agree-three">
                                                                <a href="javascript:;" @click.prevent="openPDF(doctor.encrypted_id)" title="click to get pdf">
                                                                    <img :src="getImagePath('pdf.svg')"
                                                                        class="icon_logo" />
                                                                </a>
                                                            </td>
                                                            <td class="agree agree-three">
                                                                <!-- Survey Completed -->
                                                                <template
                                                                    v-if="doctor.is_accept === 0 && doctor.is_reject === 0 && (doctor.is_hold === 0 || doctor.is_hold === 1)">
                                                                    <template
                                                                        v-if="(doctor.employee_pos === 2 || doctor.employee_pos === 3) && doctor.is_survey_completed === 1">
                                                                        <template v-if="doctor.is_accept === 0">
                                                                            <a class="accept" href="#"
                                                                                :data-value="doctor.id"
                                                                                title="Click to approve">
                                                                                <img :src="getImagePath('pending.svg')"
                                                                                    class="icon_logo"
                                                                                    style="width:30px !important" />
                                                                                <div class="icon-text remarks-open">
                                                                                    Pending Approval</div>
                                                                            </a>
                                                                        </template>
                                                                    </template>
                                                                    <!-- Pending State -->
                                                                    <template v-else>
                                                                        <img :src="getImagePath('pending.svg')"
                                                                            class="icon_logo" />
                                                                        <div class="icon-text">Pending</div>
                                                                    </template>
                                                                </template>

                                                                <!-- Hold Status -->
                                                                <template v-else-if="doctor.is_hold === 1">
                                                                    <img :src="getImagePath('hold.svg')"
                                                                        class="remarks-open icon_logo"
                                                                        :data-remarks="doctor.remarks" />
                                                                    <div class="icon-text remarks-open"
                                                                        :data-remarks="doctor.remarks">{{
                                                                            getStatusText(doctor.status) }}</div>
                                                                </template>

                                                                <!-- Reject Status -->
                                                                <template v-else-if="doctor.is_reject === 1">
                                                                    <img :src="getImagePath('cancel.svg')"
                                                                        class="remarks-open icon_logo"
                                                                        :data-remarks="doctor.remarks" />
                                                                    <div class="icon-text remarks-open"
                                                                        :data-remarks="doctor.remarks">{{
                                                                            getStatusText(doctor.status) }}</div>
                                                                </template>

                                                                <!-- Accept Status -->
                                                                <template v-else-if="doctor.is_accept === 1">
                                                                    <img :src="getImagePath('accept.svg')"
                                                                        class="icon_logo" />
                                                                    <div class="icon-text">{{
                                                                        getStatusText(doctor.status) }}</div>
                                                                </template>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
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
    </div>
</template>
<style>
.inertia-progress-bar {
    display: none !important;
}
</style>
