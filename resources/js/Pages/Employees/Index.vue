<script setup>
import { ref, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import ClassificationBadge from '@/Components/ClassificationBadge.vue';
import { Search, Filter, Eye, Users, FileText } from '@lucide/vue';

const props = defineProps({
  employees: Object,
  filters: Object,
  distrikOptions: Array,
  fungsiOptions: Array,
});

const search = ref(props.filters?.search || '');
const distrik = ref(props.filters?.distrik || '');
const fungsi = ref(props.filters?.fungsi || '');
const tipe_pekerjaan = ref(props.filters?.tipe_pekerjaan || '');

const applyFilters = () => {
  router.get(
    route('employees.index'),
    {
      search: search.value,
      distrik: distrik.value,
      fungsi: fungsi.value,
      tipe_pekerjaan: tipe_pekerjaan.value,
    },
    { preserveState: true, replace: true }
  );
};

const resetFilters = () => {
  search.value = '';
  distrik.value = '';
  fungsi.value = '';
  tipe_pekerjaan.value = '';
  applyFilters();
};
</script>

<template>
  <AppLayout title="Daftar Karyawan — HSE Pertamina Gas">
    <div class="space-y-6">
      <!-- Header Bar -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
            <Users class="size-6 text-red-600" />
            <span>Data Karyawan Perusahaan</span>
          </h2>
          <p class="text-xs text-slate-500 mt-0.5">Kelola dan pantau seluruh data karyawan terdaftar</p>
        </div>
      </div>

      <!-- Filters & Search Bar Card -->
      <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs space-y-3">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
          <!-- Search input -->
          <div class="relative">
            <Search class="size-4 absolute left-3 top-3 text-slate-400" />
            <input
              v-model="search"
              @keyup.enter="applyFilters"
              type="text"
              placeholder="Cari NIK atau Nama..."
              class="w-full pl-9 pr-3 py-2 text-xs border border-slate-200 rounded-lg focus:outline-hidden focus:border-red-600"
            />
          </div>

          <!-- Distrik filter -->
          <select
            v-model="distrik"
            @change="applyFilters"
            class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:outline-hidden focus:border-red-600 bg-white"
          >
            <option value="">Semua Distrik</option>
            <option v-for="d in distrikOptions" :key="d" :value="d">{{ d }}</option>
          </select>

          <!-- Fungsi filter -->
          <select
            v-model="fungsi"
            @change="applyFilters"
            class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:outline-hidden focus:border-red-600 bg-white"
          >
            <option value="">Semua Fungsi / Dept</option>
            <option v-for="f in fungsiOptions" :key="f" :value="f">{{ f }}</option>
          </select>

          <!-- Action buttons -->
          <div class="flex items-center gap-2">
            <button
              @click="applyFilters"
              class="flex-1 px-3 py-2 bg-red-600 text-white rounded-lg text-xs font-semibold hover:bg-red-700 transition"
            >
              Filter
            </button>
            <button
              @click="resetFilters"
              class="px-3 py-2 bg-slate-100 text-slate-600 rounded-lg text-xs font-semibold hover:bg-slate-200 transition"
            >
              Reset
            </button>
          </div>
        </div>
      </div>

      <!-- Employees Table Card -->
      <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
              <tr>
                <th class="py-3 px-4">NIK (No)</th>
                <th class="py-3 px-4">Nama Karyawan</th>
                <th class="py-3 px-4">Distrik</th>
                <th class="py-3 px-4">Fungsi / Jabatan</th>
                <th class="py-3 px-4">Tipe Kerja</th>
                <th class="py-3 px-4">Klasifikasi MCU Terbaru</th>
                <th class="py-3 px-4 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr
                v-for="employee in employees.data"
                :key="employee.id"
                class="hover:bg-slate-50/80 transition"
              >
                <td class="py-3 px-4 font-bold text-slate-900">{{ employee.nik }}</td>
                <td class="py-3 px-4 font-semibold text-slate-800">{{ employee.name }}</td>
                <td class="py-3 px-4 text-slate-600">{{ employee.distrik }}</td>
                <td class="py-3 px-4 text-slate-600">{{ employee.fungsi }}</td>
                <td class="py-3 px-4">
                  <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-700">
                    {{ employee.tipe_pekerjaan }}
                  </span>
                </td>
                <td class="py-3 px-4">
                  <ClassificationBadge
                    v-if="employee.latest_mcu_record"
                    :code="employee.latest_mcu_record.klasifikasi_mcu"
                  />
                  <span v-else class="text-slate-400 italic">Belum MCU</span>
                </td>
                <td class="py-3 px-4 text-right">
                  <Link
                    :href="route('employees.show', employee.id)"
                    class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 text-slate-700 hover:bg-red-50 hover:text-red-600 font-semibold rounded-md border border-slate-200 transition"
                  >
                    <Eye class="size-3.5" />
                    <span>Detail & Histori</span>
                  </Link>
                </td>
              </tr>

              <tr v-if="employees.data.length === 0">
                <td colspan="7" class="py-8 text-center text-slate-400">
                  Tidak ditemukan data karyawan sesuai pencarian/filter.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <Pagination :links="employees.links" />
      </div>
    </div>
  </AppLayout>
</template>
