<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import StatCard from '@/Components/StatCard.vue';
import {
  ClipboardCheck,
  Plus,
  Search,
  Eye,
  Edit,
  Calendar,
  CheckCircle2,
  AlertCircle,
  XCircle,
  Clock,
  UserCheck
} from '@lucide/vue';

const props = defineProps({
  dailyCheckUps: Object,
  statsToday: Object,
  filters: Object,
  areaOptions: Array,
});

const search = ref(props.filters?.search || '');
const date = ref(props.filters?.date || '');
const status = ref(props.filters?.status || '');
const area = ref(props.filters?.area || '');

const applyFilters = () => {
  router.get(
    route('daily-check-ups.index'),
    {
      search: search.value,
      date: date.value,
      status: status.value,
      area: area.value,
    },
    { preserveState: true, replace: true }
  );
};

const resetFilters = () => {
  search.value = '';
  date.value = '';
  status.value = '';
  area.value = '';
  applyFilters();
};

const getStatusBadge = (status) => {
  switch (status) {
    case 'Fit to Work':
      return {
        label: 'Fit to Work',
        class: 'bg-emerald-50 text-emerald-700 border-emerald-200',
        dot: 'bg-emerald-500',
      };
    case 'Fit with Note':
      return {
        label: 'Fit with Note',
        class: 'bg-amber-50 text-amber-700 border-amber-200',
        dot: 'bg-amber-500',
      };
    case 'Unfit':
      return {
        label: 'Unfit (Tidak Fit)',
        class: 'bg-rose-50 text-rose-700 border-rose-200 font-bold',
        dot: 'bg-rose-600',
      };
    default:
      return {
        label: status || '-',
        class: 'bg-slate-100 text-slate-700 border-slate-200',
        dot: 'bg-slate-400',
      };
  }
};
</script>

<template>
  <AppLayout title="Daily Check Up (DCU) — HSE Pertamina Gas">
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
            <ClipboardCheck class="size-6 text-red-600" />
            <span>Daily Check Up (DCU) Harian Pekerja</span>
          </h2>
          <p class="text-xs text-slate-500 mt-0.5">Pemeriksaan skrining kesehatan fit-to-work harian sebelum memulai pekerjaan</p>
        </div>

        <Link
          v-if="$page.props.auth.user.role !== 'employee'"
          :href="route('daily-check-ups.create')"
          class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-600 text-white rounded-xl text-xs font-bold hover:bg-red-700 transition shadow-sm self-start"
        >
          <Plus class="size-4" />
          <span>Input Daily Check Up Baru</span>
        </Link>
      </div>

      <!-- Quick Metrics Bar Hari Ini (Only for Admin, HSE, Doctor, Manager) -->
      <div v-if="$page.props.auth.user.role !== 'employee'" class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <StatCard title="Total DCU Hari Ini" :value="statsToday?.total ?? 0" icon-bg="bg-slate-100 text-slate-700">
          <template #icon><ClipboardCheck class="size-5" /></template>
        </StatCard>
        <StatCard title="Fit to Work" :value="statsToday?.fit ?? 0" icon-bg="bg-emerald-50 text-emerald-600">
          <template #icon><CheckCircle2 class="size-5" /></template>
        </StatCard>
        <StatCard title="Fit with Note" :value="statsToday?.fit_with_note ?? 0" icon-bg="bg-amber-50 text-amber-600">
          <template #icon><AlertCircle class="size-5" /></template>
        </StatCard>
        <StatCard title="Unfit (Perlu Istirahat)" :value="statsToday?.unfit ?? 0" icon-bg="bg-rose-50 text-rose-600">
          <template #icon><XCircle class="size-5" /></template>
        </StatCard>
      </div>

      <!-- Filters & Search Bar -->
      <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-xs space-y-3">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
          <!-- Search -->
          <div class="relative lg:col-span-2">
            <Search class="size-4 absolute left-3 top-3 text-slate-400" />
            <input
              v-model="search"
              @keyup.enter="applyFilters"
              type="text"
              placeholder="Cari Nama atau Nomor Pekerja (NIK)..."
              class="w-full pl-9 pr-3 py-2 text-xs border border-slate-200 rounded-lg focus:outline-hidden focus:border-red-600"
            />
          </div>

          <!-- Date Filter -->
          <input
            v-model="date"
            @change="applyFilters"
            type="date"
            class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:outline-hidden focus:border-red-600 bg-white"
          />

          <!-- Status Filter -->
          <select
            v-model="status"
            @change="applyFilters"
            class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:outline-hidden focus:border-red-600 bg-white"
          >
            <option value="">Semua Status Kelayakan</option>
            <option value="Fit to Work">Fit to Work</option>
            <option value="Fit with Note">Fit with Note</option>
            <option value="Unfit">Unfit</option>
          </select>

          <!-- Buttons -->
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

      <!-- Daily Check Up Records Table -->
      <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
              <tr>
                <th class="py-3 px-4">Tgl & Waktu</th>
                <th class="py-3 px-4">No. Pekerja</th>
                <th class="py-3 px-4">Nama Pekerja</th>
                <th class="py-3 px-4">Lokasi / Area</th>
                <th class="py-3 px-4">Tekanan Darah</th>
                <th class="py-3 px-4">Nadi / Temp / SpO2</th>
                <th class="py-3 px-4">Status Kelayakan</th>
                <th class="py-3 px-4 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr
                v-for="dcu in dailyCheckUps.data"
                :key="dcu.id"
                class="hover:bg-slate-50/80 transition"
              >
                <td class="py-3 px-4 whitespace-nowrap">
                  <div class="font-bold text-slate-800">{{ dcu.tanggal_pemeriksaan }}</div>
                  <div class="text-[10px] text-slate-400 flex items-center gap-1">
                    <Clock class="size-3" />
                    <span>{{ dcu.waktu_pemeriksaan }}</span>
                  </div>
                </td>
                <td class="py-3 px-4 font-bold text-slate-900">{{ dcu.nomor_pekerja }}</td>
                <td class="py-3 px-4 font-semibold text-slate-800">
                  <div>{{ dcu.nama_pekerja }}</div>
                  <div class="text-[10px] text-slate-400">{{ dcu.jenis_pekerjaan || '-' }}</div>
                </td>
                <td class="py-3 px-4 text-slate-600">
                  <div class="font-medium text-slate-800">{{ dcu.area || dcu.region || '-' }}</div>
                  <div class="text-[10px] text-slate-400 truncate max-w-[140px]">{{ dcu.lokasi_pekerjaan || '-' }}</div>
                </td>
                <td class="py-3 px-4 whitespace-nowrap font-medium text-slate-800">
                  {{ dcu.sistolik }}/{{ dcu.diastolik }} <span class="text-[10px] text-slate-400">mmHg</span>
                </td>
                <td class="py-3 px-4 whitespace-nowrap text-slate-700">
                  <div>{{ dcu.nadi_istirahat ? `${dcu.nadi_istirahat} bpm` : '-' }} • {{ dcu.temperatur ? `${dcu.temperatur}°C` : '-' }}</div>
                  <div class="text-[10px] text-slate-400">SpO2: {{ dcu.saturasi_oksigen ? `${dcu.saturasi_oksigen}%` : '-' }}</div>
                </td>
                <td class="py-3 px-4 whitespace-nowrap">
                  <span
                    class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full border"
                    :class="getStatusBadge(dcu.status_kelayakan).class"
                  >
                    <span class="size-1.5 rounded-full" :class="getStatusBadge(dcu.status_kelayakan).dot"></span>
                    <span>{{ getStatusBadge(dcu.status_kelayakan).label }}</span>
                  </span>
                </td>
                <td class="py-3 px-4 text-right whitespace-nowrap space-x-1">
                  <Link
                    :href="route('daily-check-ups.show', dcu.id)"
                    class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 text-slate-700 hover:bg-slate-200 font-semibold rounded-md border border-slate-200 transition"
                  >
                    <Eye class="size-3.5" />
                    <span>Detail</span>
                  </Link>

                  <Link
                    v-if="$page.props.auth.user.role !== 'employee'"
                    :href="route('daily-check-ups.edit', dcu.id)"
                    class="inline-flex items-center gap-1 px-2.5 py-1 bg-red-50 text-red-600 hover:bg-red-100 font-semibold rounded-md border border-red-200 transition"
                  >
                    <Edit class="size-3.5" />
                    <span>Edit</span>
                  </Link>
                </td>
              </tr>

              <tr v-if="dailyCheckUps.data.length === 0">
                <td colspan="8" class="py-8 text-center text-slate-400">
                  Tidak ditemukan data Daily Check Up.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <Pagination :links="dailyCheckUps.links" />
      </div>
    </div>
  </AppLayout>
</template>
