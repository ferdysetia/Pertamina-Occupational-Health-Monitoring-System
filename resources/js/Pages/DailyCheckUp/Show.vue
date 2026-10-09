<script setup>
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import {
  ClipboardCheck,
  ChevronLeft,
  Edit,
  User,
  HeartPulse,
  Stethoscope,
  AlertTriangle,
  CheckCircle2,
  AlertCircle,
  XCircle,
  MapPin,
  Calendar,
  Clock,
  Printer,
  ShieldCheck,
  Building2,
  FileHeart
} from '@lucide/vue';

const props = defineProps({
  dailyCheckUp: Object,
});

const page = usePage();
const user = computed(() => page.props.auth?.user);

const statusBadge = computed(() => {
  switch (props.dailyCheckUp.status_kelayakan) {
    case 'Fit to Work':
      return {
        bg: 'bg-emerald-50 border-emerald-200 text-emerald-700',
        iconBg: 'bg-emerald-500 text-white',
        icon: CheckCircle2,
        label: 'Fit to Work (Layak Bekerja)',
      };
    case 'Fit with Note':
      return {
        bg: 'bg-amber-50 border-amber-200 text-amber-700',
        iconBg: 'bg-amber-500 text-white',
        icon: AlertCircle,
        label: 'Fit with Note (Layak dengan Catatan)',
      };
    case 'Unfit':
      return {
        bg: 'bg-rose-50 border-rose-200 text-rose-700',
        iconBg: 'bg-rose-500 text-white',
        icon: XCircle,
        label: 'Unfit (Tidak Layak Bekerja)',
      };
    default:
      return {
        bg: 'bg-slate-50 border-slate-200 text-slate-700',
        iconBg: 'bg-slate-500 text-white',
        icon: AlertCircle,
        label: props.dailyCheckUp.status_kelayakan || 'Belum Diperiksa',
      };
  }
});

const formatDate = (val) => {
  if (!val) return '-';
  const d = new Date(val);
  return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
};

const printDCU = () => {
  window.print();
};
</script>

<template>
  <AppLayout :title="`DCU: ${dailyCheckUp.nama_pekerja} (${dailyCheckUp.nomor_pekerja})`">
    <div class="max-w-5xl mx-auto space-y-6 print:space-y-4">
      <!-- Breadcrumb & Top Controls (Hidden on print) -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 print:hidden">
        <Link
          :href="route('daily-check-ups.index')"
          class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-red-600 transition"
        >
          <ChevronLeft class="size-4" />
          <span>Kembali ke Daftar Daily Check Up</span>
        </Link>

        <div class="flex items-center gap-2">
          <button
            @click="printDCU"
            class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-semibold shadow-2xs transition"
          >
            <Printer class="size-3.5 text-slate-500" />
            <span>Cetak Rekam DCU</span>
          </button>

          <Link
            v-if="user?.role !== 'employee'"
            :href="route('daily-check-ups.edit', dailyCheckUp.id)"
            class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-bold shadow-2xs transition"
          >
            <Edit class="size-3.5" />
            <span>Edit Hasil DCU</span>
          </Link>
        </div>
      </div>

      <!-- MAIN SUMMARY HEADER CARD -->
      <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
        <!-- Banner Header with Pertagas Theme -->
        <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white p-6 sm:p-8 relative">
          <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
            <div class="flex items-start sm:items-center gap-4">
              <div class="size-16 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 text-white flex items-center justify-center font-bold text-2xl shrink-0 shadow-inner">
                <ClipboardCheck class="size-8 text-red-400" />
              </div>
              <div>
                <div class="flex flex-wrap items-center gap-2.5 mb-1">
                  <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight">{{ dailyCheckUp.nama_pekerja }}</h1>
                  <span class="px-2.5 py-0.5 rounded-lg bg-red-600/30 text-red-200 border border-red-500/40 text-xs font-mono font-bold">
                    {{ dailyCheckUp.nomor_pekerja }}
                  </span>
                </div>
                <p class="text-xs text-slate-300 flex flex-wrap items-center gap-x-3 gap-y-1">
                  <span class="flex items-center gap-1">
                    <Building2 class="size-3.5 text-slate-400" />
                    {{ dailyCheckUp.jenis_pekerjaan || 'Pekerja Pertamina Gas' }}
                  </span>
                  <span>&bull;</span>
                  <span class="flex items-center gap-1">
                    <MapPin class="size-3.5 text-slate-400" />
                    {{ dailyCheckUp.area || dailyCheckUp.region || 'Area Operasi' }}
                  </span>
                </p>
              </div>
            </div>

            <!-- Fit Status Big Badge -->
            <div class="flex flex-col sm:items-end">
              <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-1.5">Status Kelayakan Kerja</div>
              <div
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border text-xs sm:text-sm font-bold shadow-sm"
                :class="statusBadge.bg"
              >
                <div class="size-5 rounded-full flex items-center justify-center shrink-0" :class="statusBadge.iconBg">
                  <component :is="statusBadge.icon" class="size-3.5" />
                </div>
                <span>{{ statusBadge.label }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Examination Meta Bar -->
        <div class="bg-slate-50/80 border-b border-slate-200/80 px-6 py-3.5 flex flex-wrap items-center justify-between gap-4 text-xs">
          <div class="flex flex-wrap items-center gap-6 text-slate-600">
            <span class="flex items-center gap-1.5 font-medium">
              <Calendar class="size-4 text-slate-400" />
              <span>Tanggal: <strong class="text-slate-800 font-semibold">{{ formatDate(dailyCheckUp.tanggal_pemeriksaan) }}</strong></span>
            </span>
            <span class="flex items-center gap-1.5 font-medium">
              <Clock class="size-4 text-slate-400" />
              <span>Waktu: <strong class="text-slate-800 font-semibold font-mono">{{ dailyCheckUp.waktu_pemeriksaan?.substring(0, 5) || '-' }} WIB</strong></span>
            </span>
            <span class="flex items-center gap-1.5 font-medium">
              <ShieldCheck class="size-4 text-slate-400" />
              <span>Pemeriksa: <strong class="text-slate-800 font-semibold">{{ dailyCheckUp.examiner?.name || 'HSE / Medical Team' }}</strong></span>
            </span>
          </div>

          <div v-if="dailyCheckUp.employee_id">
            <Link
              v-if="user?.role !== 'employee'"
              :href="route('employees.show', dailyCheckUp.employee_id)"
              class="inline-flex items-center gap-1 text-xs font-semibold text-red-600 hover:text-red-700 underline print:hidden"
            >
              <FileHeart class="size-3.5" />
              <span>Lihat Rekam Medis MCU Master</span>
            </Link>
          </div>
        </div>
      </div>

      <!-- GRID LAYOUT: SECTIONS -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- LEFT COLUMN: IDENTITAS & TANYA JAWAB (1 COL) -->
        <div class="space-y-6">
          <!-- CARD 1: IDENTITAS PEKERJA & LOKASI -->
          <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-xs">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 flex items-center gap-2 pb-3 border-b border-slate-100 mb-4">
              <User class="size-4 text-red-600" />
              <span>Identitas & Lokasi Pekerjaan</span>
            </h3>

            <dl class="space-y-3 text-xs">
              <div class="flex justify-between py-1 border-b border-slate-50">
                <dt class="text-slate-500">Nama Lengkap</dt>
                <dd class="font-bold text-slate-800 text-right">{{ dailyCheckUp.nama_pekerja }}</dd>
              </div>
              <div class="flex justify-between py-1 border-b border-slate-50">
                <dt class="text-slate-500">Nomor Pekerja (NIK)</dt>
                <dd class="font-mono font-bold text-slate-800 text-right">{{ dailyCheckUp.nomor_pekerja }}</dd>
              </div>
              <div class="flex justify-between py-1 border-b border-slate-50">
                <dt class="text-slate-500">Tanggal Lahir</dt>
                <dd class="font-medium text-slate-800 text-right">{{ formatDate(dailyCheckUp.tanggal_lahir) }}</dd>
              </div>
              <div class="flex justify-between py-1 border-b border-slate-50">
                <dt class="text-slate-500">Usia</dt>
                <dd class="font-bold text-slate-800 text-right">{{ dailyCheckUp.usia ? `${dailyCheckUp.usia} Tahun` : '-' }}</dd>
              </div>
              <div class="flex justify-between py-1 border-b border-slate-50">
                <dt class="text-slate-500">Jenis Pekerjaan</dt>
                <dd class="font-medium text-slate-800 text-right">{{ dailyCheckUp.jenis_pekerjaan || '-' }}</dd>
              </div>
              <div class="flex justify-between py-1 border-b border-slate-50">
                <dt class="text-slate-500">Region</dt>
                <dd class="font-medium text-slate-800 text-right">{{ dailyCheckUp.region || '-' }}</dd>
              </div>
              <div class="flex justify-between py-1 border-b border-slate-50">
                <dt class="text-slate-500">Area / Distrik</dt>
                <dd class="font-medium text-slate-800 text-right">{{ dailyCheckUp.area || '-' }}</dd>
              </div>
              <div class="flex justify-between py-1 border-b border-slate-50">
                <dt class="text-slate-500">Lokasi Kerja</dt>
                <dd class="font-medium text-slate-800 text-right">{{ dailyCheckUp.lokasi_pekerjaan || '-' }}</dd>
              </div>
              <div class="flex justify-between py-1">
                <dt class="text-slate-500">Detail Lokasi</dt>
                <dd class="font-medium text-slate-800 text-right">{{ dailyCheckUp.detail_lokasi || '-' }}</dd>
              </div>
            </dl>
          </div>

          <!-- CARD 2: TANYA JAWAB (ANAMNESIS) -->
          <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-xs">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 flex items-center gap-2 pb-3 border-b border-slate-100 mb-4">
              <HeartPulse class="size-4 text-red-600" />
              <span>Tanya Jawab (Anamnesis)</span>
            </h3>

            <div class="space-y-4 text-xs">
              <!-- Q1 -->
              <div class="p-3 rounded-xl border" :class="dailyCheckUp.konsumsi_obat === 'Ya' ? 'bg-amber-50/60 border-amber-200' : 'bg-slate-50/70 border-slate-100'">
                <div class="flex items-center justify-between">
                  <span class="font-semibold text-slate-800">Sedang mengkonsumsi obat-obatan?</span>
                  <span
                    class="px-2 py-0.5 rounded-md font-bold text-[10px]"
                    :class="dailyCheckUp.konsumsi_obat === 'Ya' ? 'bg-amber-600 text-white' : 'bg-emerald-100 text-emerald-800'"
                  >
                    {{ dailyCheckUp.konsumsi_obat }}
                  </span>
                </div>
                <p v-if="dailyCheckUp.detail_konsumsi_obat" class="mt-2 text-slate-600 bg-white/80 p-2 rounded-lg border border-amber-200/60 text-[11px]">
                  <strong>Detail:</strong> {{ dailyCheckUp.detail_konsumsi_obat }}
                </p>
              </div>

              <!-- Q2 -->
              <div class="p-3 rounded-xl border" :class="dailyCheckUp.keluhan_saat_ini === 'Ya' ? 'bg-rose-50/60 border-rose-200' : 'bg-slate-50/70 border-slate-100'">
                <div class="flex items-center justify-between">
                  <span class="font-semibold text-slate-800">Apakah ada keluhan saat ini?</span>
                  <span
                    class="px-2 py-0.5 rounded-md font-bold text-[10px]"
                    :class="dailyCheckUp.keluhan_saat_ini === 'Ya' ? 'bg-rose-600 text-white' : 'bg-emerald-100 text-emerald-800'"
                  >
                    {{ dailyCheckUp.keluhan_saat_ini }}
                  </span>
                </div>
                <p v-if="dailyCheckUp.detail_keluhan" class="mt-2 text-slate-600 bg-white/80 p-2 rounded-lg border border-rose-200/60 text-[11px]">
                  <strong>Keluhan:</strong> {{ dailyCheckUp.detail_keluhan }}
                </p>
              </div>

              <!-- Q3 -->
              <div class="p-3 rounded-xl border" :class="dailyCheckUp.riwayat_penyakit === 'Ya' ? 'bg-amber-50/60 border-amber-200' : 'bg-slate-50/70 border-slate-100'">
                <div class="flex items-center justify-between">
                  <span class="font-semibold text-slate-800">Riwayat Penyakit Bawaan?</span>
                  <span
                    class="px-2 py-0.5 rounded-md font-bold text-[10px]"
                    :class="dailyCheckUp.riwayat_penyakit === 'Ya' ? 'bg-amber-600 text-white' : 'bg-emerald-100 text-emerald-800'"
                  >
                    {{ dailyCheckUp.riwayat_penyakit }}
                  </span>
                </div>
                <p v-if="dailyCheckUp.detail_riwayat_penyakit" class="mt-2 text-slate-600 bg-white/80 p-2 rounded-lg border border-amber-200/60 text-[11px]">
                  <strong>Riwayat:</strong> {{ dailyCheckUp.detail_riwayat_penyakit }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- RIGHT COLUMN: PEMERIKSAAN FISIK & KESIMPULAN (2 COLS) -->
        <div class="lg:col-span-2 space-y-6">
          <!-- CARD 3: PEMERIKSAAN FISIK & VITAL SIGNS -->
          <div class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-xs">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 flex items-center gap-2 pb-3 border-b border-slate-100 mb-5">
              <Stethoscope class="size-4 text-red-600" />
              <span>Hasil Pemeriksaan Fisik & Tanda Vital</span>
            </h3>

            <!-- Vital Signs KPI Highlight Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
              <!-- Tekanan Darah -->
              <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 text-center">
                <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Tekanan Darah</span>
                <span class="text-lg font-black text-slate-900 font-mono">
                  {{ dailyCheckUp.sistolik }}/{{ dailyCheckUp.diastolik }}
                </span>
                <span class="text-[10px] text-slate-500 block">mmHg</span>
              </div>

              <!-- Nadi Istirahat -->
              <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 text-center">
                <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Nadi Istirahat</span>
                <span class="text-lg font-black text-slate-900 font-mono">
                  {{ dailyCheckUp.nadi_istirahat || '-' }}
                </span>
                <span class="text-[10px] text-slate-500 block">bpm ({{ dailyCheckUp.irama_nadi }})</span>
              </div>

              <!-- Saturasi Oksigen SpO2 -->
              <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 text-center">
                <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Saturasi O2 (SpO2)</span>
                <span class="text-lg font-black text-slate-900 font-mono">
                  {{ dailyCheckUp.saturasi_oksigen || '-' }}%
                </span>
                <span class="text-[10px] text-emerald-600 font-semibold block">Oksigenasi</span>
              </div>

              <!-- Temperatur Tubuh -->
              <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 text-center">
                <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Temperatur</span>
                <span class="text-lg font-black text-slate-900 font-mono">
                  {{ dailyCheckUp.temperatur || '-' }}°C
                </span>
                <span class="text-[10px] text-slate-500 block">Suhu Aksila</span>
              </div>
            </div>

            <!-- Detailed Clinical Examination Table -->
            <div class="overflow-x-auto rounded-xl border border-slate-200/80">
              <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px] border-b border-slate-200/80">
                  <tr>
                    <th class="px-4 py-3">Parameter Uji Klinis</th>
                    <th class="px-4 py-3">Hasil Pemeriksaan</th>
                    <th class="px-4 py-3">Keterangan / Standar</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                  <tr>
                    <td class="px-4 py-3 font-semibold text-slate-900">Tingkat Kesadaran</td>
                    <td class="px-4 py-3">
                      <span
                        class="px-2.5 py-1 rounded-md font-bold text-[11px]"
                        :class="dailyCheckUp.kesadaran === 'Compos Mentis' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'"
                      >
                        {{ dailyCheckUp.kesadaran }}
                      </span>
                    </td>
                    <td class="px-4 py-3 text-slate-500">Standar: Compos Mentis (Sadar Penuh)</td>
                  </tr>

                  <tr>
                    <td class="px-4 py-3 font-semibold text-slate-900">Frekuensi Pernapasan</td>
                    <td class="px-4 py-3 font-mono font-bold text-slate-800">
                      {{ dailyCheckUp.frekuensi_napas || '-' }} x / menit
                    </td>
                    <td class="px-4 py-3 text-slate-500">Normal Dewasa: 12 - 20 x/menit</td>
                  </tr>

                  <tr>
                    <td class="px-4 py-3 font-semibold text-slate-900">Irama Nadi</td>
                    <td class="px-4 py-3">
                      <span
                        class="px-2 py-0.5 rounded-md font-semibold text-[11px]"
                        :class="dailyCheckUp.irama_nadi === 'Teratur' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700'"
                      >
                        {{ dailyCheckUp.irama_nadi }}
                      </span>
                    </td>
                    <td class="px-4 py-3 text-slate-500">Teratur (Regular Rhythm)</td>
                  </tr>

                  <tr>
                    <td class="px-4 py-3 font-semibold text-slate-900">Conjungtiva Mata</td>
                    <td class="px-4 py-3">
                      <span
                        class="px-2 py-0.5 rounded-md font-semibold text-[11px]"
                        :class="dailyCheckUp.conjungtiva_mata === 'Tidak Pucat' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700'"
                      >
                        {{ dailyCheckUp.conjungtiva_mata }}
                      </span>
                    </td>
                    <td class="px-4 py-3 text-slate-500">Tidak Pucat (Non-Anemis)</td>
                  </tr>

                  <tr>
                    <td class="px-4 py-3 font-semibold text-slate-900">Pupil Mata</td>
                    <td class="px-4 py-3">
                      <span
                        class="px-2 py-0.5 rounded-md font-semibold text-[11px]"
                        :class="dailyCheckUp.pupil_mata === 'Normal' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700'"
                      >
                        {{ dailyCheckUp.pupil_mata }}
                      </span>
                    </td>
                    <td class="px-4 py-3 text-slate-500">Isokor, Refleks Cahaya Positif</td>
                  </tr>

                  <tr>
                    <td class="px-4 py-3 font-semibold text-slate-900">Uji Keseimbangan (Romberg)</td>
                    <td class="px-4 py-3">
                      <span
                        class="px-2.5 py-1 rounded-md font-bold text-[11px]"
                        :class="dailyCheckUp.keseimbangan === 'Negatif' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'"
                      >
                        {{ dailyCheckUp.keseimbangan }}
                      </span>
                    </td>
                    <td class="px-4 py-3 text-slate-500">Negatif = Keseimbangan Baik</td>
                  </tr>

                  <tr>
                    <td class="px-4 py-3 font-semibold text-slate-900">Penyalahgunaan NAPZA</td>
                    <td class="px-4 py-3">
                      <span
                        class="px-2.5 py-1 rounded-md font-bold text-[11px]"
                        :class="dailyCheckUp.penyalahgunaan_napza === 'Negatif' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'"
                      >
                        {{ dailyCheckUp.penyalahgunaan_napza }}
                      </span>
                    </td>
                    <td class="px-4 py-3 text-slate-500">Negatif = Bebas Pengaruh Narkotika/Obat Terlarang</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- CARD 4: KESIMPULAN & CATATAN DOKTER/HSE -->
          <div class="bg-white rounded-2xl border border-slate-200/90 p-6 shadow-xs">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 flex items-center gap-2 pb-3 border-b border-slate-100 mb-4">
              <AlertTriangle class="size-4 text-red-600" />
              <span>Kesimpulan & Catatan Medis Pemeriksa</span>
            </h3>

            <div class="space-y-4">
              <div class="p-4 rounded-xl border flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3" :class="statusBadge.bg">
                <div class="flex items-center gap-3">
                  <div class="size-9 rounded-xl flex items-center justify-center shrink-0" :class="statusBadge.iconBg">
                    <component :is="statusBadge.icon" class="size-5" />
                  </div>
                  <div>
                    <span class="text-xs font-bold uppercase block tracking-wider">Hasil Akhir Fit-to-Work</span>
                    <p class="text-sm font-black">{{ statusBadge.label }}</p>
                  </div>
                </div>

                <span class="text-xs text-slate-500">
                  Tanggal Input: {{ formatDate(dailyCheckUp.created_at) }}
                </span>
              </div>

              <!-- Catatan Tambahan -->
              <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/70 text-xs">
                <span class="font-bold text-slate-700 block mb-1">Catatan & Anjuran Tim Pemeriksa:</span>
                <p class="text-slate-600 leading-relaxed italic">
                  {{ dailyCheckUp.catatan_pemeriksa || 'Tidak ada catatan khusus. Pekerja dinyatakan dalam kondisi kesehatan standar untuk menjalankan aktivitas operasional sesuai prosedur keselamatan kerja PT Pertamina Gas.' }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
