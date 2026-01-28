<template>
    <div>
        <!-- PROJECT SUMMARY -->
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Project Summary</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Project Code</th>
                            <th>Total Contract Value</th>
                            <th>Overall Progress (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="p in projects" :key="p.project_code">
                            <td>{{ p.project_code }}</td>
                            <td>{{ format(p.total_contract_value) }}</td>
                            <td>{{ p.overall_progress }}%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- WP SUMMARY -->
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Work Package Progress</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>WP Code</th>
                            <th>Discipline</th>
                            <th>Total BOQ Amount</th>
                            <th>Progress (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="wp in workPackages" :key="wp.id">
                            <td>{{ wp.wp_code }}</td>
                            <td>{{ wp.discipline }}</td>
                            <td>{{ format(wp.total_boq_amount) }}</td>
                            <td>{{ wp.progress_percent }}%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- BOQ DETAIL -->
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">BOQ Progress Detail</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>BOQ Code</th>
                            <th>Description</th>
                            <th>Budget Qty</th>
                            <th>Actual Qty</th>
                            <th>Progress (%)</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="boq in boqs" :key="boq.id">
                            <td>{{ boq.boq_code }}</td>
                            <td>{{ boq.description }}</td>
                            <td>{{ boq.budget_qty }}</td>
                            <td>{{ boq.actual_qty }}</td>
                            <td>{{ boq.progress_percent }}%</td>
                            <td>{{ format(boq.amount) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from "vue";
import api from "../../services/api";

const projects = ref([]);
const workPackages = ref([]);
const boqs = ref([]);

const format = (num) => {
    return new Intl.NumberFormat().format(num);
};

onMounted(async () => {
    projects.value = (await api.get("/dashboard")).data;
    workPackages.value = (await api.get("/work-packages")).data;
    boqs.value = (await api.get("/boqs")).data;
});
</script>
