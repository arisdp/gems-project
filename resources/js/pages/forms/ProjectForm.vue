<template>
    <section class="content">
        <div class="d-flex justify-content-between mb-3">
            <h4>Project</h4>
            <button class="btn btn-primary" @click="openModal">
                + Tambah Project
            </button>
        </div>

        <div class="row mb-2">
            <div class="col-md-4">
                <input v-model="search" type="text" class="form-control" placeholder="Search Project ..." />
            </div>
        </div>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Project Code</th>
                    <th>Project Name</th>
                    <th width="120">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="p in paginatedData" :key="p.id">
                    <td>{{ p.project_code }}</td>
                    <td>{{ p.project_name }}</td>
                    <td>
                        <button class="btn btn-sm btn-warning mr-1" @click="edit(p)">
                            Edit
                        </button>
                        <button class="btn btn-sm btn-danger" @click="remove(p)">
                            Delete
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                Showing {{ startItem }} - {{ endItem }}
                of {{ filteredData.length }}
            </div>

            <ul class="pagination mb-0">
                <li class="page-item" :class="{ disabled: page === 1 }">
                    <a class="page-link" href="#" @click.prevent="page--">
                        Prev
                    </a>
                </li>

                <li class="page-item" v-for="n in totalPages" :key="n" :class="{ active: n === page }">
                    <a class="page-link" href="#" @click.prevent="page = n">
                        {{ n }}
                    </a>
                </li>

                <li class="page-item" :class="{ disabled: page === totalPages }">
                    <a class="page-link" href="#" @click.prevent="page++">
                        Next
                    </a>
                </li>
            </ul>
        </div>

        <!-- MODAL -->
        <div class="modal fade" ref="modalEl">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Project</h5>
                        <button class="close" @click="closeModal">
                            &times;
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group mb-2">
                            <label>Project Code</label>
                            <input v-model="form.project_code" class="form-control" />
                        </div>

                        <div class="form-group mb-2">
                            <label>Project Name</label>
                            <input v-model="form.project_name" class="form-control" />
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" @click="closeModal">
                            Cancel
                        </button>
                        <button class="btn btn-primary" @click="save">
                            Save
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- TOAST -->
        <div class="toast position-fixed" style="top: 20px; right: 20px; z-index: 9999" ref="toastEl">
            <div class="toast-header">
                <strong class="mr-auto">
                    {{ toast.title }}
                </strong>
                <button class="ml-2 mb-1 close" @click="hideToast">
                    &times;
                </button>
            </div>
            <div class="toast-body">
                {{ toast.message }}
            </div>
        </div>

        <!-- DELETE CONFIRMATION MODAL -->
        <div class="modal fade" tabindex="-1" ref="deleteModal" aria-hidden="true">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Delete</h5>
                        <button type="button" class="btn-close" @click="closeDeleteModal"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this Project?
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" @click="closeDeleteModal">
                            Cancel
                        </button>
                        <button class="btn btn-danger" @click="confirmDelete">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { ref, onMounted, watch, computed } from "vue";
import axios from "axios";

/* =========================================================
 * STATE: SEARCH & PAGINATION
 * ========================================================= */
const search = ref("");
const page = ref(1);
const perPage = 10;

/* =========================================================
 * COMPUTED: FILTERING
 * ========================================================= */
const filteredData = computed(() => {
    if (!search.value) return projects.value;

    const q = search.value.toLowerCase();

    return projects.value.filter((p) =>
        p.project_code.toLowerCase().includes(q) ||
        p.project_name.toLowerCase().includes(q),
    );
});

watch(search, () => {
    page.value = 1;
});

/* =========================================================
 * COMPUTED: PAGINATION
 * ========================================================= */
const totalPages = computed(() =>
    Math.ceil(filteredData.value.length / perPage)
);

const paginatedData = computed(() => {
    const start = (page.value - 1) * perPage;
    return filteredData.value.slice(start, start + perPage);
});

const startItem = computed(() =>
    filteredData.value.length === 0
        ? 0
        : (page.value - 1) * perPage + 1
);

const endItem = computed(() =>
    Math.min(page.value * perPage, filteredData.value.length)
);

/* =========================================================
 * STATE: DATA
 * ========================================================= */
const projects = ref([]);

/* =========================================================
 * STATE: MODAL & TOAST
 * ========================================================= */
const modalEl = ref(null);
const toastEl = ref(null);
const deleteModal = ref(null);
const projectToDelete = ref(null);

let modalInstance = null;
let toastInstance = null;
let deleteModalInstance = null;


/* =========================================================
 * STATE: FORM
 * ========================================================= */
const form = ref({
    id: null,
    project_code: "",
    project_name: "",
});

/* =========================================================
 * STATE: TOAST MESSAGE
 * ========================================================= */
const toast = ref({
    title: "",
    message: "",
});

/* =========================================================
 * UI: TOAST HANDLER
 * ========================================================= */
const showToast = (title, message) => {
    toast.value = { title, message };
    toastInstance = new bootstrap.Toast(toastEl.value, {
        delay: 3000,
    });
    toastInstance.show();
};

/* =========================================================
 * API: LOAD DATA
 * ========================================================= */
const loadData = async () => {
    try {
        const res = await axios.get("/api/projects");
        projects.value = res.data.data;
    } catch {
        showToast("Error", "Failed to load projects");
    }
};

/* =========================================================
 * LIFECYCLE
 * ========================================================= */
onMounted(() => {
    modalInstance = new bootstrap.Modal(modalEl.value);
    deleteModalInstance = new bootstrap.Modal(deleteModal.value);
    loadData();
});

/* =========================================================
 * MODAL ACTIONS
 * ========================================================= */
const openModal = () => {
    reset();
    modalInstance.show();
};

// buka modal delete
const remove = (project) => {
    if (!project?.id) return; // safety check
    projectToDelete.value = project;
    deleteModalInstance.show();
};

// batal delete
const closeDeleteModal = () => {
    projectToDelete.value = null;
    deleteModalInstance.hide();
};

const closeModal = () => modalInstance.hide();

/* =========================================================
 * CRUD ACTIONS
 * ========================================================= */
const edit = (project) => {
    form.value = { ...project };
    modalInstance.show();
};

const save = async () => {
    try {
        if (form.value.id) {
            const res = await axios.put(
                `/api/projects/${form.value.id}`,
                form.value,
            );
            const i = projects.value.findIndex(
                (p) => p.id === form.value.id,
            );
            projects.value[i] = res.data.data;
            showToast("Success", "Project updated");
        } else {
            const res = await axios.post(
                "/api/projects",
                form.value,
            );
            projects.value.unshift(res.data.data);
            showToast("Success", "Project created");
        }
        modalInstance.hide();
        reset();
    } catch (e) {
        showToast("Error", "Failed to save project");
    }
};

// konfirmasi delete
const confirmDelete = async () => {
    if (!projectToDelete.value) return;

    try {
        await axios.delete(`/api/projects/${projectToDelete.value.id}`);
        projects.value = projects.value.filter(
            (p) => p.id !== projectToDelete.value.id,
        );
        showToast("Success", "Project deleted successfully");
    } catch {
        showToast("Error", "Failed to delete project");
    } finally {
        closeDeleteModal();
    }
};

/* =========================================================
 * FORM RESET
 * ========================================================= */
const reset = () => {
    form.value = {
        id: null,
        project_code: "",
        project_name: "",
    };
};
</script>

<style scoped>
.table td,
.table th {
    vertical-align: middle;
}
</style>
