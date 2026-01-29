<template>
    <section class="content">
        <div class="d-flex justify-content-between mb-3">
            <h4>Work Package</h4>
            <button class="btn btn-primary" @click="openModal">
                + Tambah Work Package
            </button>
        </div>

        <div class="row mb-2">
            <div class="col-md-4">
                <input v-model="search" type="text" class="form-control"
                    placeholder="Search Work Package / Project / Discipline..." />
            </div>
        </div>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Project</th>
                    <th>WP Code</th>
                    <th>WP Name</th>
                    <th>Discipline Code</th>
                    <th width="120">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="wp in paginatedData" :key="wp.id">
                    <td>{{ wp.project.project_name }}</td>
                    <td>{{ wp.wp_code }}</td>
                    <td>{{ wp.wp_name }}</td>
                    <td>{{ wp.discipline_code }}</td>
                    <td>
                        <button class="btn btn-sm btn-warning mr-1" @click="edit(wp)">
                            Edit
                        </button>
                        <button class="btn btn-sm btn-danger" @click="remove(wp)">
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
                        <h5>Work Package</h5>
                        <button class="close" @click="closeModal">
                            &times;
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label>Project</label>
                            <select v-model="form.project_id" class="form-control">
                                <option value="">
                                    -- pilih project --
                                </option>
                                <option v-for="p in projects" :key="p.id" :value="p.id">
                                    {{ p.project_name }}
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>WP Code</label>
                            <input v-model="form.wp_code" class="form-control" />
                        </div>

                        <div class="form-group">
                            <label>WP Name</label>
                            <input v-model="form.wp_name" class="form-control" />
                        </div>

                        <div class="form-group">
                            <label>Discipline</label>
                            <select v-model="form.discipline_code" class="form-control">
                                <option value="">
                                    -- Select Discipline --
                                </option>
                                <option value="Civil">Civil</option>
                                <option value="Mechanical">Mechanical</option>
                                <option value="Electrical">Electrical</option>
                                <option value="Instrumentation">
                                    Instrumentation
                                </option>
                            </select>
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
                        Are you sure you want to delete this Work Package?
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
    if (!search.value) return wps.value;

    const q = search.value.toLowerCase();

    return wps.value.filter((wp) =>
        wp.wp_code.toLowerCase().includes(q) ||
        wp.wp_name.toLowerCase().includes(q) ||
        wp.discipline_code.toLowerCase().includes(q) ||
        wp.project.project_name.toLowerCase().includes(q)
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
const wps = ref([]);
const projects = ref([]);

/* =========================================================
 * STATE: MODAL & TOAST
 * ========================================================= */
const modalEl = ref(null);
const toastEl = ref(null);
const deleteModal = ref(null);
const WptToDelete = ref(null);

let modalInstance = null;
let toastInstance = null;
let deleteModalInstance = null;


/* =========================================================
 * STATE: FORM
 * ========================================================= */
const form = ref({
    id: null,
    project_id: "",
    discipline_code: "",
    wp_code: "",
    wp_name: "",
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
    wps.value = (await axios.get("/api/work-packages")).data.data;
    projects.value = (await axios.get("/api/projects")).data.data;
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
const remove = (wp) => {
    if (!wp?.id) return; // safety check
    WptToDelete.value = wp;
    deleteModalInstance.show();
};

// batal delete
const closeDeleteModal = () => {
    WptToDelete.value = null;
    deleteModalInstance.hide();
};

const closeModal = () => modalInstance.hide();

/* =========================================================
 * CRUD ACTIONS
 * ========================================================= */
const edit = (wp) => {
    form.value = {
        id: wp.id,
        project_id: wp.project_id,
        wp_code: wp.wp_code,
        wp_name: wp.wp_name,
        discipline_code: wp.discipline_code,
    };
    modalInstance.show();
};

const save = async () => {
    try {
        if (form.value.id) {
            const res = await axios.put(
                `/api/work-packages/${form.value.id}`,
                form.value
            );
            const i = wps.value.findIndex(
                (x) => x.id === form.value.id
            );
            wps.value[i] = res.data.data;
            showToast("Success", "Work Package updated");
        } else {
            const res = await axios.post(
                "/api/work-packages",
                form.value
            );
            wps.value.unshift(res.data.data);
            showToast("Success", "Work Package created");
        }
        modalInstance.hide();
    } catch (e) {
        showToast("Error", "Failed to save data");
    }
};

// const remove = async (id) => {
//     if (!confirm("Delete this work package?")) return;

//     await axios.delete(`/api/work-packages/${id}`);
//     wps.value = wps.value.filter((x) => x.id !== id);
//     showToast("Success", "Work Package deleted");
// };

// konfirmasi delete
const confirmDelete = async () => {
    if (!WptToDelete.value) return;

    try {
        await axios.delete(`/api/work-packages/${WptToDelete.value.id}`);
        wps.value = wps.value.filter(
            (p) => p.id !== WptToDelete.value.id,
        );
        showToast("Success", "Work Package deleted successfully");
    } catch {
        showToast("Error", "Failed to delete Work Package");
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
        project_id: "",
        wp_code: "",
        wp_name: "",
        discipline_code: "",
    };
};
</script>

<style scoped>
.table td,
.table th {
    vertical-align: middle;
}
</style>
