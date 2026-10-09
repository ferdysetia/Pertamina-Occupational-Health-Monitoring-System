<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ClassificationBadge from '@/Components/ClassificationBadge.vue';
import { User, FileHeart, AlertTriangle, Building, Shield, ChevronLeft, Calendar, FileText, ClipboardCheck, Stethoscope } from '@lucide/vue';

const props = defineProps({
  employee: Object,
});

const latestMcu = computed(() => props.employee?.mcu_records?.[0]);
</script>

<template>
  <AppLayout :title="`Detail Karyawan — ${employee.nik}`">
    <div class="space-y-6">
      <!-- Back Link & Header -->
      <div class="flex items-center justify-between">
        <Link
          v-if="$page.props.auth.user.role !== 'employee'"
          :href="route('employees.index')"
          class="inline-flex items-center gap-1 text-xs font-semibold text-slate-600 hover:text-red-600 transition"
        >
          <ChevronLeft class="size-4" />
          <span>Kembali ke Daftar Karyawan</span>
        </Link>
        <span class="text-xs text-slate-400">Profil Kesehatan Karyawan</span>
      </div>

      <!-- Employee Info Card -->
      <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
          <div class="size-16 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center font-bold text-2xl border border-red-100 shrink-0">
            {{ employee.name.charAt(0) }}
          </div>
          <div>
            <div class="flex items-center gap-2">
              <h2 class="text-xl font-extrabold text-slate-900">{{ employee.name }}</h2>
              <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800">
                {{ employee.nik }}
              </span>
            </div>
            <p class="text-xs text-slate-500 mt-1 flex items-center gap-3 flex-wrap">
              <span><Building class="size-3.5 inline text-slate-400" /> {{ employee.distrik }}</span>
              <span>•</span>
              <span>{{ employee.fungsi }}</span>
              <span>•</span>
              <span>Tipe: {{ employee.tipe_pekerjaan }}</span>
            </p>
          </div>
        </div>

        <div v-if="latestMcu" class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 text-right shrink-0">
          <p class="text-[10px] uppercase font-bold text-slate-400">Status Kesehatan Terakhir</p>
          <div class="mt-1">
            <ClassificationBadge :code="latestMcu.klasifikasi_mcu" />
          </div>
        </div>
      </div>

      <!-- MCU Records Timeline -->
      <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-xs space-y-4">
        <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
          <FileHeart class="size-5 text-red-600" />
          <span>Histori Pemeriksaan Kesehatan (MCU)</span>
        </h3>

        <div v-if="employee.mcu_records && employee.mcu_records.length > 0" class="space-y-4">
          <div
            v-for="mcu in employee.mcu_records"
            :key="mcu.id"
            class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-3"
          >
            <div class="flex items-center justify-between border-b border-slate-200 pb-3">
              <div class="flex items-center gap-2">
                <Calendar class="size-4 text-slate-400" />
                <span class="text-xs font-bold text-slate-800">MCU Tanggal: {{ mcu.tanggal_mcu }}</span>
                <span class="text-xs text-slate-500">(Umur: {{ mcu.umur }} th)</span>
              </div>
              <ClassificationBadge :code="mcu.klasifikasi_mcu" />
            </div>

            <!-- Parameters Overview Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-3 text-xs pt-1">
              <div>
                <span class="text-slate-400 block text-[10px]">TB / BB / BMI:</span>
                <span class="font-semibold text-slate-800">{{ mcu.tb }}cm / {{ mcu.bb }}kg / {{ mcu.bmi }}</span>
              </div>
              <div>
                <span class="text-slate-400 block text-[10px]">Tekanan Darah:</span>
                <span class="font-semibold text-slate-800">{{ mcu.sistolik }}/{{ mcu.diastolik }} mmHg</span>
              </div>
              <div>
                <span class="text-slate-400 block text-[10px]">HbA1c / GDP:</span>
                <span class="font-semibold text-slate-800">{{ mcu.hba1c || '-' }}% / {{ mcu.gdp || '-' }}</span>
              </div>
              <div>
                <span class="text-slate-400 block text-[10px]">Kolesterol / LDL:</span>
                <span class="font-semibold text-slate-800">{{ mcu.kolesterol || '-' }} / {{ mcu.ldl || '-' }}</span>
              </div>
              <div>
                <span class="text-slate-400 block text-[10px]">Merokok:</span>
                <span class="font-semibold text-slate-800">{{ mcu.riwayat_merokok || '-' }}</span>
              </div>
              <div>
                <span class="text-slate-400 block text-[10px]">Risiko Jkt:</span>
                <span class="font-semibold text-slate-800">{{ mcu.skor_kardiovaskular_jakarta || '-' }}</span>
              </div>
            </div>

            <div class="p-3 bg-white rounded-lg border border-slate-200 text-xs">
              <span class="font-bold text-slate-700">Hasil Resume / Catatan MCU:</span>
              <p class="text-slate-800 mt-0.5">{{ mcu.hasil_mcu }}</p>
            </div>

            <div class="text-right">
              <Link
                :href="route('mcu.show', mcu.id)"
                class="inline-flex items-center gap-1 text-xs font-semibold text-red-600 hover:text-red-700"
              >
                <span>Lihat Detail Parameter Lengkap</span>
                <span>&rarr;</span>
              </Link>
            </div>
          </div>
        </div>

        <div v-else class="text-center py-6 text-slate-400 text-xs">
          Belum ada riwayat MCU untuk karyawan ini.
        </div>
      </div>

      <!-- Daily Check Up (DCU) Timeline Section -->
      <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-xs space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
            <ClipboardCheck class="size-5 text-red-600" />
            <span>Histori Daily Check Up (DCU - Harian)</span>
          </h3>

          <Link
            v-if="$page.props.auth.user.role !== 'employee'"
            :href="route('daily-check-ups.create')"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-700 hover:bg-red-100 font-bold rounded-lg text-xs transition"
          >
            <span>+ Input DCU Hari Ini</span>
          </Link>
        </div>

        <div v-if="employee.daily_check_ups && employee.daily_check_ups.length > 0" class="space-y-3">
          <div
            v-for="dcu in employee.daily_check_ups"
            :key="dcu.id"
            class="p-4 rounded-xl border border-slate-200 bg-slate-50/60 hover:bg-white hover:border-slate-300 transition space-y-2 text-xs"
          >
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200/80 pb-2">
              <div class="flex items-center gap-2 font-bold text-slate-800">
                <Calendar class="size-3.5 text-slate-400" />
                <span>{{ dcu.tanggal_pemeriksaan }}</span>
                <span class="text-slate-400">|</span>
                <span class="font-mono text-slate-600 font-normal">{{ dcu.waktu_pemeriksaan?.substring(0, 5) }} WIB</span>
                <span v-if="dcu.area" class="text-slate-500 font-normal">({{ dcu.area }})</span>
              </div>

              <div class="flex items-center gap-2">
                <span
                  class="px-2.5 py-0.5 rounded-full text-[10px] font-bold"
                  :class="{
                    'bg-emerald-100 text-emerald-800': dcu.status_kelayakan === 'Fit to Work',
                    'bg-amber-100 text-amber-800': dcu.status_kelayakan === 'Fit with Note',
                    'bg-rose-100 text-rose-800': dcu.status_kelayakan === 'Unfit',
                  }"
                >
                  {{ dcu.status_kelayakan }}
                </span>

                <Link
                  :href="route('daily-check-ups.show', dcu.id)"
                  class="text-red-600 hover:text-red-700 font-semibold"
                >
                  Detail &rarr;
                </Link>
              </div>
            </div>

            <!-- Parameters Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-1 text-slate-600 text-[11px]">
              <div><strong>Tekanan Darah:</strong> {{ dcu.sistolik }}/{{ dcu.diastolik }} mmHg</div>
              <div><strong>Nadi / Suhu:</strong> {{ dcu.nadi_istirahat || '-' }} bpm / {{ dcu.temperatur || '-' }}°C</div>
              <div><strong>SpO2:</strong> {{ dcu.saturasi_oksigen || '-' }}%</div>
              <div><strong>Kesadaran:</strong> {{ dcu.kesadaran }}</div>
            </div>

            <p v-if="dcu.catatan_pemeriksa" class="text-slate-500 italic text-[11px] pt-1">
              Catatan: {{ dcu.catatan_pemeriksa }}
            </p>
          </div>
        </div>

        <div v-else class="text-center py-6 text-slate-400 text-xs">
          Belum ada riwayat Daily Check Up untuk pekerja ini.
        </div>
      </div>

      <!-- Health Alerts & Follow Ups History -->
      <div v-if="employee.health_alerts && employee.health_alerts.length > 0" class="bg-white rounded-xl border border-slate-200 p-6 shadow-xs space-y-4">
        <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
          <AlertTriangle class="size-5 text-amber-500" />
          <span>Health Alerts & Catatan Tindak Lanjut</span>
        </h3>

        <div class="space-y-3">
          <div
            v-for="alert in employee.health_alerts"
            :key="alert.id"
            class="p-4 rounded-xl border border-amber-200 bg-amber-50/30 space-y-2 text-xs"
          >
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <ClassificationBadge :code="alert.severity" :show-label="false" />
                <span class="font-bold text-slate-900">Kategori: {{ alert.category }}</span>
              </div>
              <span class="px-2 py-0.5 text-[10px] font-bold rounded-md" :class="alert.status === 'Open' ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700'">
                Status Alert: {{ alert.status }}
              </span>
            </div>
            <p class="text-slate-800 font-medium">{{ alert.reason }}</p>

            <!-- Follow Ups List -->
            <div v-if="alert.follow_ups && alert.follow_ups.length > 0" class="mt-3 pt-3 border-t border-amber-200 space-y-2">
              <p class="font-bold text-slate-700 text-[11px]">Riwayat Tindak Lanjut:</p>
              <div v-for="f in alert.follow_ups" :key="f.id" class="p-2.5 bg-white rounded-lg border border-slate-200 text-xs">
                <div class="flex items-center justify-between text-[10px] text-slate-400 mb-1">
                  <span>PIC: {{ f.pic?.name || 'Petugas HSE' }}</span>
                  <span>Status: {{ f.status }}</span>
                </div>
                <p class="text-slate-800">{{ f.notes }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
