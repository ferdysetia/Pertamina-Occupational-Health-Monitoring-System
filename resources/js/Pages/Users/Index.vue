<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { UserCheck, Plus, Search, Edit, Shield, Mail } from '@lucide/vue';

const props = defineProps({
  users: Object,
  employees: Array,
  filters: Object,
});

const search = ref(props.filters?.search || '');
const role = ref(props.filters?.role || '');
const showModal = ref(false);
const isEditing = ref(false);
const editingUserId = ref(null);

const form = useForm({
  name: '',
  email: '',
  role: 'employee',
  employee_id: '',
  password: '',
});

const applyFilters = () => {
  router.get(
    route('users.index'),
    {
      search: search.value,
      role: role.value,
    },
    { preserveState: true, replace: true }
  );
};

const openCreateModal = () => {
  isEditing.value = false;
  editingUserId.value = null;
  form.reset();
  showModal.value = true;
};

const openEditModal = (user) => {
  isEditing.value = true;
  editingUserId.value = user.id;
  form.name = user.name;
  form.email = user.email;
  form.role = user.role;
  form.employee_id = user.employee_id || '';
  form.password = '';
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  form.reset();
};

const submitForm = () => {
  if (isEditing.value) {
    form.put(route('users.update', editingUserId.value), {
      onSuccess: () => closeModal(),
    });
  } else {
    form.post(route('users.store'), {
      onSuccess: () => closeModal(),
    });
  }
};
</script>

<template>
  <AppLayout title="User & Role Management — Admin Only">
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
            <UserCheck class="size-6 text-purple-600" />
            <span>Manajemen User & Hak Akses Role</span>
          </h2>
          <p class="text-xs text-slate-500 mt-0.5">Pengaturan akun pengguna dan penugasan role (Admin, HSE, Dokter, Karyawan)</p>
        </div>

        <button
          @click="openCreateModal"
          class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 text-white rounded-lg text-xs font-semibold hover:bg-purple-700 transition shadow-xs self-start"
        >
          <Plus class="size-4" />
          <span>Tambah User Pengguna</span>
        </button>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
          <Search class="size-4 absolute left-3 top-3 text-slate-400" />
          <input
            v-model="search"
            @keyup.enter="applyFilters"
            type="text"
            placeholder="Cari Nama atau Email..."
            class="w-full pl-9 pr-3 py-2 text-xs border border-slate-200 rounded-lg focus:outline-hidden focus:border-purple-600"
          />
        </div>

        <select
          v-model="role"
          @change="applyFilters"
          class="px-3 py-2 text-xs border border-slate-200 rounded-lg focus:outline-hidden focus:border-purple-600 bg-white"
        >
          <option value="">Semua Role</option>
          <option value="admin">Administrator</option>
          <option value="manager">Manager HSE</option>
          <option value="hse">HSE Officer</option>
          <option value="doctor">Dokter Perusahaan</option>
          <option value="employee">Karyawan</option>
        </select>
      </div>

      <!-- Users Table -->
      <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
              <tr>
                <th class="py-3 px-4">Nama Pengguna</th>
                <th class="py-3 px-4">Email</th>
                <th class="py-3 px-4">Role Hak Akses</th>
                <th class="py-3 px-4">Terhubung Karyawan</th>
                <th class="py-3 px-4 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="user in users.data" :key="user.id" class="hover:bg-slate-50/80 transition">
                <td class="py-3 px-4 font-bold text-slate-900">{{ user.name }}</td>
                <td class="py-3 px-4 text-slate-600">{{ user.email }}</td>
                <td class="py-3 px-4">
                  <span
                    class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase border"
                    :class="{
                      'bg-purple-100 text-purple-800 border-purple-200': user.role === 'admin',
                      'bg-amber-100 text-amber-800 border-amber-200': user.role === 'manager',
                      'bg-emerald-100 text-emerald-800 border-emerald-200': user.role === 'hse',
                      'bg-blue-100 text-blue-800 border-blue-200': user.role === 'doctor',
                      'bg-slate-100 text-slate-700 border-slate-200': user.role === 'employee',
                    }"
                  >
                    {{ user.role === 'manager' ? 'Manager HSE' : user.role }}
                  </span>
                </td>
                <td class="py-3 px-4 text-slate-600">
                  <span v-if="user.employee" class="font-semibold text-slate-800">
                    {{ user.employee.nik }} - {{ user.employee.name }}
                  </span>
                  <span v-else class="text-slate-400 italic">Tidak terhubung</span>
                </td>
                <td class="py-3 px-4 text-right">
                  <button
                    @click="openEditModal(user)"
                    class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-md border border-slate-200 transition"
                  >
                    <Edit class="size-3.5" />
                    <span>Edit Role</span>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <Pagination :links="users.links" />
      </div>

      <!-- Modal Create / Edit User -->
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
        <div class="bg-white rounded-xl border border-slate-200 p-6 max-w-md w-full shadow-xl space-y-4">
          <h3 class="text-base font-bold text-slate-900 border-b border-slate-200 pb-2">
            {{ isEditing ? 'Edit Hak Akses User' : 'Tambah User Pengguna Baru' }}
          </h3>

          <form @submit.prevent="submitForm" class="space-y-3 text-xs">
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Nama Lengkap *</label>
              <input v-model="form.name" type="text" required class="w-full px-3 py-2 border rounded-lg" />
            </div>

            <div>
              <label class="block font-semibold text-slate-700 mb-1">Email *</label>
              <input v-model="form.email" type="email" required class="w-full px-3 py-2 border rounded-lg" />
            </div>

            <div>
              <label class="block font-semibold text-slate-700 mb-1">Role Hak Akses *</label>
              <select v-model="form.role" required class="w-full px-3 py-2 border rounded-lg bg-white font-bold">
                <option value="admin">Administrator</option>
                <option value="manager">Manager HSE</option>
                <option value="hse">HSE Officer</option>
                <option value="doctor">Dokter Perusahaan</option>
                <option value="employee">Karyawan</option>
              </select>
            </div>

            <div>
              <label class="block font-semibold text-slate-700 mb-1">Hubungkan Karyawan (Opsional)</label>
              <select v-model="form.employee_id" class="w-full px-3 py-2 border rounded-lg bg-white">
                <option value="">-- Tidak Terhubung --</option>
                <option v-for="emp in employees" :key="emp.id" :value="emp.id">
                  {{ emp.nik }} - {{ emp.name }}
                </option>
              </select>
            </div>

            <div>
              <label class="block font-semibold text-slate-700 mb-1">
                Password {{ isEditing ? '(Biarkan kosong jika tidak diubah)' : '*' }}
              </label>
              <input v-model="form.password" type="password" :required="!isEditing" class="w-full px-3 py-2 border rounded-lg" />
            </div>

            <div class="flex justify-end gap-2 pt-2">
              <button @click="closeModal" type="button" class="px-4 py-2 bg-slate-100 rounded-lg text-slate-600 font-semibold">
                Batal
              </button>
              <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-purple-600 text-white font-bold rounded-lg hover:bg-purple-700">
                Simpan
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
