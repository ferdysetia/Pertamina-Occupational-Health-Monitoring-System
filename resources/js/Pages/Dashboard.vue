<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ClassificationBadge from '@/Components/ClassificationBadge.vue';
import {
  Users,
  FileHeart,
  AlertTriangle,
  Activity,
  Heart,
  ChevronRight,
  ShieldCheck,
  UserCheck,
  Stethoscope,
  Building2,
  Calendar,
  BarChart3,
  TrendingUp,
  ClipboardCheck,
  CheckCircle2,
  Scale,
  HeartPulse,
  Clock,
  ArrowUpRight,
  Layers,
  Sparkles,
  Info,
  Shield,
  FileText
} from '@lucide/vue';

const props = defineProps({
  role: String,
  employee: Object,
  latestMcu: Object,
  stats: Object,
  recentAlerts: Array,
  recentMcu: Array,
  adminStats: Object,
});

const isEmployee = computed(() => props.role === 'employee');

// Occupational Health (P1 - P7) Classification Corporate Color System
const pColorPalette = {
  P1: { bar: '#10b981', light: 'bg-emerald-50 text-emerald-700 border-emerald-200/90', text: 'text-emerald-600', dot: 'bg-emerald-500' }, // Fit to Work
  P2: { bar: '#0ea5e9', light: 'bg-sky-50 text-sky-700 border-sky-200/90', text: 'text-sky-600', dot: 'bg-sky-500' }, // Fit with Note
  P3: { bar: '#f59e0b', light: 'bg-amber-50 text-amber-700 border-amber-200/90', text: 'text-amber-600', dot: 'bg-amber-500' }, // Periodic Monitoring
  P4: { bar: '#f97316', light: 'bg-orange-50 text-orange-700 border-orange-200/90', text: 'text-orange-600', dot: 'bg-orange-500' }, // Work Restriction
  P5: { bar: '#f43f5e', light: 'bg-rose-50 text-rose-700 border-rose-200/90', text: 'text-rose-600', dot: 'bg-rose-500' }, // Health Issue
  P6: { bar: '#8b5cf6', light: 'bg-purple-50 text-purple-700 border-purple-200/90', text: 'text-purple-600', dot: 'bg-purple-500' }, // Temporary Unfit
  P7: { bar: '#dc2626', light: 'bg-red-50 text-red-700 border-red-200/90', text: 'text-red-600', dot: 'bg-red-600' }, // Permanent Unfit
};

const getPColor = (code) => {
  return pColorPalette[code] || { bar: '#64748b', light: 'bg-slate-50 text-slate-700 border-slate-200', text: 'text-slate-600', dot: 'bg-slate-400' };
};

// Clinical Findings Corporate Color Palette (Rank-based distinct harmonious colors)
const findingPalette = [
  { bar: '#8b5cf6', track: 'bg-purple-100/70', badge: 'bg-purple-50 text-purple-700 border-purple-200', rank: 'bg-purple-600 text-white', text: 'text-purple-600' }, // #1 Violet
  { bar: '#f59e0b', track: 'bg-amber-100/70', badge: 'bg-amber-50 text-amber-700 border-amber-200', rank: 'bg-amber-500 text-white', text: 'text-amber-600' }, // #2 Amber
  { bar: '#f43f5e', track: 'bg-rose-100/70', badge: 'bg-rose-50 text-rose-700 border-rose-200', rank: 'bg-rose-600 text-white', text: 'text-rose-600' }, // #3 Rose
  { bar: '#0284c7', track: 'bg-sky-100/70', badge: 'bg-sky-50 text-sky-700 border-sky-200', rank: 'bg-sky-600 text-white', text: 'text-sky-600' }, // #4 Sky
  { bar: '#10b981', track: 'bg-emerald-100/70', badge: 'bg-emerald-50 text-emerald-700 border-emerald-200', rank: 'bg-emerald-600 text-white', text: 'text-emerald-600' }, // #5 Emerald
];

const getFindingStyle = (rank) => {
  return findingPalette[(rank - 1) % findingPalette.length];
};

// Computed max count for P1-P7 scale calculation
const maxPCount = computed(() => {
  if (!props.stats?.classificationData) return 1;
  const max = Math.max(...props.stats.classificationData.map(item => item.count));
  return max > 0 ? max : 1;
});

// Computed sorted top findings to STRICTLY GUARANTEE descending order
const sortedTopFindings = computed(() => {
  if (!props.stats?.topFindings || !Array.isArray(props.stats.topFindings)) return [];
  return [...props.stats.topFindings]
    .sort((a, b) => b.count - a.count)
    .map((item, index) => ({
      ...item,
      rank: index + 1,
      percentage: props.stats?.totalMcu > 0
        ? Number(((item.count / props.stats.totalMcu) * 100).toFixed(1))
        : (item.percentage ?? 0),
    }));
});

// Computed max for Top 5 findings calculation
const maxFindingCount = computed(() => {
  if (sortedTopFindings.value.length === 0) return 1;
  const top = sortedTopFindings.value[0]?.count || 0;
  return top > 0 ? top : 1;
});

// Tanggal hari ini dalam format Bahasa Indonesia
const todayFormatted = computed(() => {
  return new Intl.DateTimeFormat('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  }).format(new Date());
});

// Label peran pengguna
const roleLabel = computed(() => {
  switch (props.role) {
    case 'admin':
      return 'Administrator Sistem';
    case 'manager':
      return 'Manager HSE';
    case 'hse':
      return 'HSE Officer';
    case 'doctor':
      return 'Dokter Perusahaan';
    case 'employee':
      return 'Karyawan';
    default:
      return 'Staff HSE';
  }
});
</script>

<template>
  <AppLayout title="Dashboard Information Center">
    <!-- ============================================================= -->
    <!-- SECTION 1: EMPLOYEE DASHBOARD (PERSONAL HEALTH OVERVIEW)      -->
    <!-- ============================================================= -->
    <div v-if="isEmployee" class="space-y-5 sm:space-y-6">
      <!-- Clean Corporate Welcome Card -->
      <div class="bg-white rounded-xl border border-slate-200/90 p-5 sm:p-6 shadow-xs">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-5">
          <div class="space-y-1.5">
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-blue-50 border border-blue-200/80 text-blue-700 text-[11px] font-semibold">
              <HeartPulse class="size-3.5 text-blue-600" />
              <span>Portal Kesehatan Kerja Karyawan</span>
            </div>
            <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
              Selamat Datang, {{ employee?.name || $page.props.auth.user.name }}
            </h1>
            <p class="text-xs text-slate-500 font-normal">
              NIK: <span class="font-semibold text-slate-700">{{ employee?.nik || '-' }}</span> · 
              Fungsi: <span class="font-semibold text-slate-700">{{ employee?.fungsi || 'Karyawan' }}</span> · 
              Distrik: <span class="font-semibold text-slate-700">{{ employee?.distrik || 'PT Pertamina Gas' }}</span>
            </p>
          </div>

          <div v-if="latestMcu" class="bg-slate-50 rounded-lg p-3.5 border border-slate-200 shrink-0 text-left md:text-right">
            <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 block">Status MCU Terakhir</span>
            <div class="mt-1.5 flex items-center md:justify-end gap-2">
              <ClassificationBadge :code="latestMcu.klasifikasi_mcu" />
            </div>
            <span class="text-[11px] text-slate-500 font-normal mt-1 block">
              Pemeriksaan: {{ latestMcu.tanggal_mcu || '-' }}
            </span>
          </div>
        </div>
      </div>

      <!-- Quick Personal Vitals Metric Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        <!-- BMI -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 sm:p-5 shadow-xs hover:border-slate-300 transition-all">
          <div class="flex items-center justify-between">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Indeks Massa Tubuh (BMI)</span>
            <div class="p-2 rounded-lg bg-blue-50 text-blue-600 border border-blue-100/80">
              <Scale class="size-4" />
            </div>
          </div>
          <div class="mt-3">
            <span class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight tabular-nums">{{ latestMcu?.bmi ?? '-' }}</span>
          </div>
          <p class="mt-1.5 text-[11px] text-slate-500 font-normal">Nilai proporsi berat dan tinggi badan</p>
        </div>

        <!-- Tekanan Darah -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 sm:p-5 shadow-xs hover:border-slate-300 transition-all">
          <div class="flex items-center justify-between">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Tekanan Darah</span>
            <div class="p-2 rounded-lg bg-rose-50 text-rose-600 border border-rose-100/80">
              <Heart class="size-4" />
            </div>
          </div>
          <div class="mt-3">
            <span class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight tabular-nums">
              {{ latestMcu ? `${latestMcu.sistolik}/${latestMcu.diastolik}` : '-' }}
            </span>
            <span v-if="latestMcu" class="text-xs font-medium text-slate-400 ml-1">mmHg</span>
          </div>
          <p class="mt-1.5 text-[11px] text-slate-500 font-normal">Kondisi kardiovaskular tensi darah</p>
        </div>

        <!-- Gula Darah Puasa -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 sm:p-5 shadow-xs hover:border-slate-300 transition-all">
          <div class="flex items-center justify-between">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Gula Darah Puasa</span>
            <div class="p-2 rounded-lg bg-amber-50 text-amber-600 border border-amber-100/80">
              <Activity class="size-4" />
            </div>
          </div>
          <div class="mt-3">
            <span class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight tabular-nums">{{ latestMcu?.gdp ?? '-' }}</span>
            <span v-if="latestMcu?.gdp" class="text-xs font-medium text-slate-400 ml-1">mg/dL</span>
          </div>
          <p class="mt-1.5 text-[11px] text-slate-500 font-normal">Kadar glukosa darah saat berpuasa</p>
        </div>

        <!-- Kolesterol Total -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 sm:p-5 shadow-xs hover:border-slate-300 transition-all">
          <div class="flex items-center justify-between">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Kolesterol Total</span>
            <div class="p-2 rounded-lg bg-violet-50 text-violet-600 border border-violet-100/80">
              <HeartPulse class="size-4" />
            </div>
          </div>
          <div class="mt-3">
            <span class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight tabular-nums">{{ latestMcu?.kolesterol ?? '-' }}</span>
            <span v-if="latestMcu?.kolesterol" class="text-xs font-medium text-slate-400 ml-1">mg/dL</span>
          </div>
          <p class="mt-1.5 text-[11px] text-slate-500 font-normal">Profil lipid kumulatif darah</p>
        </div>
      </div>

      <!-- Clinical Resume Card -->
      <div class="bg-white rounded-xl border border-slate-200/90 p-5 sm:p-6 shadow-xs">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
          <div class="flex items-center gap-2.5">
            <div class="p-2 rounded-lg bg-blue-50 text-blue-600 border border-blue-100/80">
              <FileText class="size-4.5" />
            </div>
            <div>
              <h2 class="text-sm sm:text-[15px] font-bold text-slate-900 tracking-tight">
                Resume Hasil Pemeriksaan Kesehatan (MCU)
              </h2>
              <p class="text-xs text-slate-400 font-normal mt-0.5">
                Pemeriksaan berkala yang dilaksanakan oleh Tim Medis HSE Pertamina Gas
              </p>
            </div>
          </div>

          <span v-if="latestMcu" class="text-[11px] font-semibold px-2.5 py-1 rounded-md bg-slate-100 text-slate-600 border border-slate-200/80">
            Tanggal: {{ latestMcu.tanggal_mcu || '-' }}
          </span>
        </div>

        <div v-if="latestMcu" class="mt-5 space-y-4">
          <div class="p-4 rounded-lg bg-slate-50 border border-slate-200/80">
            <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 block">Kesimpulan & Anjuran Dokter:</span>
            <p class="text-xs sm:text-[13px] font-medium text-slate-700 mt-1.5 leading-relaxed">
              {{ latestMcu.hasil_mcu || 'Kondisi kesehatan umum dalam batas normal, tidak ada kelainan bermakna.' }}
            </p>
          </div>

          <!-- 4 Specific Exam Results Grid -->
          <div class="grid grid-cols-2 md:grid-cols-4 gap-3.5">
            <div class="p-3.5 rounded-lg bg-white border border-slate-200/80">
              <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block">Spirometri (Paru)</span>
              <p class="text-xs sm:text-sm font-semibold text-slate-800 mt-1">{{ latestMcu.spirometri || 'Normal' }}</p>
            </div>
            <div class="p-3.5 rounded-lg bg-white border border-slate-200/80">
              <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block">Visus Mata</span>
              <p class="text-xs sm:text-sm font-semibold text-slate-800 mt-1">{{ latestMcu.visus_mata || 'Normal' }}</p>
            </div>
            <div class="p-3.5 rounded-lg bg-white border border-slate-200/80">
              <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block">EKG (Jantung)</span>
              <p class="text-xs sm:text-sm font-semibold text-slate-800 mt-1">{{ latestMcu.ekg || 'Normal Sinus Rhythm' }}</p>
            </div>
            <div class="p-3.5 rounded-lg bg-white border border-slate-200/80">
              <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block">Foto Thorax</span>
              <p class="text-xs sm:text-sm font-semibold text-slate-800 mt-1">{{ latestMcu.foto_thorax || 'Cor & Pulmo Normal' }}</p>
            </div>
          </div>
        </div>

        <div v-else class="text-center py-10 text-slate-400 text-xs">
          Belum ada data riwayat MCU yang terdaftar pada sistem.
        </div>
      </div>
    </div>

    <!-- ============================================================= -->
    <!-- SECTION 2: MANAGEMENT / HSE / DOCTOR / ADMIN DASHBOARD        -->
    <!-- ============================================================= -->
    <div v-else class="space-y-5 sm:space-y-6">
      <!-- Executive Header Banner: Clean White Corporate Header -->
      <div class="bg-white rounded-xl border border-slate-200/90 p-5 sm:p-6 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div class="space-y-1">
            <div class="flex items-center gap-2">
              <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
                Pusat Informasi Kesehatan Kerja (HSE Dashboard)
              </h1>
              <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200/80 uppercase">
                {{ roleLabel }}
              </span>
            </div>
            <p class="text-xs text-slate-500 font-normal">
              PT Pertamina Gas · Pemantauan Kesehatan Karyawan Terpadu · {{ todayFormatted }}
            </p>
          </div>

          <div class="flex items-center gap-2.5 shrink-0">
            <Link
              v-if="['admin', 'manager', 'hse', 'doctor'].includes(role)"
              :href="route('mcu.create')"
              class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-blue-600 text-white text-xs font-semibold hover:bg-blue-700 transition"
            >
              <FileHeart class="size-4" />
              <span>Input MCU Baru</span>
            </Link>
            <Link
              :href="route('daily-check-ups.create')"
              class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition"
            >
              <ClipboardCheck class="size-4 text-slate-500" />
              <span>Input DCU Harian</span>
            </Link>
          </div>
        </div>
      </div>

      <!-- Admin Role User Breakdown Bar (Admin Only) -->
      <div v-if="adminStats" class="grid grid-cols-2 sm:grid-cols-5 gap-3.5">
        <div class="bg-white rounded-xl border border-slate-200/90 p-3.5 shadow-xs">
          <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 block">Total User Sistem</span>
          <p class="text-lg sm:text-xl font-bold text-slate-900 mt-1 tabular-nums">{{ adminStats.total_users }}</p>
          <span class="text-[10px] text-slate-400 mt-0.5 block">Akun terdaftar</span>
        </div>
        <div class="bg-white rounded-xl border border-slate-200/90 p-3.5 shadow-xs">
          <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 block">Manager HSE</span>
          <p class="text-lg sm:text-xl font-bold text-slate-900 mt-1 tabular-nums">{{ adminStats.total_managers || 1 }}</p>
          <span class="text-[10px] text-slate-400 mt-0.5 block">Hak akses pimpinan</span>
        </div>
        <div class="bg-white rounded-xl border border-slate-200/90 p-3.5 shadow-xs">
          <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 block">Petugas HSE</span>
          <p class="text-lg sm:text-xl font-bold text-slate-900 mt-1 tabular-nums">{{ adminStats.total_hse }}</p>
          <span class="text-[10px] text-slate-400 mt-0.5 block">Officer pelaksana</span>
        </div>
        <div class="bg-white rounded-xl border border-slate-200/90 p-3.5 shadow-xs">
          <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 block">Dokter Perusahaan</span>
          <p class="text-lg sm:text-xl font-bold text-slate-900 mt-1 tabular-nums">{{ adminStats.total_doctors }}</p>
          <span class="text-[10px] text-slate-400 mt-0.5 block">Tim medis klinik</span>
        </div>
        <div class="bg-white rounded-xl border border-slate-200/90 p-3.5 shadow-xs">
          <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 block">Akun Pekerja</span>
          <p class="text-lg sm:text-xl font-bold text-slate-900 mt-1 tabular-nums">{{ adminStats.total_employee_users }}</p>
          <span class="text-[10px] text-slate-400 mt-0.5 block">Akses mandiri karyawan</span>
        </div>
      </div>

      <!-- Primary KPI Metric Cards (Standard 4-Grid Corporate Cards) -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        <!-- Metric 1: Total Karyawan -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 sm:p-5 shadow-xs hover:border-slate-300 transition-all">
          <div class="flex items-center justify-between">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Total Karyawan</span>
            <div class="p-2 rounded-lg bg-blue-50 text-blue-600 border border-blue-100/80">
              <Users class="size-4" />
            </div>
          </div>
          <div class="mt-3">
            <span class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight tabular-nums">{{ stats.totalEmployees }}</span>
          </div>
          <p class="mt-1.5 text-[11px] text-slate-500 font-normal">Terdata di database PT Pertamina Gas</p>
        </div>

        <!-- Metric 2: Record MCU -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 sm:p-5 shadow-xs hover:border-slate-300 transition-all">
          <div class="flex items-center justify-between">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Total Record MCU</span>
            <div class="p-2 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100/80">
              <FileHeart class="size-4" />
            </div>
          </div>
          <div class="mt-3">
            <span class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight tabular-nums">{{ stats.totalMcu }}</span>
          </div>
          <p class="mt-1.5 text-[11px] text-slate-500 font-normal">Pemeriksaan kesehatan berkala</p>
        </div>

        <!-- Metric 3: DCU Hari Ini -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 sm:p-5 shadow-xs hover:border-slate-300 transition-all">
          <div class="flex items-center justify-between">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">DCU Hari Ini</span>
            <div class="p-2 rounded-lg bg-violet-50 text-violet-600 border border-violet-100/80">
              <ClipboardCheck class="size-4" />
            </div>
          </div>
          <div class="mt-3">
            <span class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight tabular-nums">{{ stats.dcuStats?.todayTotal || 0 }}</span>
          </div>
          <p class="mt-1.5 text-[11px] text-slate-500 font-normal">
            Fit: <span class="font-semibold text-emerald-600">{{ stats.dcuStats?.todayFit || 0 }}</span> · 
            Unfit: <span class="font-semibold text-rose-600">{{ stats.dcuStats?.todayUnfit || 0 }}</span>
          </p>
        </div>

        <!-- Metric 4: Health Alerts Terbuka -->
        <div class="bg-white rounded-xl border border-slate-200/90 p-4 sm:p-5 shadow-xs hover:border-slate-300 transition-all">
          <div class="flex items-center justify-between">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Health Alerts Terbuka</span>
            <div class="p-2 rounded-lg bg-amber-50 text-amber-600 border border-amber-100/80">
              <AlertTriangle class="size-4" />
            </div>
          </div>
          <div class="mt-3">
            <span class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight tabular-nums">{{ stats.openAlerts || 0 }}</span>
          </div>
          <p class="mt-1.5 text-[11px] text-slate-500 font-normal">Kondisi butuh perhatian & tindak lanjut</p>
        </div>
      </div>

      <!-- ============================================================= -->
      <!-- CENTRAL ANALYTICS: 2 BARCHART VISUALIZATIONS                  -->
      <!-- ============================================================= -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6 items-stretch">
        <!-- =========================================================== -->
        <!-- CHART 1: DISTRIBUSI KLASIFIKASI KESEHATAN MCU (P1 - P7)     -->
        <!-- =========================================================== -->
        <div class="lg:col-span-7 bg-white rounded-xl border border-slate-200/90 shadow-xs flex flex-col overflow-hidden">
          <!-- Card Header -->
          <div class="p-5 pb-0">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 border-b border-slate-100">
              <div class="flex items-center gap-2.5">
                <div class="p-2 rounded-lg bg-blue-50 text-blue-600 border border-blue-100/80">
                  <BarChart3 class="size-4.5" />
                </div>
                <div>
                  <h2 class="text-sm sm:text-[15px] font-bold text-slate-900 tracking-tight">
                    Distribusi Status Kesehatan MCU (P1 – P7)
                  </h2>
                  <p class="text-xs text-slate-400 font-normal mt-0.5">
                    Hasil klasifikasi kelaikan kerja pekerja berdasarkan standar HSE
                  </p>
                </div>
              </div>

              <!-- Total Badge -->
              <div class="flex items-center gap-2 self-start sm:self-auto">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 text-slate-600 text-[11px] font-semibold border border-slate-200/80">
                  <FileHeart class="size-3.5 text-slate-500" />
                  <span>{{ stats.totalMcu }} Total MCU</span>
                </span>
              </div>
            </div>

            <!-- Triage Summary Strip: 3 Clean Corporate Indicator Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-4">
              <!-- Laik Kerja -->
              <div class="p-3 sm:p-3.5 rounded-lg bg-emerald-50/50 border border-emerald-200/80 flex items-center justify-between">
                <div class="min-w-0">
                  <div class="flex items-center gap-1.5">
                    <span class="size-2 rounded-full bg-emerald-500 shrink-0"></span>
                    <span class="text-xs font-semibold text-emerald-900 truncate">Laik Kerja (P1-P2)</span>
                  </div>
                  <p class="text-base font-bold text-emerald-700 mt-1 tabular-nums">
                    {{ stats.classificationSummary?.fit?.count ?? 0 }}
                    <span class="text-xs font-normal text-emerald-600/80 ml-1">({{ stats.classificationSummary?.fit?.percentage ?? 0 }}%)</span>
                  </p>
                </div>
              </div>

              <!-- Pemantauan -->
              <div class="p-3 sm:p-3.5 rounded-lg bg-amber-50/50 border border-amber-200/80 flex items-center justify-between">
                <div class="min-w-0">
                  <div class="flex items-center gap-1.5">
                    <span class="size-2 rounded-full bg-amber-500 shrink-0"></span>
                    <span class="text-xs font-semibold text-amber-900 truncate">Pantau (P3-P4)</span>
                  </div>
                  <p class="text-base font-bold text-amber-700 mt-1 tabular-nums">
                    {{ stats.classificationSummary?.monitoring?.count ?? 0 }}
                    <span class="text-xs font-normal text-amber-600/80 ml-1">({{ stats.classificationSummary?.monitoring?.percentage ?? 0 }}%)</span>
                  </p>
                </div>
              </div>

              <!-- Isu Medis -->
              <div class="p-3 sm:p-3.5 rounded-lg bg-rose-50/50 border border-rose-200/80 flex items-center justify-between">
                <div class="min-w-0">
                  <div class="flex items-center gap-1.5">
                    <span class="size-2 rounded-full bg-rose-500 shrink-0"></span>
                    <span class="text-xs font-semibold text-rose-900 truncate">Isu Medis (P5-P7)</span>
                  </div>
                  <p class="text-base font-bold text-rose-700 mt-1 tabular-nums">
                    {{ stats.classificationSummary?.unfit?.count ?? 0 }}
                    <span class="text-xs font-normal text-rose-600/80 ml-1">({{ stats.classificationSummary?.unfit?.percentage ?? 0 }}%)</span>
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Barchart Canvas: P1-P7 Occupational Health Standard Color Scheme -->
          <div class="flex-1 px-5 py-5 sm:py-6">
            <div class="relative h-60 sm:h-64 w-full flex flex-col justify-end">
              <!-- Y-Axis Reference Guide Lines -->
              <div class="absolute inset-0 flex flex-col justify-between pointer-events-none pb-12">
                <div class="w-full flex items-center text-[10px] text-slate-400 font-mono">
                  <span class="w-7 text-right pr-2 shrink-0">{{ maxPCount }}</span>
                  <span class="flex-1 border-b border-dashed border-slate-200"></span>
                </div>
                <div class="w-full flex items-center text-[10px] text-slate-400 font-mono">
                  <span class="w-7 text-right pr-2 shrink-0">{{ Math.round(maxPCount * 0.75) }}</span>
                  <span class="flex-1 border-b border-dashed border-slate-200"></span>
                </div>
                <div class="w-full flex items-center text-[10px] text-slate-400 font-mono">
                  <span class="w-7 text-right pr-2 shrink-0">{{ Math.round(maxPCount * 0.5) }}</span>
                  <span class="flex-1 border-b border-dashed border-slate-200"></span>
                </div>
                <div class="w-full flex items-center text-[10px] text-slate-400 font-mono">
                  <span class="w-7 text-right pr-2 shrink-0">{{ Math.round(maxPCount * 0.25) }}</span>
                  <span class="flex-1 border-b border-dashed border-slate-200"></span>
                </div>
                <div class="w-full flex items-center text-[10px] text-slate-400 font-mono">
                  <span class="w-7 text-right pr-2 shrink-0">0</span>
                  <span class="flex-1 border-b border-slate-200"></span>
                </div>
              </div>

              <!-- Bars: Distinct Standard Occupational Health Colors -->
              <div class="relative z-10 flex items-end justify-around gap-2 sm:gap-3 pl-8 h-full">
                <div
                  v-for="item in stats.classificationData"
                  :key="item.code"
                  class="flex-1 flex flex-col items-center justify-end h-full group cursor-default max-w-14"
                >
                  <!-- Number on top of bar -->
                  <div class="mb-1.5 text-center transition-transform duration-200 group-hover:-translate-y-0.5">
                    <span
                      class="text-xs font-bold block tabular-nums"
                      :style="{ color: item.barColor || getPColor(item.code).bar }"
                    >
                      {{ item.count }}
                    </span>
                    <span class="text-[10px] font-medium text-slate-400 block tabular-nums">
                      {{ item.percentage }}%
                    </span>
                  </div>

                  <!-- Color Bar with Soft Rounded Top -->
                  <div
                    class="w-full rounded-t transition-all duration-300 ease-out hover:opacity-90 shadow-2xs group-hover:scale-y-[1.02] origin-bottom"
                    :style="{
                      height: `${Math.max((item.count / maxPCount) * 100, item.count > 0 ? 8 : 3)}%`,
                      backgroundColor: item.barColor || getPColor(item.code).bar,
                      minHeight: '6px'
                    }"
                    :title="`${item.code} (${item.name}): ${item.count} orang`"
                  ></div>

                  <!-- X-Axis Label -->
                  <div class="mt-2.5 text-center">
                    <span
                      class="inline-block px-1.5 py-0.5 text-[10px] font-bold rounded uppercase border transition-all"
                      :class="item.lightColor || getPColor(item.code).light"
                    >
                      {{ item.code }}
                    </span>
                    <span class="block text-[11px] font-medium text-slate-500 mt-1 leading-tight truncate max-w-[60px] sm:max-w-none">
                      {{ item.short || item.name }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Bottom Legend: Panduan Kategori yang Jelas & Terstruktur -->
          <div class="px-5 pb-5 pt-3.5 border-t border-slate-100 bg-slate-50/60">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2.5">
              Keterangan Standar Klasifikasi MCU (P1 – P7):
            </span>
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-2">
              <div
                v-for="item in stats.classificationData"
                :key="item.code"
                class="p-2 rounded-lg border bg-white text-center hover:border-slate-300 hover:shadow-2xs transition"
                :class="item.count > 0 ? 'border-slate-200/90' : 'border-slate-100 opacity-60'"
              >
                <div class="flex items-center justify-center gap-1.5">
                  <span
                    class="size-1.5 rounded-full shrink-0"
                    :style="{ backgroundColor: item.barColor || getPColor(item.code).bar }"
                  ></span>
                  <span class="text-xs font-bold text-slate-800">{{ item.code }}</span>
                </div>
                <p
                  class="text-xs font-bold mt-0.5 tabular-nums"
                  :style="{ color: item.barColor || getPColor(item.code).bar }"
                >
                  {{ item.count }} Orang
                </p>
                <p class="text-[10px] text-slate-400 font-normal truncate mt-0.5" :title="item.name">{{ item.name }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- =========================================================== -->
        <!-- CHART 2: TOP 5 TEMUAN KASUS MCU                             -->
        <!-- =========================================================== -->
        <div class="lg:col-span-5 bg-white rounded-xl border border-slate-200/90 shadow-xs flex flex-col overflow-hidden">
          <!-- Card Header -->
          <div class="p-5 pb-3">
            <div class="flex items-center justify-between pb-3.5 border-b border-slate-100">
              <div class="flex items-center gap-2.5">
                <div class="p-2 rounded-lg bg-violet-50 text-violet-600 border border-violet-100/80">
                  <TrendingUp class="size-4.5" />
                </div>
                <div>
                  <h2 class="text-sm sm:text-[15px] font-bold text-slate-900 tracking-tight">
                    Top 5 Temuan Kasus MCU
                  </h2>
                  <p class="text-xs text-slate-400 font-normal mt-0.5">
                    Diagnosa klinis dengan jumlah kasus terbanyak
                  </p>
                </div>
              </div>

              <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md bg-violet-50 text-violet-700 border border-violet-100">
                Top 1 – 5
              </span>
            </div>
          </div>

          <!-- Ranked List: Distinct Harmonious Corporate Colors -->
          <div class="px-5 flex-1 space-y-3">
            <div
              v-for="finding in sortedTopFindings"
              :key="finding.name"
              class="p-3 sm:p-3.5 rounded-lg bg-slate-50/70 border border-slate-200/80 hover:border-slate-300 hover:bg-slate-50 transition-all duration-200"
            >
              <!-- Baris 1: Rank, Nama Temuan & Jumlah Kasus -->
              <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5 min-w-0">
                  <span
                    class="size-5 sm:size-5.5 rounded-md flex items-center justify-center text-[10px] font-bold shrink-0 shadow-2xs"
                    :class="getFindingStyle(finding.rank).rank"
                  >
                    #{{ finding.rank }}
                  </span>

                  <span class="text-xs sm:text-[13px] font-semibold text-slate-800 truncate">
                    {{ finding.name }}
                  </span>
                </div>

                <div class="text-right shrink-0">
                  <span
                    class="text-xs sm:text-[13px] font-bold tabular-nums"
                    :style="{ color: finding.barColor || getFindingStyle(finding.rank).bar }"
                  >
                    {{ finding.count }}
                  </span>
                  <span class="text-[11px] text-slate-400 font-normal ml-1">({{ finding.percentage }}%)</span>
                </div>
              </div>

              <!-- Baris 2: Batang Horizontal dengan Warna Temuan Khas -->
              <div
                class="w-full h-2 rounded-full overflow-hidden mt-2.5"
                :class="getFindingStyle(finding.rank).track || 'bg-slate-200/70'"
              >
                <div
                  class="h-full rounded-full transition-all duration-500 ease-out"
                  :style="{
                    width: `${Math.max((finding.count / maxFindingCount) * 100, 8)}%`,
                    backgroundColor: finding.barColor || getFindingStyle(finding.rank).bar
                  }"
                ></div>
              </div>

              <!-- Baris 3: Rekomendasi & Kategori -->
              <div class="flex items-center justify-between text-xs pt-2 gap-2">
                <span class="text-[11px] text-slate-500 font-normal truncate">💡 {{ finding.recommendation }}</span>
                <span
                  class="text-[9px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded border shrink-0 transition-colors"
                  :class="finding.badge || getFindingStyle(finding.rank).badge"
                >
                  {{ finding.category }}
                </span>
              </div>
            </div>
          </div>

          <!-- Bottom Action Link -->
          <div class="p-5 pt-3.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500 mt-auto bg-slate-50/40">
            <span class="font-normal">Rujukan: Rekapitulasi Data Medis MCU</span>
            <Link :href="route('mcu.index')" class="font-semibold text-violet-600 hover:text-violet-700 flex items-center gap-1 transition">
              <span>Buka Detail Rekap MCU</span>
              <ChevronRight class="size-3.5" />
            </Link>
          </div>
        </div>
      </div>

      <!-- ============================================================= -->
      <!-- SECTION 3: RECENT HEALTH ALERTS & RECENT MCU ENTRIES          -->
      <!-- ============================================================= -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 sm:gap-6">
        <!-- Column 1: Health Alerts Terbaru (P3 - P7) -->
        <div class="bg-white rounded-xl border border-slate-200/90 shadow-xs flex flex-col overflow-hidden">
          <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
              <div class="p-2 rounded-lg bg-amber-50 text-amber-600 border border-amber-100/80">
                <AlertTriangle class="size-4.5" />
              </div>
              <div>
                <h2 class="text-sm sm:text-[15px] font-bold text-slate-900 tracking-tight">Health Alerts Terbaru</h2>
                <p class="text-xs text-slate-400 font-normal mt-0.5">
                  Karyawan status P3 sampai P7 yang butuh pantauan
                </p>
              </div>
            </div>
            <Link :href="route('health-alerts.index')" class="text-xs text-blue-600 hover:text-blue-700 font-semibold flex items-center gap-1 transition">
              <span>Lihat Semua</span>
              <ChevronRight class="size-3.5" />
            </Link>
          </div>

          <div class="p-5 flex-1 space-y-2.5">
            <div
              v-for="alert in recentAlerts"
              :key="alert.id"
              class="p-3.5 rounded-lg border border-slate-200/80 bg-white hover:bg-slate-50/80 transition-all flex items-start justify-between gap-3"
            >
              <div class="space-y-1 min-w-0">
                <div class="flex items-center gap-2">
                  <span class="font-semibold text-slate-800 text-xs sm:text-[13px] truncate">{{ alert.employee?.name }}</span>
                  <span class="text-[11px] font-mono text-slate-400 shrink-0">({{ alert.employee?.nik }})</span>
                </div>
                <p class="text-xs text-slate-600 line-clamp-1 font-normal">{{ alert.reason }}</p>
                <div class="flex items-center gap-1.5 text-[11px] text-slate-400 pt-0.5 font-normal">
                  <span>{{ alert.employee?.distrik }}</span>
                  <span>·</span>
                  <span>{{ alert.category }}</span>
                </div>
              </div>
              <div class="shrink-0 flex flex-col items-end gap-1.5">
                <ClassificationBadge :code="alert.severity" :show-label="false" />
                <span
                  class="text-[10px] px-2 py-0.5 rounded font-semibold border"
                  :class="alert.status === 'Open' ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200'"
                >
                  {{ alert.status }}
                </span>
              </div>
            </div>

            <div v-if="!recentAlerts || recentAlerts.length === 0" class="text-center py-10 text-slate-400 text-xs">
              <AlertTriangle class="size-6 text-slate-300 mx-auto mb-1.5" />
              Tidak ada health alert aktif saat ini.
            </div>
          </div>
        </div>

        <!-- Column 2: Record MCU Terbaru -->
        <div class="bg-white rounded-xl border border-slate-200/90 shadow-xs flex flex-col overflow-hidden">
          <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
              <div class="p-2 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100/80">
                <FileHeart class="size-4.5" />
              </div>
              <div>
                <h2 class="text-sm sm:text-[15px] font-bold text-slate-900 tracking-tight">Record MCU Terbaru</h2>
                <p class="text-xs text-slate-400 font-normal mt-0.5">
                  Hasil pemeriksaan kesehatan berkala terkini
                </p>
              </div>
            </div>
            <Link :href="route('mcu.index')" class="text-xs text-blue-600 hover:text-blue-700 font-semibold flex items-center gap-1 transition">
              <span>Lihat Semua</span>
              <ChevronRight class="size-3.5" />
            </Link>
          </div>

          <div class="p-5 flex-1 space-y-2.5">
            <div
              v-for="mcu in recentMcu"
              :key="mcu.id"
              class="p-3.5 rounded-lg border border-slate-200/80 bg-white hover:bg-slate-50/80 transition-all flex items-center justify-between gap-3"
            >
              <div class="min-w-0">
                <p class="font-semibold text-slate-800 text-xs sm:text-[13px] truncate">{{ mcu.employee?.name }}</p>
                <p class="text-[11px] text-slate-400 mt-0.5 font-normal">
                  NIK: {{ mcu.employee?.nik }} · {{ mcu.employee?.distrik }} · {{ mcu.tanggal_mcu }}
                </p>
              </div>

              <div class="flex items-center gap-2.5 shrink-0">
                <ClassificationBadge :code="mcu.klasifikasi_mcu" :show-label="false" />
                <Link
                  :href="route('mcu.show', mcu.id)"
                  class="p-1.5 rounded-lg border border-slate-200 text-slate-400 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition"
                  title="Lihat Detail MCU"
                >
                  <ChevronRight class="size-3.5" />
                </Link>
              </div>
            </div>

            <div v-if="!recentMcu || recentMcu.length === 0" class="text-center py-10 text-slate-400 text-xs">
              <FileHeart class="size-6 text-slate-300 mx-auto mb-1.5" />
              Belum ada record MCU yang terdaftar.
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
