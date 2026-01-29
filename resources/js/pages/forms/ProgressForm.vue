<template>
    <section class="content">
        <!-- HEADER -->
        <div class="d-flex justify-content-between mb-3">
            <h4>Progress BOQ</h4>
            <button class="btn btn-primary" @click="openModal">
                + Tambah Progress
            </button>
        </div>

        <!-- SEARCH -->
        <div class="row mb-2">
            <div class="col-md-4">
                <input v-model="search" type="text" class="form-control" placeholder="Search BOQ Code / Date..." />
            </div>
        </div>

        <!-- TABLE -->
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>BOQ Code</th>
                    <th>Date</th>
                    <th>Actual Qty</th>
                    <th width="120">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="p in paginatedData" :key="p.id">
                    <td>{{ p.boq.boq_code }}</td>
                    <td>{{ p.progress_date }}</td>
                    <td>{{ p.actual_qty }}</td>
                    <td>
                        <!-- <button class="btn btn-sm btn-warning mr-1" @click="edit(p)">
                            Edit
                        </button> -->
                        <button class="btn btn-sm btn-danger" @click="remove(p)">
                            Delete
                        </button>
                    </td>
                </tr>

                <tr v-if="filteredData.length === 0">
                    <td colspan="4" class="text-center text-muted">
                        No data found
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- PAGINATION -->
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
                        <h5>Progress BOQ</h5>
                        <button class="close" @click="closeModal">
                            &times;
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label>BOQ</label>
                            <select v-model="form.boq_id" class="form-control">
                                <option value="">
                                    -- pilih BOQ --
                                </option>
                                <option v-for="b in boqs" :key="b.id" :value="b.id">
                                    {{ b.boq_code }}
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" v-model="form.progress_date" class="form-control" />
                        </div>

                        <div class="form-group">
                            <label>Actual Qty</label>
                            <input type="number" step="0.0001" v-model="form.actual_qty" class="form-control" />
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

        <!-- DELETE CONFIRMATION -->
        <div class="modal fade" ref="deleteModal">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Confirm Delete</h5>
                        <button type="button" class="btn-close" @click="closeDeleteModal"></button>
                    </div>
                    <div class="modal-body">
                        Delete this progress entry?
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
 * STATE: DATA
 * ========================================================= */
const progresses = ref([]);
const boqs = ref([]);

/* =========================================================
 * COMPUTED: FILTERING
 * ========================================================= */
const filteredData = computed(() => {
    if (!search.value) return progresses.value;

    const q = search.value.toLowerCase();

    return progresses.value.filter(
        (p) =>
            p.boq.boq_code.toLowerCase().includes(q) ||
            p.progress_date.includes(q)
    );
});

watch(search, () => (page.value = 1));

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
 * STATE: MODAL & TOAST
 * ========================================================= */
const modalEl = ref(null);
const toastEl = ref(null);
const deleteModal = ref(null);

let modalInstance = null;
let toastInstance = null;
let deleteModalInstance = null;

const progressToDelete = ref(null);

/* =========================================================
 * STATE: FORM
 * ========================================================= */
const form = ref({
    id: null,
    boq_id: "",
    progress_date: "",
    actual_qty: "",
});

/* =========================================================
 * TOAST
 * ========================================================= */
const toast = ref({ title: "", message: "" });

const showToast = (title, message) => {
    toast.value = { title, message };
    toastInstance = new bootstrap.Toast(toastEl.value, { delay: 3000 });
    toastInstance.show();
};

const hideToast = () => toastInstance?.hide();

/* =========================================================
 * API
 * ========================================================= */
const loadData = async () => {
    progresses.value = (await axios.get("/api/progress")).data.data;
    boqs.value = (await axios.get("/api/boqs")).data.data;
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

const closeModal = () => modalInstance.hide();

/* =========================================================
 * CRUD
 * ========================================================= */
const edit = (p) => {
    form.value = {
        id: p.id,
        boq_id: p.boq_id,
        progress_date: p.progress_date,
        actual_qty: p.actual_qty,
    };
    modalInstance.show();
};

const save = async () => {
    try {
        if (form.value.id) {
            await axios.put(
                `/api/progress/${form.value.id}`,
                form.value
            );
            showToast("Success", "Progress updated");
        } else {
            await axios.post("/api/progress", form.value);
            showToast("Success", "Progress created");
        }
        modalInstance.hide();
        loadData();
    } catch (e) {
        showToast(
            "Error",
            e.response?.data?.message || "Failed to save progress"
        );
    }
};

const remove = (p) => {
    progressToDelete.value = p;
    deleteModalInstance.show();
};

const closeDeleteModal = () => {
    progressToDelete.value = null;
    deleteModalInstance.hide();
};

const confirmDelete = async () => {
    try {
        await axios.delete(
            `/api/progress/${progressToDelete.value.id}`
        );
        showToast("Success", "Progress deleted");
        loadData();
    } catch {
        showToast("Error", "Failed to delete progress");
    } finally {
        closeDeleteModal();
    }
};

/* =========================================================
 * RESET
 * ========================================================= */
const reset = () => {
    form.value = {
        id: null,
        boq_id: "",
        progress_date: "",
        actual_qty: "",
    };
};
</script>

<style scoped>
.table td,
.table th {
    vertical-align: middle;
}
</style>
