<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ClassificationBadge from '@/Components/ClassificationBadge.vue';
import { FileHeart, ChevronLeft, Calendar, User, Activity, HeartPulse, Eye, Stethoscope, AlertTriangle, Edit } from '@lucide/vue';

defineProps({
  mcuRecord: Object,
});
</script>

<template>
  <AppLayout :title="`Detail MCU — ${mcuRecord.employee?.nik}`">
    <div class="space-y-6">
      <!-- Top Navigation & Action -->
      <div class="flex items-center justify-between">
        <Link
          :href="route('mcu.index')"
          class="inline-flex items-center gap-1 text-xs font-semibold text-slate-600 hover:text-red-600 transition"
        >
          <ChevronLeft class="size-4" />
          <span>Kembali ke Daftar MCU</span>
        </Link>

        <Link
          v-if="$page.props.auth.user.role !== 'employee'"
          :href="route('mcu.edit', mcuRecord.id)"
          class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-600 text-white hover:bg-red-700 text-xs font-semibold rounded-lg shadow-xs transition"
        >
          <Edit class="size-3.5" />
          <span>Edit MCU Record</span>
        </Link>
      </div>

      <!-- Employee Header Card -->
      <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-3">
            <h2 class="text-xl font-extrabold text-slate-900">{{ mcuRecord.employee?.name }}</h2>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800">
              {{ mcuRecord.employee?.nik }}
            </span>
          </div>
          <p class="text-xs text-slate-500 mt-1">
            {{ mcuRecord.employee?.distrik }} | {{ mcuRecord.employee?.fungsi }} ({{ mcuRecord.employee?.tipe_pekerjaan }}) • Umur: {{ mcuRecord.umur }} Tahun
          </p>
        </div>

        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 text-right">
          <p class="text-[10px] uppercase font-bold text-slate-400">Klasifikasi Kesehatan</p>
          <div class="mt-1">
            <ClassificationBadge :code="mcuRecord.klasifikasi_mcu" />
          </div>
        </div>
      </div>

      <!-- Resume Diagnosis Banner -->
      <div class="bg-gradient-to-r from-slate-900 to-slate-800 rounded-xl p-5 text-white shadow-xs">
        <span class="text-[10px] font-bold uppercase tracking-wider text-red-400">Resume / Kesimpulan MCU</span>
        <h3 class="text-base font-bold mt-1 text-slate-100">{{ mcuRecord.hasil_mcu }}</h3>
        <p class="text-xs text-slate-400 mt-2 flex items-center gap-2">
          <Calendar class="size-3.5" />
          <span>Tanggal Pemeriksaan: {{ mcuRecord.tanggal_mcu }}</span>
        </p>
      </div>

      <!-- Medical Parameters Grid Sections -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Card 1: Antropometri & Tanda Vital -->
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs space-y-3">
          <h4 class="font-bold text-sm text-slate-900 border-b border-slate-200 pb-2 flex items-center gap-2">
            <Activity class="size-4 text-red-600" />
            <span>Antropometri & Vital Sign</span>
          </h4>

          <div class="space-y-2 text-xs">
            <div class="flex justify-between py-1 border-b border-slate-100">
              <span class="text-slate-500">Tinggi Badan (TB):</span>
              <span class="font-bold text-slate-800">{{ mcuRecord.tb ? `${mcuRecord.tb} cm` : '-' }}</span>
            </div>
            <div class="flex justify-between py-1 border-b border-slate-100">
              <span class="text-slate-500">Berat Badan (BB):</span>
              <span class="font-bold text-slate-800">{{ mcuRecord.bb ? `${mcuRecord.bb} kg` : '-' }}</span>
            </div>
            <div class="flex justify-between py-1 border-b border-slate-100">
              <span class="text-slate-500">BMI (Indeks Massa Tubuh):</span>
              <span class="font-bold text-slate-800">{{ mcuRecord.bmi || '-' }}</span>
            </div>
            <div class="flex justify-between py-1 border-b border-slate-100">
              <span class="text-slate-500">Lingkar Perut:</span>
              <span class="font-bold text-slate-800">{{ mcuRecord.lingkar_perut ? `${mcuRecord.lingkar_perut} cm` : '-' }}</span>
            </div>
            <div class="flex justify-between py-1">
              <span class="text-slate-500">Tekanan Darah:</span>
              <span class="font-bold text-slate-800">{{ mcuRecord.sistolik }}/{{ mcuRecord.diastolik }} mmHg</span>
            </div>
          </div>
        </div>

        <!-- Card 2: Profil Laboratorium & Kimia Darah -->
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs space-y-3">
          <h4 class="font-bold text-sm text-slate-900 border-b border-slate-200 pb-2 flex items-center gap-2">
            <HeartPulse class="size-4 text-blue-600" />
            <span>Kimia Darah & Hematologi</span>
          </h4>

          <div class="space-y-2 text-xs">
            <div class="flex justify-between py-1 border-b border-slate-100">
              <span class="text-slate-500">Gula Darah Puasa (GDP):</span>
              <span class="font-bold text-slate-800">{{ mcuRecord.gdp ? `${mcuRecord.gdp} mg/dL` : '-' }}</span>
            </div>
            <div class="flex justify-between py-1 border-b border-slate-100">
              <span class="text-slate-500">HbA1c:</span>
              <span class="font-bold text-slate-800">{{ mcuRecord.hba1c ? `${mcuRecord.hba1c} %` : '-' }}</span>
            </div>
            <div class="flex justify-between py-1 border-b border-slate-100">
              <span class="text-slate-500">Kolesterol Total / LDL:</span>
              <span class="font-bold text-slate-800">{{ mcuRecord.kolesterol || '-' }} / {{ mcuRecord.ldl || '-' }} mg/dL</span>
            </div>
            <div class="flex justify-between py-1 border-b border-slate-100">
              <span class="text-slate-500">HDL / Trigliserida:</span>
              <span class="font-bold text-slate-800">{{ mcuRecord.hdl || '-' }} / {{ mcuRecord.trigliserida || '-' }} mg/dL</span>
            </div>
            <div class="flex justify-between py-1">
              <span class="text-slate-500">Eritrosit / Leukosit / Trombosit:</span>
              <span class="font-bold text-slate-800">{{ mcuRecord.eritrosit }}/{{ mcuRecord.leukosit }}/{{ mcuRecord.trombosit }}</span>
            </div>
          </div>
        </div>

        <!-- Card 3: Pemeriksaan Penunjang Medis -->
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs space-y-3">
          <h4 class="font-bold text-sm text-slate-900 border-b border-slate-200 pb-2 flex items-center gap-2">
            <Stethoscope class="size-4 text-emerald-600" />
            <span>Pemeriksaan Penunjang Medis</span>
          </h4>

          <div class="space-y-2 text-xs">
            <div class="flex justify-between py-1 border-b border-slate-100">
              <span class="text-slate-500">Spirometri:</span>
              <span class="font-bold text-slate-800">{{ mcuRecord.spirometri || '-' }}</span>
            </div>
            <div class="flex justify-between py-1 border-b border-slate-100">
              <span class="text-slate-500">Foto Thorax (Rontgen):</span>
              <span class="font-bold text-slate-800">{{ mcuRecord.foto_thorax || '-' }}</span>
            </div>
            <div class="flex justify-between py-1 border-b border-slate-100">
              <span class="text-slate-500">EKG (Jantung):</span>
              <span class="font-bold text-slate-800">{{ mcuRecord.ekg || '-' }}</span>
            </div>
            <div class="flex justify-between py-1 border-b border-slate-100">
              <span class="text-slate-500">Visus Mata:</span>
              <span class="font-bold text-slate-800">{{ mcuRecord.visus_mata || '-' }}</span>
            </div>
            <div class="flex justify-between py-1">
              <span class="text-slate-500">Audiometri (Pendengaran):</span>
              <span class="font-bold text-slate-800">{{ mcuRecord.audiometri || '-' }}</span>
            </div>
          </div>
        </div>

        <!-- Card 4: Gaya Hidup & Faktor Risiko -->
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs space-y-3">
          <h4 class="font-bold text-sm text-slate-900 border-b border-slate-200 pb-2 flex items-center gap-2">
            <User class="size-4 text-purple-600" />
            <span>Gaya Hidup & Risiko</span>
          </h4>

          <div class="space-y-2 text-xs">
            <div class="flex justify-between py-1 border-b border-slate-100">
              <span class="text-slate-500">Riwayat Merokok:</span>
              <span class="font-bold text-slate-800">{{ mcuRecord.riwayat_merokok || '-' }}</span>
            </div>
            <div class="flex justify-between py-1 border-b border-slate-100">
              <span class="text-slate-500">Riwayat Alkohol:</span>
              <span class="font-bold text-slate-800">{{ mcuRecord.riwayat_alkohol || '-' }}</span>
            </div>
            <div class="flex justify-between py-1 border-b border-slate-100">
              <span class="text-slate-500">Riwayat Diabetes:</span>
              <span class="font-bold text-slate-800">{{ mcuRecord.riwayat_diabetes || '-' }}</span>
            </div>
            <div class="flex justify-between py-1 border-b border-slate-100">
              <span class="text-slate-500">Skor Kardiovaskular Jkt:</span>
              <span class="font-bold text-slate-800">{{ mcuRecord.skor_kardiovaskular_jakarta || '-' }}</span>
            </div>
            <div class="flex justify-between py-1">
              <span class="text-slate-500">Frekuensi Olahraga:</span>
              <span class="font-bold text-slate-800">{{ mcuRecord.frekuensi_olahraga || '-' }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
