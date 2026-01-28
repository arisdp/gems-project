<template>
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand -->
        <a href="#" class="brand-link">
            <span class="brand-text font-weight-light ml-2">GEMS</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <nav>
                <ul class="nav nav-pills nav-sidebar flex-column">
                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a
                            href="#"
                            class="nav-link"
                            :class="{ active: activeMenu === 'dashboard' }"
                            @click.prevent="$emit('change', 'dashboard')"
                        >
                            <i class="nav-icon fas fa-chart-bar"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    <!-- Dynamic Menus -->
                    <li class="nav-item" v-for="menu in menus" :key="menu.path">
                        <a
                            href="#"
                            class="nav-link"
                            :class="{ active: activeMenu === menu.path }"
                            @click.prevent="$emit('change', menu.path)"
                        >
                            <i class="nav-icon fas fa-edit"></i>
                            <p>{{ menu.label }}</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>
</template>

<script setup>
defineProps({
    activeMenu: {
        type: String,
        default: "dashboard",
    },
});

const menus = [
    { label: "Project", path: "project" },
    { label: "Work Package", path: "wp" },
    { label: "BOQ", path: "boq" },
    { label: "Progress", path: "progress" },
];
</script>
