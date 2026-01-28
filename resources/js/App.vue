<template>
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <span class="nav-link font-weight-bold">
                        GEMS – BOQ Progress Dashboard
                    </span>
                </li>
            </ul>
        </nav>

        <!-- Sidebar (HANYA DI SINI) -->
        <Sidebar :active="page" @change="page = $event" />

        <!-- Content -->
        <div class="content-wrapper p-3">
            <Dashboard v-if="page === 'dashboard'" />
            <ProjectForm v-if="page === 'project'" />
            <WorkPackageForm v-if="page === 'wp'" />
            <BoqForm v-if="page === 'boq'" />
            <ProgressForm v-if="page === 'progress'" />
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";

import Sidebar from "../components/layout/Sidebar.vue";
import Dashboard from "./pages/Dashboard.vue";
import ProjectForm from "./pages/forms/ProjectForm.vue";
import WorkPackageForm from "./pages/forms/WorkPackageForm.vue";
import BoqForm from "./pages/forms/BoqForm.vue";
import ProgressForm from "./pages/forms/ProgressForm.vue";

const page = ref("dashboard");
</script>
