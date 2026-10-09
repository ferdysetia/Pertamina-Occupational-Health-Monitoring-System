<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { FileHeart, ChevronLeft, Save } from '@lucide/vue';

const props = defineProps({
  employees: Array,
});

const form = useForm({
  employee_id: '',
  tanggal_mcu: new Date().toISOString().substring(0, 10),
  umur: '',
  lingkar_perut: '',
  tb: '',
  bb: '',
  bmi: '',
  sistolik: '',
  diastolik: '',
  hba1c: '',
  gdp: '',
  kolesterol: '',
  ldl: '',
  hdl: '',
  trigliserida: '',
  eritrosit: '',
  leukosit: '',
  trombosit: '',
  spirometri: 'Normal',
  foto_thorax: 'Normal',
  ekg: 'Normal',
  visus_mata: 'Normal',
  audiometri: 'Normal',
  riwayat_alkohol: 'Tidak',
  riwayat_merokok: 'Tidak merokok',
  riwayat_diabetes: 'Tidak Ada',
  skor_kardiovaskular_jakarta: 'Rendah',
  frekuensi_olahraga: '1×/minggu',
  hasil_mcu: '',
  klasifikasi_mcu: 'P1',
});

const calculateBmi = () => {
  if (form.tb > 0 && form.bb > 0) {
    const heightInMeters = form.tb / 100;
    form.bmi = (form.bb / (heightInMeters * heightInMeters)).toFixed(2);
  }
};

const submit = () => {
  form.post(route('mcu.store'));
};
</script>

<template>
  <AppLayout title="Input Data MCU Baru">
    <div class="max-w-4xl mx-auto space-y-6">
      <div class="flex items-center justify-between">
        <Link
          :href="route('mcu.index')"
          class="inline-flex items-center gap-1 text-xs font-semibold text-slate-600 hover:text-red-600 transition"
        >
          <ChevronLeft class="size-4" />
          <span>Batal & Kembali</span>
        </Link>
        <span class="text-xs text-slate-400">Form Input MCU Karyawan</span>
      </div>

      <form @submit.prevent="submit" class="bg-white rounded-xl border border-slate-200 p-6 shadow-xs space-y-6">
        <div class="border-b border-slate-200 pb-4">
          <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
            <FileHeart class="size-5 text-red-600" />
            <span>Form Input Hasil MCU Baru</span>
          </h2>
          <p class="text-xs text-slate-500 mt-0.5">Masukkan data lengkap hasil pemeriksaan fisik dan laboratorium</p>
        </div>

        <!-- Section 1: Informasi Karyawan & Klasifikasi -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Karyawan *</label>
            <select
              v-model="form.employee_id"
              required
              class="w-full text-xs px-3 py-2 border border-slate-200 rounded-lg focus:outline-hidden focus:border-red-600 bg-white"
            >
              <option value="" disabled>-- Pilih Karyawan --</option>
              <option v-for="emp in employees" :key="emp.id" :value="emp.id">
                {{ emp.nik }} - {{ emp.name }} ({{ emp.distrik }})
              </option>
            </select>
            <p v-if="form.errors.employee_id" class="text-[10px] text-red-600 mt-1">{{ form.errors.employee_id }}</p>
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
              <label class="block text-[11px] text-slate-600 mb-1">BMI (Otomatis)</label>
              <input v-model="form.bmi" type="number" step="0.01" readonly class="w-full text-xs px-3 py-1.5 border border-slate-200 rounded-lg bg-slate-50 font-bold" />
            </div>
            <div>
              <label class="block text-[11px] text-slate-600 mb-1">Lingkar Perut (cm)</label>
              <input v-model="form.lingkar_perut" type="number" step="0.1" class="w-full text-xs px-3 py-1.5 border border-slate-200 rounded-lg" />
            </div>
            <div>
              <label class="block text-[11px] text-slate-600 mb-1">Tekanan Sistolik (mmHg)</label>
              <input v-model="form.sistolik" type="number" class="w-full text-xs px-3 py-1.5 border border-slate-200 rounded-lg" />
            </div>
            <div>
              <label class="block text-[11px] text-slate-600 mb-1">Tekanan Diastolik (mmHg)</label>
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

        <!-- Section 4: Penunjang Medis & Resume -->
        <div class="space-y-3 pt-2 border-t border-slate-100">
          <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Pemeriksaan Penunjang & Resume Diagnosa</h3>
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div>
              <label class="block text-[11px] text-slate-600 mb-1">Spirometri</label>
              <input v-model="form.spirometri" type="text" class="w-full text-xs px-3 py-1.5 border border-slate-200 rounded-lg" />
            </div>
            <div>
              <label class="block text-[11px] text-slate-600 mb-1">Visus Mata</label>
              <input v-model="form.visus_mata" type="text" class="w-full text-xs px-3 py-1.5 border border-slate-200 rounded-lg" />
            </div>
            <div>
              <label class="block text-[11px] text-slate-600 mb-1">EKG</label>
              <input v-model="form.ekg" type="text" class="w-full text-xs px-3 py-1.5 border border-slate-200 rounded-lg" />
            </div>
            <div>
              <label class="block text-[11px] text-slate-600 mb-1">Foto Thorax</label>
              <input v-model="form.foto_thorax" type="text" class="w-full text-xs px-3 py-1.5 border border-slate-200 rounded-lg" />
            </div>
          </div>

          <div class="pt-2">
            <label class="block text-xs font-bold text-slate-700 mb-1">Resume Diagnosa / Hasil MCU *</label>
            <textarea
              v-model="form.hasil_mcu"
              rows="3"
              placeholder="Contoh: Obesitas; Visus Mata Abnormal; Risk Kardiovaskular Tinggi"
              class="w-full text-xs px-3 py-2 border border-slate-200 rounded-lg focus:outline-hidden focus:border-red-600"
            ></textarea>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-4 border-t border-slate-200 flex justify-end">
          <button
            type="submit"
            :disabled="form.processing"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 text-white font-bold rounded-lg text-xs hover:bg-red-700 transition shadow-xs"
          >
            <Save class="size-4" />
            <span>Simpan Data MCU</span>
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
