<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import ClassificationBadge from '@/Components/ClassificationBadge.vue';
import { AlertTriangle, Search, Eye, Filter, CheckCircle2 } from '@lucide/vue';

const props = defineProps({
  alerts: Object,
  filters: Object,
});

const search = ref(props.filters?.search || '');
const severity = ref(props.filters?.severity || '');
const status = ref(props.filters?.status || '');

const applyFilters = () => {
  router.get(
    route('health-alerts.index'),
    {
      search: search.value,
      severity: severity.value,
      status: status.value,
    },
    { preserveState: true, replace: true }
  );
};

const resetFilters = () => {
  search.value = '';
  severity.value = '';
  status.value = '';
  applyFilters();
};
</script>

<template>
  <AppLayout title="Health Alerts / Pemantauan Perhatian — HSE Pertamina Gas">
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
            <AlertTriangle class="size-6 text-amber-500" />
            <span>Health Alerts & Pemantauan Tindak Lanjut</span>
          </h2>
          <p class="text-xs text-slate-500 mt-0.5">Daftar karyawan dengan klasifikasi MCU P3 - P7 yang membutuhkan penanganan</p>
        </div>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs space-y-3">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
          <div class="relative">
            <Search class="size-4 absolute left-3 top-3 text-slate-400" />
            <input
              v-model="search"
              @keyup.enter="applyFilters"
              type="text"
              placeholder="Cari NIK / Nama Karyawan..."
              class="w-full pl-9 pr-3 py-2 text-xs border border-slate-200 rounded-lg focus:outline-hidden focus:border-red-600"
            />
          </div>

          <select
            v-model="severity"
            @change="applyFilters"
            class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:outline-hidden focus:border-red-600 bg-white"
          >
            <option value="">Semua Tingkat (P3 - P7)</option>
            <option value="P3">P3 — Perhatian Ringan</option>
            <option value="P4">P4 — Perhatian Sedang</option>
            <option value="P5">P5 — Perlu Tindak Lanjut</option>
            <option value="P6">P6 — Perhatian Tinggi</option>
            <option value="P7">P7 — Kondisi Kurang Baik</option>
          </select>

          <select
            v-model="status"
            @change="applyFilters"
            class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:outline-hidden focus:border-red-600 bg-white"
          >
            <option value="">Semua Status Alert</option>
            <option value="Open">Open (Belum Selesai)</option>
            <option value="In Progress">In Progress (Sedang Ditangani)</option>
            <option value="Closed">Closed (Selesai)</option>
          </select>

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

      <!-- Health Alerts Table -->
      <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
              <tr>
                <th class="py-3 px-4">Tingkat / Klasifikasi</th>
                <th class="py-3 px-4">NIK</th>
                <th class="py-3 px-4">Nama Karyawan</th>
                <th class="py-3 px-4">Kategori Alert</th>
                <th class="py-3 px-4">Alasan / Catatan Perhatian</th>
                <th class="py-3 px-4">Status Alert</th>
                <th class="py-3 px-4 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr
                v-for="alert in alerts.data"
                :key="alert.id"
                class="hover:bg-slate-50/80 transition"
              >
                <td class="py-3 px-4">
                  <ClassificationBadge :code="alert.severity" />
                </td>
                <td class="py-3 px-4 font-bold text-slate-900">{{ alert.employee?.nik }}</td>
                <td class="py-3 px-4 font-semibold text-slate-800">{{ alert.employee?.name }}</td>
                <td class="py-3 px-4 font-medium text-slate-700">{{ alert.category }}</td>
                <td class="py-3 px-4 text-slate-700 max-w-xs truncate" :title="alert.reason">
                  {{ alert.reason }}
                </td>
                <td class="py-3 px-4">
                  <span
                    class="px-2.5 py-1 rounded-full text-[10px] font-bold border"
                    :class="{
                      'bg-red-50 text-red-700 border-red-200': alert.status === 'Open',
                      'bg-amber-50 text-amber-700 border-amber-200': alert.status === 'In Progress',
                      'bg-emerald-50 text-emerald-700 border-emerald-200': alert.status === 'Closed',
                    }"
                  >
                    {{ alert.status }}
                  </span>
                </td>
                <td class="py-3 px-4 text-right whitespace-nowrap">
                  <Link
                    :href="route('health-alerts.show', alert.id)"
                    class="inline-flex items-center gap-1 px-3 py-1 bg-red-50 text-red-600 hover:bg-red-100 font-semibold rounded-md border border-red-200 transition"
                  >
                    <Eye class="size-3.5" />
                    <span>Tindak Lanjut & Timeline</span>
                  </Link>
                </td>
              </tr>

              <tr v-if="alerts.data.length === 0">
                <td colspan="7" class="py-8 text-center text-slate-400">
                  Tidak ditemukan Health Alert.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <Pagination :links="alerts.links" />
      </div>
    </div>
  </AppLayout>
</template>
