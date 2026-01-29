<template>
    <section class="content">
        <div class="d-flex justify-content-between mb-3">
            <h4>BOQ</h4>
            <button class="btn btn-primary" @click="openModal">
                + Tambah BOQ
            </button>
        </div>

        <div class="row mb-2">
            <div class="col-md-4">
                <input v-model="search" type="text" class="form-control" placeholder="Search BOQ / Work Package..." />
            </div>
        </div>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>WP Code</th>
                    <th>BOQ Code</th>
                    <th>Description</th>
                    <th>UOM</th>
                    <th>Qty</th>
                    <th>Unit Rate</th>
                    <!-- <th>Amount</th> -->
                    <th width="120">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="boq in paginatedData" :key="boq.id">
                    <td>{{ boq.work_package.wp_code }}</td>
                    <td>{{ boq.boq_code }}</td>
                    <td>{{ boq.description }}</td>
                    <td>{{ boq.uom }}</td>
                    <td>{{ boq.budget_qty }}</td>
                    <td>{{ formatCurrency(boq.unit_rate) }}</td>
                    <!-- <td>{{ formatCurrency(boq.amount) }}</td> -->
                    <td>
                        <button class="btn btn-sm btn-warning mr-1" @click="edit(boq)">
                            Edit
                        </button>
                        <button class="btn btn-sm btn-danger" @click="remove(boq)">
                            Delete
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- ===== Pagination ===== -->
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

        <!-- ===== MODAL ===== -->
        <div class="modal fade" ref="modalEl">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5>BOQ</h5>
                        <button class="close" @click="closeModal">
                            &times;
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label>Work Package</label>
                            <select v-model="form.work_package_id" class="form-control">
                                <option value="">
                                    -- pilih work package --
                                </option>
                                <option v-for="wp in wps" :key="wp.id" :value="wp.id">
                                    {{ wp.wp_code }} - {{ wp.wp_name }}
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>BOQ Code</label>
                            <input v-model="form.boq_code" class="form-control" />
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <input v-model="form.description" class="form-control" />
                        </div>

                        <div class="form-group">
                            <label>UOM</label>
                            <input v-model="form.uom" class="form-control" />
                        </div>

                        <div class="form-group">
                            <label>Budget Qty</label>
                            <input type="number" v-model.number="form.budget_qty" class="form-control" />
                        </div>

                        <div class="form-group">
                            <label>Unit Rate</label>
                            <input type="number" v-model.number="form.unit_rate" class="form-control" />
                        </div>

                        <div class="form-group">
                            <label>Amount</label>
                            <input class="form-control" :value="boqAmount" disabled />
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

        <!-- ===== TOAST ===== -->
        <div class="toast position-fixed" style="top:20px;right:20px;z-index:9999" ref="toastEl">
            <div class="toast-header">
                <strong class="mr-auto">{{ toast.title }}</strong>
                <button class="ml-2 mb-1 close" @click="hideToast">
                    &times;
                </button>
            </div>
            <div class="toast-body">
                {{ toast.message }}
            </div>
        </div>

        <!-- ===== DELETE CONFIRM ===== -->
        <div class="modal fade" ref="deleteModal">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Confirm Delete</h5>
                    </div>
                    <div class="modal-body">
                        Delete this BOQ?
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

/* ===== SEARCH & PAGINATION ===== */
const search = ref("");
const page = ref(1);
const perPage = 10;

/* ===== DATA ===== */
const boqs = ref([]);
const wps = ref([]);

/* ===== MODAL & TOAST ===== */
const modalEl = ref(null);
const toastEl = ref(null);
const deleteModal = ref(null);
const boqToDelete = ref(null);

let modalInstance = null;
let toastInstance = null;
let deleteModalInstance = null;

/* ===== FORM ===== */
const form = ref({
    id: null,
    work_package_id: "",
    boq_code: "",
    description: "",
    uom: "",
    budget_qty: 0,
    unit_rate: 0,
});

/* ===== TOAST ===== */
const toast = ref({ title: "", message: "" });

const showToast = (title, message) => {
    toast.value = { title, message };
    toastInstance = new bootstrap.Toast(toastEl.value, { delay: 3000 });
    toastInstance.show();
};

const hideToast = () => toastInstance?.hide();

/* ===== COMPUTED ===== */
const filteredData = computed(() => {
    if (!search.value) return boqs.value;
    const q = search.value.toLowerCase();
    return boqs.value.filter(
        (b) =>
            b.boq_code.toLowerCase().includes(q) ||
            b.description.toLowerCase().includes(q) ||
            b.work_package.wp_code.toLowerCase().includes(q)
    );
});

watch(search, () => (page.value = 1));

const totalPages = computed(() =>
    Math.ceil(filteredData.value.length / perPage)
);

const paginatedData = computed(() => {
    const start = (page.value - 1) * perPage;
    return filteredData.value.slice(start, start + perPage);
});

const startItem = computed(() =>
    filteredData.value.length ? (page.value - 1) * perPage + 1 : 0
);

const endItem = computed(() =>
    Math.min(page.value * perPage, filteredData.value.length)
);

const boqAmount = computed(
    () => form.value.budget_qty * form.value.unit_rate
);

/* ===== API ===== */
const loadData = async () => {
    boqs.value = (await axios.get("/api/boqs")).data.data;
    wps.value = (await axios.get("/api/work-packages")).data.data;
};

/* ===== LIFECYCLE ===== */
onMounted(() => {
    modalInstance = new bootstrap.Modal(modalEl.value);
    deleteModalInstance = new bootstrap.Modal(deleteModal.value);
    loadData();
});

/* ===== ACTIONS ===== */
const openModal = () => {
    reset();
    modalInstance.show();
};

const edit = (boq) => {
    form.value = { ...boq };
    modalInstance.show();
};

const save = async () => {
    try {
        if (form.value.id) {
            const res = await axios.put(
                `/api/boqs/${form.value.id}`,
                form.value
            );
            const i = boqs.value.findIndex(
                (x) => x.id === form.value.id
            );
            boqs.value[i] = res.data.data;
            showToast("Success", "BOQ updated");
        } else {
            const res = await axios.post("/api/boqs", form.value);
            boqs.value.unshift(res.data.data);
            showToast("Success", "BOQ created");
        }
        modalInstance.hide();
    } catch {
        showToast("Error", "Failed to save BOQ");
    }
};

const remove = (boq) => {
    boqToDelete.value = boq;
    deleteModalInstance.show();
};

const closeDeleteModal = () => {
    boqToDelete.value = null;
    deleteModalInstance.hide();
};

const confirmDelete = async () => {
    if (!boqToDelete.value) return;
    await axios.delete(`/api/boqs/${boqToDelete.value.id}`);
    boqs.value = boqs.value.filter(
        (b) => b.id !== boqToDelete.value.id
    );
    showToast("Success", "BOQ deleted");
    closeDeleteModal();
};

const closeModal = () => modalInstance.hide();

const reset = () => {
    form.value = {
        id: null,
        work_package_id: "",
        boq_code: "",
        description: "",
        uom: "",
        budget_qty: 0,
        unit_rate: 0,
    };
};

const formatCurrency = (n) =>
    new Intl.NumberFormat("id-ID").format(n || 0);
</script>

<style scoped>
.table td,
.table th {
    vertical-align: middle;
}
</style>
