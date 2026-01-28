<template>
    <section class="content">
        <!-- SIDEBAR MENU -->
        <!-- <Sidebar @change="page = $event" /> -->

        <!-- ================= INPUT FORMS ================= -->
        <div class="card mt-3" v-if="page !== 'dashboard'">
            <div class="card-body">
                <ProjectForm v-if="page === 'project'" />
                <WorkPackageForm v-if="page === 'wp'" />
                <BoqForm v-if="page === 'boq'" />
                <ProgressForm v-if="page === 'progress'" />
            </div>
        </div>

        <!-- ================= DASHBOARD SUMMARY ================= -->
        <div v-if="page === 'dashboard'">
            <!-- PROJECT -->
            <div class="card">
                <div class="card-header bg-primary">
                    <h3 class="card-title">Project Summary</h3>
                </div>
                <div class="card-body">
                    <p><b>Project Code:</b> {{ project.project_code }}</p>
                    <p>
                        <b>Total Contract Value:</b>
                        {{ format(project.total_amount) }}
                    </p>
                    <div class="progress">
                        <div
                            class="progress-bar bg-success"
                            :style="{ width: project.progress + '%' }"
                        >
                            {{ project.progress }}%
                        </div>
                    </div>
                </div>
            </div>

            <!-- WP -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Work Package Progress</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>WP Code</th>
                                <th>Total Amount</th>
                                <th>Progress</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="wp in workPackages" :key="wp.wp_code">
                                <td>{{ wp.wp_code }}</td>
                                <td>{{ format(wp.total_amount) }}</td>
                                <td>
                                    <div class="progress">
                                        <div
                                            class="progress-bar bg-info"
                                            :style="{
                                                width: wp.progress + '%',
                                            }"
                                        >
                                            {{ wp.progress }}%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- BOQ -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">BOQ Detail</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>BOQ Code</th>
                                <th>Budget Qty</th>
                                <th>Actual Qty</th>
                                <th>Progress (%)</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="boq in boqs" :key="boq.boq_code">
                                <td>{{ boq.boq_code }}</td>
                                <td>{{ boq.budget_qty }}</td>
                                <td>{{ boq.actual_qty }}</td>
                                <td>{{ boq.progress }}%</td>
                                <td>{{ format(boq.amount) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

import Sidebar from "../../components/layout/Sidebar.vue";
import ProjectForm from "./forms/ProjectForm.vue";
// import WorkPackageForm from "./forms/WorkPackageForm.vue";
// import BoqForm from "./forms/BoqForm.vue";
// import ProgressForm from "./forms/ProgressForm.vue";

const page = ref("dashboard");

const project = ref({});
const workPackages = ref([]);
const boqs = ref([]);

const format = (n) => new Intl.NumberFormat().format(n);

onMounted(async () => {
    const res = await axios.get("/api/dashboard");
    project.value = res.data.data.project;
    workPackages.value = res.data.data.work_packages;
    boqs.value = res.data.data.boqs;
});
</script>
