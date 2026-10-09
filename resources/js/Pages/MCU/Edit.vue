<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { FileHeart, ChevronLeft, Save } from '@lucide/vue';

const props = defineProps({
  mcuRecord: Object,
  employees: Array,
});

const form = useForm({
  employee_id: props.mcuRecord.employee_id,
  tanggal_mcu: props.mcuRecord.tanggal_mcu,
  umur: props.mcuRecord.umur,
  lingkar_perut: props.mcuRecord.lingkar_perut,
  tb: props.mcuRecord.tb,
  bb: props.mcuRecord.bb,
  bmi: props.mcuRecord.bmi,
  sistolik: props.mcuRecord.sistolik,
  diastolik: props.mcuRecord.diastolik,
  hba1c: props.mcuRecord.hba1c,
  gdp: props.mcuRecord.gdp,
  kolesterol: props.mcuRecord.kolesterol,
  ldl: props.mcuRecord.ldl,
  hdl: props.mcuRecord.hdl,
  trigliserida: props.mcuRecord.trigliserida,
  eritrosit: props.mcuRecord.eritrosit,
  leukosit: props.mcuRecord.leukosit,
  trombosit: props.mcuRecord.trombosit,
  spirometri: props.mcuRecord.spirometri,
  foto_thorax: props.mcuRecord.foto_thorax,
  ekg: props.mcuRecord.ekg,
  visus_mata: props.mcuRecord.visus_mata,
  audiometri: props.mcuRecord.audiometri,
  riwayat_alkohol: props.mcuRecord.riwayat_alkohol,
  riwayat_merokok: props.mcuRecord.riwayat_merokok,
  riwayat_diabetes: props.mcuRecord.riwayat_diabetes,
  skor_kardiovaskular_jakarta: props.mcuRecord.skor_kardiovaskular_jakarta,
  frekuensi_olahraga: props.mcuRecord.frekuensi_olahraga,
  hasil_mcu: props.mcuRecord.hasil_mcu,
  klasifikasi_mcu: props.mcuRecord.klasifikasi_mcu,
});

const calculateBmi = () => {
  if (form.tb > 0 && form.bb > 0) {
    const heightInMeters = form.tb / 100;
    form.bmi = (form.bb / (heightInMeters * heightInMeters)).toFixed(2);
  }
};

const submit = () => {
  form.put(route('mcu.update', props.mcuRecord.id));
};
</script>

<template>
  <AppLayout :title="`Edit MCU — ${mcuRecord.employee?.nik}`">
    <div class="max-w-4xl mx-auto space-y-6">
      <div class="flex items-center justify-between">
        <Link
          :href="route('mcu.show', mcuRecord.id)"
          class="inline-flex items-center gap-1 text-xs font-semibold text-slate-600 hover:text-red-600 transition"
        >
          <ChevronLeft class="size-4" />
          <span>Batal & Kembali</span>
        </Link>
        <span class="text-xs text-slate-400">Edit MCU Record ID #{{ mcuRecord.id }}</span>
      </div>

      <form @submit.prevent="submit" class="bg-white rounded-xl border border-slate-200 p-6 shadow-xs space-y-6">
        <div class="border-b border-slate-200 pb-4">
          <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
            <FileHeart class="size-5 text-red-600" />
            <span>Edit Record MCU Karyawan</span>
          </h2>
          <p class="text-xs text-slate-500 mt-0.5">Perbarui data pemeriksaan fisik, laboratorium, atau klasifikasi P1-P7</p>
        </div>

        <!-- Section 1: Informasi Karyawan & Klasifikasi -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Karyawan *</label>
            <select
              v-model="form.employee_id"
              required
              class="w-full text-xs px-3 py-2 border border-slate-200 rounded-lg focus:outline-hidden focus:border-red-600 bg-white"
            >
              <option v-for="emp in employees" :key="emp.id" :value="emp.id">
                {{ emp.nik }} - {{ emp.name }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal MCU *</label>
            <input
              v-model="form.tanggal_mcu"
              type="date"
              required
              class="w-full text-xs px-3 py-2 border border-slate-200 rounded-lg focus:outline-hidden focus:border-red-600"
            />
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Klasifikasi Kesehatan (P1 - P7) *</label>
            <select
              v-model="form.klasifikasi_mcu"
              required
              class="w-full text-xs px-3 py-2 border border-slate-200 rounded-lg focus:outline-hidden focus:border-red-600 bg-white font-bold text-red-600"
            >
              <option value="P1">P1 — Fit / Normal</option>
              <option value="P2">P2 — Kelainan Ringan</option>
              <option value="P3">P3 — Perhatian Ringan</option>
              <option value="P4">P4 — Perhatian Sedang</option>
              <option value="P5">P5 — Perlu Tindak Lanjut</option>
              <option value="P6">P6 — Perhatian Tinggi</option>
              <option value="P7">P7 — Kondisi Kurang Baik</option>
            </select>
          </div>
        </div>

        <!-- Section 2: Antropometri -->
        <div class="space-y-3 pt-2 border-t border-slate-100">
          <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Antropometri & Vital Sign</h3>
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div>
              <label class="block text-[11px] text-slate-600 mb-1">Umur (Thn)</label>
              <input v-model="form.umur" type="number" class="w-full text-xs px-3 py-1.5 border border-slate-200 rounded-lg" />
            </div>
            <div>
              <label class="block text-[11px] text-slate-600 mb-1">Tinggi Badan (cm)</label>
              <input v-model="form.tb" @input="calculateBmi" type="number" step="0.1" class="w-full text-xs px-3 py-1.5 border border-slate-200 rounded-lg" />
            </div>
            <div>
              <label class="block text-[11px] text-slate-600 mb-1">Berat Badan (kg)</label>
              <input v-model="form.bb" @input="calculateBmi" type="number" step="0.1" class="w-full text-xs px-3 py-1.5 border border-slate-200 rounded-lg" />
            </div>
            <div>
              <label class="block text-[11px] text-slate-600 mb-1">BMI</label>
              <input v-model="form.bmi" type="number" step="0.01" class="w-full text-xs px-3 py-1.5 border border-slate-200 rounded-lg font-bold" />
            </div>
            <div>
              <label class="block text-[11px] text-slate-600 mb-1">Lingkar Perut (cm)</label>
              <input v-model="form.lingkar_perut" type="number" step="0.1" class="w-full text-xs px-3 py-1.5 border border-slate-200 rounded-lg" />
            </div>
            <div>
              <label class="block text-[11px] text-slate-600 mb-1">Tekanan Sistolik</label>
              <input v-model="form.sistolik" type="number" class="w-full text-xs px-3 py-1.5 border border-slate-200 rounded-lg" />
            </div>
            <div>
              <label class="block text-[11px] text-slate-600 mb-1">Tekanan Diastolik</label>
              <input v-model="form.diastolik" type="number" class="w-full text-xs px-3 py-1.5 border border-slate-200 rounded-lg" />
            </div>
          </div>
        </div>

        <!-- Section 3: Lab Blood -->
        <div class="space-y-3 pt-2 border-t border-slate-100">
          <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Kimia Darah & Hematologi</h3>
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div>
              <label class="block text-[11px] text-slate-600 mb-1">GDP (mg/dL)</label>
              <input v-model="form.gdp" type="number" step="0.1" class="w-full text-xs px-3 py-1.5 border border-slate-200 rounded-lg" />
            </div>
            <div>
              <label class="block text-[11px] text-slate-600 mb-1">HbA1c (%)</label>
              <input v-model="form.hba1c" type="number" step="0.1" class="w-full text-xs px-3 py-1.5 border border-slate-200 rounded-lg" />
            </div>
            <div>
              <label class="block text-[11px] text-slate-600 mb-1">Kolesterol Total</label>
              <input v-model="form.kolesterol" type="number" class="w-full text-xs px-3 py-1.5 border border-slate-200 rounded-lg" />
            </div>
            <div>
              <label class="block text-[11px] text-slate-600 mb-1">LDL</label>
              <input v-model="form.ldl" type="number" step="0.1" class="w-full text-xs px-3 py-1.5 border border-slate-200 rounded-lg" />
            </div>
            <div>
              <label class="block text-[11px] text-slate-600 mb-1">HDL</label>
              <input v-model="form.hdl" type="number" class="w-full text-xs px-3 py-1.5 border border-slate-200 rounded-lg" />
            </div>
            <div>
              <label class="block text-[11px] text-slate-600 mb-1">Trigliserida</label>
              <input v-model="form.trigliserida" type="number" class="w-full text-xs px-3 py-1.5 border border-slate-200 rounded-lg" />
            </div>
          </div>
        </div>

        <!-- Section 4: Resume -->
        <div class="space-y-3 pt-2 border-t border-slate-100">
          <label class="block text-xs font-bold text-slate-700 mb-1">Resume Diagnosa / Hasil MCU *</label>
          <textarea
            v-model="form.hasil_mcu"
            rows="3"
            class="w-full text-xs px-3 py-2 border border-slate-200 rounded-lg focus:outline-hidden focus:border-red-600"
          ></textarea>
        </div>

        <!-- Submit Button -->
        <div class="pt-4 border-t border-slate-200 flex justify-end">
          <button
            type="submit"
            :disabled="form.processing"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 text-white font-bold rounded-lg text-xs hover:bg-red-700 transition shadow-xs"
          >
            <Save class="size-4" />
            <span>Simpan Perubahan</span>
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
