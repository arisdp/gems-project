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

        <!-- Sidebar -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="#" class="brand-link">
                <span class="brand-text font-weight-light">GEMS</span>
            </a>
            <div class="sidebar">
                <nav>
                    <ul class="nav nav-pills nav-sidebar flex-column">
                        <li class="nav-item">
                            <a href="#" class="nav-link active">
                                <i class="nav-icon fas fa-chart-bar"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Content -->
        <div class="content-wrapper p-3">
            <Dashboard />
        </div>
    </div>
</template>

<script setup>
import Dashboard from "./pages/Dashboard.vue";
</script>
