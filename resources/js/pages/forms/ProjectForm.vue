<template>
    <section class="content">
        <!-- Header -->
        <div class="d-flex justify-content-between mb-3">
            <h4>Project</h4>
            <button class="btn btn-primary mb-3" @click="openModal">
                + Tambah Project
            </button>
        </div>

        <!-- Search -->
        <div class="mb-3">
            <input
                v-model="search"
                type="text"
                class="form-control"
                placeholder="Search project..."
            />
        </div>

        <!-- Table -->
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Project Code</th>
                    <th>Project Name</th>
                    <th width="120">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="p in paginatedProjects" :key="p.id">
                    <td>{{ p.project_code }}</td>
                    <td>{{ p.project_name }}</td>
                    <td>
                        <button
                            class="btn btn-sm btn-warning mr-1"
                            @click="edit(p)"
                        >
                            Edit
                        </button>
                        <button
                            class="btn btn-sm btn-danger"
                            @click="remove(p)"
                        >
                            Delete
                        </button>
                    </td>
                </tr>
                <tr v-if="filteredProjects.length === 0">
                    <td colspan="3" class="text-center">No projects found</td>
                </tr>
            </tbody>
        </table>

        <!-- Pagination -->
        <nav v-if="totalPages > 1">
            <ul class="pagination justify-content-center">
                <li
                    class="page-item"
                    :class="{ disabled: currentPage === 1 }"
                    @click="prevPage"
                >
                    <a class="page-link" href="#">Previous</a>
                </li>
                <li
                    class="page-item"
                    v-for="page in totalPages"
                    :key="page"
                    :class="{ active: page === currentPage }"
                    @click="goToPage(page)"
                >
                    <a class="page-link" href="#">{{ page }}</a>
                </li>
                <li
                    class="page-item"
                    :class="{ disabled: currentPage === totalPages }"
                    @click="nextPage"
                >
                    <a class="page-link" href="#">Next</a>
                </li>
            </ul>
        </nav>

        <!-- MODAL -->
        <div
            class="modal fade"
            tabindex="-1"
            ref="projectModal"
            aria-hidden="true"
        >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Project</h5>
                        <button
                            type="button"
                            class="btn-close"
                            @click="closeModal"
                        ></button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group mb-2">
                            <label>Project Code</label>
                            <input
                                v-model="form.project_code"
                                class="form-control"
                            />
                        </div>

                        <div class="form-group mb-2">
                            <label>Project Name</label>
                            <input
                                v-model="form.project_name"
                                class="form-control"
                            />
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
        <div
            class="toast position-fixed bottom-0 end-0 m-3"
            ref="toastEl"
            role="alert"
            aria-live="assertive"
            aria-atomic="true"
        >
            <div class="toast-header">
                <strong class="me-auto">{{ toast.title }}</strong>
                <button
                    type="button"
                    class="btn-close"
                    @click="hideToast"
                ></button>
            </div>
            <div class="toast-body">{{ toast.message }}</div>
        </div>

        <!-- DELETE CONFIRMATION MODAL -->
        <div
            class="modal fade"
            tabindex="-1"
            ref="deleteModal"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Delete</h5>
                        <button
                            type="button"
                            class="btn-close"
                            @click="closeDeleteModal"
                        ></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this project?
                    </div>
                    <div class="modal-footer">
                        <button
                            class="btn btn-secondary"
                            @click="closeDeleteModal"
                        >
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
import { ref, computed, onMounted } from "vue";
import axios from "axios";
import "bootstrap/dist/js/bootstrap.bundle.min.js";

// MODAL
let modalInstance = null;
const projectModal = ref(null);

// TOAST
const toastEl = ref(null);
let toastInstance = null;
const toast = ref({ title: "", message: "" });
const showToast = (title, message) => {
    toast.value.title = title;
    toast.value.message = message;
    toastInstance = new bootstrap.Toast(toastEl.value, { delay: 3000 });
    toastInstance.show();
};
const hideToast = () => toastInstance?.hide();

// DELETE MODAL
const deleteModal = ref(null);
let deleteModalInstance = null;
const projectToDelete = ref(null);

onMounted(() => {
    modalInstance = new bootstrap.Modal(projectModal.value);
    deleteModalInstance = new bootstrap.Modal(deleteModal.value);
    loadData();
});

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

// DATA
const projects = ref([]);
const form = ref({ id: null, project_code: "", project_name: "" });
const search = ref("");

// PAGINATION
const currentPage = ref(1);
const perPage = 5;

// COMPUTED FILTER & PAGINATION
const filteredProjects = computed(() =>
    projects.value.filter(
        (p) =>
            p.project_code.toLowerCase().includes(search.value.toLowerCase()) ||
            p.project_name.toLowerCase().includes(search.value.toLowerCase()),
    ),
);

const totalPages = computed(() =>
    Math.ceil(filteredProjects.value.length / perPage),
);

const paginatedProjects = computed(() => {
    const start = (currentPage.value - 1) * perPage;
    return filteredProjects.value.slice(start, start + perPage);
});

// LIFECYCLE
onMounted(async () => {
    modalInstance = new bootstrap.Modal(projectModal.value);
    await loadData();
});

// LOAD DATA
const loadData = async () => {
    try {
        const res = await axios.get("/api/projects");
        projects.value = res.data.data;
    } catch {
        showToast("Error", "Failed to load projects");
    }
};

// MODAL HANDLERS
const openModal = () => {
    reset();
    modalInstance.show();
};
const closeModal = () => modalInstance.hide();

// CRUD HANDLERS
const save = async () => {
    try {
        if (form.value.id) {
            const res = await axios.put(
                `/api/projects/${form.value.id}`,
                form.value,
            );
            const index = projects.value.findIndex(
                (p) => p.id === form.value.id,
            );
            projects.value[index] = res.data.data;
            showToast("Success", "Project updated successfully");
        } else {
            const res = await axios.post("/api/projects", form.value);
            projects.value.unshift(res.data.data);
            showToast("Success", "Project created successfully");
        }
        modalInstance.hide();
        reset();
    } catch (err) {
        showToast(
            "Error",
            err.response?.data?.message || "Failed to save project",
        );
    }
};

const edit = (project) => {
    form.value = { ...project };
    modalInstance.show();
};

// const remove = async (id) => {
//     if (!confirm("Delete this project?")) return;

//     try {
//         await axios.delete(`/api/projects/${id}`);
//         projects.value = projects.value.filter((p) => p.id !== id);
//         showToast("Success", "Project deleted");
//     } catch {
//         showToast("Error", "Failed to delete project");
//     }
// };

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

// RESET FORM
const reset = () => {
    form.value = { id: null, project_code: "", project_name: "" };
};

// PAGINATION CONTROLS
const goToPage = (page) => (currentPage.value = page);
const prevPage = () => (currentPage.value = Math.max(1, currentPage.value - 1));
const nextPage = () =>
    (currentPage.value = Math.min(totalPages.value, currentPage.value + 1));
</script>

<style scoped>
.table td,
.table th {
    vertical-align: middle;
}
</style>
