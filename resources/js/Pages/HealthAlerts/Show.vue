<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ClassificationBadge from '@/Components/ClassificationBadge.vue';
import { AlertTriangle, ChevronLeft, Plus, CheckCircle, Clock, User, Calendar, MessageSquare } from '@lucide/vue';

const props = defineProps({
  alert: Object,
  pics: Array,
});

const form = useForm({
  health_alert_id: props.alert.id,
  pic_id: '',
  notes: '',
  target_date: '',
  status: 'In Progress',
});

const submitFollowUp = () => {
  form.post(route('follow-ups.store'), {
    onSuccess: () => {
      form.reset('notes', 'target_date');
    },
  });
};
</script>

<template>
  <AppLayout :title="`Health Alert — ${alert.employee?.nik}`">
    <div class="max-w-4xl mx-auto space-y-6">
      <div class="flex items-center justify-between">
        <Link
          :href="route('health-alerts.index')"
          class="inline-flex items-center gap-1 text-xs font-semibold text-slate-600 hover:text-red-600 transition"
        >
          <ChevronLeft class="size-4" />
          <span>Kembali ke Daftar Health Alerts</span>
        </Link>
        <span class="text-xs text-slate-400">Health Alert ID #{{ alert.id }}</span>
      </div>

      <!-- Alert Details Banner -->
      <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-xs space-y-4">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border-b border-slate-200 pb-4">
          <div>
            <div class="flex items-center gap-3">
              <ClassificationBadge :code="alert.severity" />
              <h2 class="text-lg font-bold text-slate-900">{{ alert.employee?.name }}</h2>
              <span class="text-xs text-slate-500">({{ alert.employee?.nik }})</span>
            </div>
            <p class="text-xs text-slate-500 mt-1">
              {{ alert.employee?.distrik }} | {{ alert.employee?.fungsi }}
            </p>
          </div>

          <div class="px-3 py-1.5 rounded-lg border text-xs font-bold"
            :class="{
              'bg-red-50 text-red-700 border-red-200': alert.status === 'Open',
              'bg-amber-50 text-amber-700 border-amber-200': alert.status === 'In Progress',
              'bg-emerald-50 text-emerald-700 border-emerald-200': alert.status === 'Closed',
            }"
          >
            Status Alert: {{ alert.status }}
          </div>
        </div>

        <div class="bg-amber-50/50 border border-amber-200 p-4 rounded-xl text-xs space-y-1">
          <span class="font-bold text-amber-900 uppercase text-[10px]">Alasan Perhatian Kesehatan:</span>
          <p class="text-amber-950 font-medium text-sm">{{ alert.reason }}</p>
          <p class="text-amber-800 text-[11px] mt-1">Kategori: {{ alert.category }}</p>
        </div>
      </div>

      <!-- Follow-Up Timeline & Form -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Form Add Follow-Up Note (Admin / HSE / Doctor) -->
        <div v-if="$page.props.auth.user.role !== 'employee'" class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs space-y-4">
          <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
            <Plus class="size-4 text-red-600" />
            <span>Tambah Catatan Tindak Lanjut Medis</span>
          </h3>

          <form @submit.prevent="submitFollowUp" class="space-y-3 text-xs">
            <div>
              <label class="block font-semibold text-slate-700 mb-1">Catatan Tindak Lanjut / Rekomendasi Medis *</label>
              <textarea
                v-model="form.notes"
                required
                rows="3"
                placeholder="Tuliskan tindakan yang dilakukan (misal: rujukan konsultasi dokter spesialis, jadwal re-evaluasi, dsb)..."
                class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:outline-hidden focus:border-red-600"
              ></textarea>
            </div>

            <div>
              <label class="block font-semibold text-slate-700 mb-1">Penanggung Jawab (PIC)</label>
              <select
                v-model="form.pic_id"
                class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:outline-hidden focus:border-red-600 bg-white"
              >
                <option value="">-- Pilih PIC Petugas --</option>
                <option v-for="pic in pics" :key="pic.id" :value="pic.id">
                  {{ pic.name }} ({{ pic.role.toUpperCase() }})
                </option>
              </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-semibold text-slate-700 mb-1">Target Tanggal Re-evaluasi</label>
                <input
                  v-model="form.target_date"
                  type="date"
                  class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:outline-hidden focus:border-red-600"
                />
              </div>

              <div>
                <label class="block font-semibold text-slate-700 mb-1">Update Status Alert *</label>
                <select
                  v-model="form.status"
                  required
                  class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:outline-hidden focus:border-red-600 bg-white font-bold"
                >
                  <option value="Pending">Pending (Menunggu)</option>
                  <option value="In Progress">In Progress (Proses Penanganan)</option>
                  <option value="Completed">Completed (Selesai & Close Alert)</option>
                </select>
              </div>
            </div>

            <button
              type="submit"
              :disabled="form.processing"
              class="w-full py-2.5 bg-red-600 text-white font-bold rounded-lg text-xs hover:bg-red-700 transition shadow-xs mt-2"
            >
              Simpan Tindak Lanjut
            </button>
          </form>
        </div>

        <!-- Timeline of Previous Follow-Ups -->
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs space-y-4" :class="{'lg:col-span-2': $page.props.auth.user.role === 'employee'}">
          <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
            <Clock class="size-4 text-slate-600" />
            <span>Histori Timeline Tindak Lanjut</span>
          </h3>

          <div v-if="alert.follow_ups && alert.follow_ups.length > 0" class="space-y-3">
            <div
              v-for="item in alert.follow_ups"
              :key="item.id"
              class="p-3.5 rounded-lg border border-slate-200 bg-slate-50/50 space-y-1.5 text-xs"
            >
              <div class="flex items-center justify-between text-[11px] text-slate-500 border-b border-slate-200 pb-1.5">
                <span class="font-bold text-slate-700">PIC: {{ item.pic?.name || 'Tim HSE / Dokter' }}</span>
                <span class="px-2 py-0.5 rounded-md font-bold text-[10px]"
                  :class="{
                    'bg-amber-100 text-amber-800': item.status === 'In Progress',
                    'bg-emerald-100 text-emerald-800': item.status === 'Completed',
                    'bg-slate-100 text-slate-700': item.status === 'Pending',
                  }"
                >
                  {{ item.status }}
                </span>
              </div>
              <p class="text-slate-800 font-medium">{{ item.notes }}</p>
              <p v-if="item.target_date" class="text-[10px] text-slate-400">Target Tanggal: {{ item.target_date }}</p>
            </div>
          </div>

          <div v-else class="text-center py-8 text-xs text-slate-400">
            Belum ada catatan tindak lanjut.
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
