<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import ClassificationBadge from '@/Components/ClassificationBadge.vue';
import { FileHeart, Plus, Search, Eye, Edit, Calendar, Filter, RotateCcw } from '@lucide/vue';

const props = defineProps({
  mcuRecords: Object,
  filters: Object,
  distrikOptions: Array,
  fungsiOptions: Array,
  tipePekerjaanOptions: Array,
});

const search = ref(props.filters?.search || '');
const klasifikasi = ref(props.filters?.klasifikasi || '');
const distrik = ref(props.filters?.distrik || '');
const date = ref(props.filters?.date || '');
const fungsi = ref(props.filters?.fungsi || '');
const tipe_pekerjaan = ref(props.filters?.tipe_pekerjaan || '');

const applyFilters = () => {
  router.get(
    route('mcu.index'),
    {
      search: search.value,
      klasifikasi: klasifikasi.value,
      distrik: distrik.value,
      date: date.value,
      fungsi: fungsi.value,
      tipe_pekerjaan: tipe_pekerjaan.value,
    },
    { preserveState: true, replace: true }
  );
};

const resetFilters = () => {
  search.value = '';
  klasifikasi.value = '';
  distrik.value = '';
  date.value = '';
  fungsi.value = '';
  tipe_pekerjaan.value = '';
  applyFilters();
};

const hasActiveFilters = computed(() => {
  return !!(search.value || klasifikasi.value || distrik.value || date.value || fungsi.value || tipe_pekerjaan.value);
});
</script>

<template>
  <AppLayout title="Data MCU Karyawan — HSE Pertamina Gas">
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
            <FileHeart class="size-6 text-red-600" />
            <span>Manajemen Data Hasil MCU Karyawan</span>
          </h2>
          <p class="text-xs text-slate-500 mt-0.5">Daftar pemeriksaan Medical Check Up terstruktur</p>
        </div>

        <Link
          v-if="$page.props.auth.user.role !== 'employee'"
          :href="route('mcu.create')"
          class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg text-xs font-semibold hover:bg-red-700 transition shadow-xs self-start"
        >
          <Plus class="size-4" />
          <span>Input Data MCU Baru</span>
        </Link>
      </div>

      <!-- Filters Toolbar Card -->
      <div class="bg-white rounded-xl border border-slate-200/90 p-4 sm:p-5 shadow-2xs space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
          <!-- 1. Search NIK / Nama (takes 2 columns on lg) -->
          <div class="sm:col-span-2 lg:col-span-2">
            <label class="block text-[11px] font-semibold text-slate-700 mb-1.5">
              Cari NIK / Nama Karyawan
            </label>
            <div class="relative">
              <Search class="size-4 absolute left-3 top-2.5 text-slate-400" />
              <input
                v-model="search"
                @keyup.enter="applyFilters"
                type="text"
                placeholder="Cari NIK atau Nama Karyawan..."
                class="w-full pl-9 pr-3 py-2 text-xs border border-slate-200 rounded-lg focus:outline-hidden focus:border-red-600 focus:ring-1 focus:ring-red-100 transition"
              />
            </div>
          </div>

          <!-- 2. Tanggal MCU -->
          <div>
            <label class="block text-[11px] font-semibold text-slate-700 mb-1.5">
              Tanggal Pelaksanaan MCU
            </label>
            <input
              v-model="date"
              @change="applyFilters"
              type="date"
              class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:outline-hidden focus:border-red-600 focus:ring-1 focus:ring-red-100 bg-white transition text-slate-700"
              title="Pilih tanggal MCU"
            />
          </div>

          <!-- 3. Klasifikasi Kesehatan (P1 - P7) -->
          <div>
            <label class="block text-[11px] font-semibold text-slate-700 mb-1.5">
              Klasifikasi Kesehatan
            </label>
            <select
              v-model="klasifikasi"
              @change="applyFilters"
              class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:outline-hidden focus:border-red-600 focus:ring-1 focus:ring-red-100 bg-white transition"
            >
              <option value="">Semua Klasifikasi (P1 - P7)</option>
              <option value="P1">P1 — Fit / Normal</option>
              <option value="P2">P2 — Kelainan Ringan</option>
              <option value="P3">P3 — Perhatian Ringan</option>
              <option value="P4">P4 — Perhatian Sedang</option>
              <option value="P5">P5 — Perlu Tindak Lanjut</option>
              <option value="P6">P6 — Perhatian Tinggi</option>
              <option value="P7">P7 — Kondisi Kurang Baik</option>
            </select>
          </div>

          <!-- 4. Distrik Kerja -->
          <div>
            <label class="block text-[11px] font-semibold text-slate-700 mb-1.5">
              Distrik Kerja
            </label>
            <select
              v-model="distrik"
              @change="applyFilters"
              class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:outline-hidden focus:border-red-600 focus:ring-1 focus:ring-red-100 bg-white transition"
            >
              <option value="">Semua Distrik</option>
              <option v-for="d in distrikOptions" :key="d" :value="d">{{ d }}</option>
            </select>
          </div>

          <!-- 5. Fungsi / Departemen -->
          <div>
            <label class="block text-[11px] font-semibold text-slate-700 mb-1.5">
              Fungsi / Departemen
            </label>
            <select
              v-model="fungsi"
              @change="applyFilters"
              class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:outline-hidden focus:border-red-600 focus:ring-1 focus:ring-red-100 bg-white transition"
            >
              <option value="">Semua Fungsi / Dept</option>
              <option v-for="f in fungsiOptions" :key="f" :value="f">{{ f }}</option>
            </select>
          </div>

          <!-- 6. Jenis Pekerjaan -->
          <div>
            <label class="block text-[11px] font-semibold text-slate-700 mb-1.5">
              Jenis Pekerjaan
            </label>
            <select
              v-model="tipe_pekerjaan"
              @change="applyFilters"
              class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:outline-hidden focus:border-red-600 focus:ring-1 focus:ring-red-100 bg-white transition"
            >
              <option value="">Semua Jenis Pekerjaan</option>
              <option v-for="tp in tipePekerjaanOptions" :key="tp" :value="tp">{{ tp }}</option>
            </select>
          </div>

          <!-- 7. Action Buttons (Filter & Reset) -->
          <div class="flex flex-col justify-end">
            <label class="hidden lg:block text-[11px] font-semibold text-transparent mb-1.5 select-none">Aksi</label>
            <div class="flex items-center gap-2">
              <button
                @click="applyFilters"
                type="button"
                class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-red-600 text-white rounded-lg text-xs font-semibold hover:bg-red-700 active:bg-red-800 transition shadow-2xs cursor-pointer"
              >
                <Filter class="size-3.5" />
                <span>Filter</span>
              </button>
              <button
                @click="resetFilters"
                type="button"
                class="inline-flex items-center justify-center gap-1 px-3 py-2 bg-slate-100 text-slate-600 rounded-lg text-xs font-semibold hover:bg-slate-200 active:bg-slate-300 transition cursor-pointer"
                title="Reset semua filter"
              >
                <RotateCcw class="size-3.5" />
                <span>Reset</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Active Filter Indicator Bar -->
        <div v-if="hasActiveFilters" class="pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2 text-xs">
          <div class="flex items-center gap-1.5 flex-wrap">
            <span class="text-[11px] font-medium text-slate-400 mr-1">Filter Aktif:</span>
            
            <span v-if="search" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-red-50 text-red-700 text-[11px] font-medium border border-red-100">
              Cari: "{{ search }}"
              <button @click="search = ''; applyFilters()" class="hover:text-red-900 cursor-pointer ml-0.5">×</button>
            </span>
            <span v-if="date" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 text-[11px] font-medium border border-blue-100">
              Tgl: {{ date }}
              <button @click="date = ''; applyFilters()" class="hover:text-blue-900 cursor-pointer ml-0.5">×</button>
            </span>
            <span v-if="klasifikasi" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 text-[11px] font-medium border border-amber-100">
              Klasifikasi: {{ klasifikasi }}
              <button @click="klasifikasi = ''; applyFilters()" class="hover:text-amber-900 cursor-pointer ml-0.5">×</button>
            </span>
            <span v-if="distrik" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 text-[11px] font-medium border border-emerald-100">
              Distrik: {{ distrik }}
              <button @click="distrik = ''; applyFilters()" class="hover:text-emerald-900 cursor-pointer ml-0.5">×</button>
            </span>
            <span v-if="fungsi" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-purple-50 text-purple-700 text-[11px] font-medium border border-purple-100">
              Fungsi: {{ fungsi }}
              <button @click="fungsi = ''; applyFilters()" class="hover:text-purple-900 cursor-pointer ml-0.5">×</button>
            </span>
            <span v-if="tipe_pekerjaan" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-sky-50 text-sky-700 text-[11px] font-medium border border-sky-100">
              Jenis: {{ tipe_pekerjaan }}
              <button @click="tipe_pekerjaan = ''; applyFilters()" class="hover:text-sky-900 cursor-pointer ml-0.5">×</button>
            </span>
          </div>

          <button
            @click="resetFilters"
            class="text-[11px] text-red-600 hover:text-red-700 font-semibold hover:underline cursor-pointer"
          >
            Hapus Semua Filter
          </button>
        </div>
      </div>

      <!-- MCU Records Table -->
      <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
              <tr>
                <th class="py-3 px-4">Tgl MCU</th>
                <th class="py-3 px-4">NIK</th>
                <th class="py-3 px-4">Nama Karyawan</th>
                <th class="py-3 px-4">Distrik / Fungsi</th>
                <th class="py-3 px-4">Klasifikasi Kesehatan</th>
                <th class="py-3 px-4">Hasil / Diagnosa Resume</th>
                <th class="py-3 px-4 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr
                v-for="mcu in mcuRecords.data"
                :key="mcu.id"
                class="hover:bg-slate-50/80 transition"
              >
                <td class="py-3 px-4 font-medium text-slate-600 whitespace-nowrap">{{ mcu.tanggal_mcu }}</td>
                <td class="py-3 px-4 font-bold text-slate-900">{{ mcu.employee?.nik }}</td>
                <td class="py-3 px-4 font-semibold text-slate-800">{{ mcu.employee?.name }}</td>
                <td class="py-3 px-4 text-slate-600">
                  <p class="font-medium text-slate-800">{{ mcu.employee?.distrik || '-' }}</p>
                  <div class="flex items-center gap-1.5 flex-wrap mt-0.5">
                    <span v-if="mcu.employee?.fungsi" class="text-[10px] text-slate-500 font-medium">
                      {{ mcu.employee.fungsi }}
                    </span>
                    <span v-if="mcu.employee?.tipe_pekerjaan" class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                      {{ mcu.employee.tipe_pekerjaan }}
                    </span>
                  </div>
                </td>
                <td class="py-3 px-4">
                  <ClassificationBadge :code="mcu.klasifikasi_mcu" />
                </td>
                <td class="py-3 px-4 text-slate-700 max-w-xs truncate" :title="mcu.hasil_mcu">
                  {{ mcu.hasil_mcu }}
                </td>
                <td class="py-3 px-4 text-right whitespace-nowrap space-x-1">
                  <Link
                    :href="route('mcu.show', mcu.id)"
                    class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 text-slate-700 hover:bg-slate-200 font-semibold rounded-md border border-slate-200 transition"
                  >
                    <Eye class="size-3.5" />
                    <span>Detail</span>
                  </Link>

                  <Link
                    v-if="$page.props.auth.user.role !== 'employee'"
                    :href="route('mcu.edit', mcu.id)"
                    class="inline-flex items-center gap-1 px-2.5 py-1 bg-red-50 text-red-600 hover:bg-red-100 font-semibold rounded-md border border-red-200 transition"
                  >
                    <Edit class="size-3.5" />
                    <span>Edit</span>
                  </Link>
                </td>
              </tr>

              <tr v-if="mcuRecords.data.length === 0">
                <td colspan="7" class="py-8 text-center text-slate-400">
                  Tidak ditemukan record MCU.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <Pagination :links="mcuRecords.links" />
      </div>
    </div>
  </AppLayout>
</template>
