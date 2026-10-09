<script setup>
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ClipboardCheck, ChevronLeft, Save, User, HeartPulse, Stethoscope, AlertTriangle } from '@lucide/vue';

const props = defineProps({
  employees: Array,
  defaultDate: String,
  defaultTime: String,
});

const form = useForm({
  tanggal_pemeriksaan: props.defaultDate || new Date().toISOString().substring(0, 10),
  waktu_pemeriksaan: props.defaultTime || new Date().toTimeString().substring(0, 5),
  employee_id: '',
  nama_pekerja: '',
  nomor_pekerja: '',
  tanggal_lahir: '',
  usia: '',
  jenis_pekerjaan: '',
  region: 'Region Sumatera',
  area: '',
  lokasi_pekerjaan: '',
  detail_lokasi: '',

  // Tanya Jawab
  konsumsi_obat: 'Tidak',
  detail_konsumsi_obat: '',
  keluhan_saat_ini: 'Tidak',
  detail_keluhan: '',
  riwayat_penyakit: 'Tidak',
  detail_riwayat_penyakit: '',

  // Pemeriksaan Fisik
  kesadaran: 'Compos Mentis',
  sistolik: 120,
  diastolik: 80,
  nadi_istirahat: 72,
  irama_nadi: 'Teratur',
  frekuensi_napas: 18,
  temperatur: 36.5,
  saturasi_oksigen: 98.0,
  conjungtiva_mata: 'Tidak Pucat',
  pupil_mata: 'Normal',
  keseimbangan: 'Negatif',
  penyalahgunaan_napza: 'Negatif',

  // Kesimpulan
  status_kelayakan: 'Fit to Work',
  catatan_pemeriksa: '',
});

const onEmployeeSelected = () => {
  const selected = props.employees.find((e) => e.id === Number(form.employee_id));
  if (selected) {
    form.nama_pekerja = selected.name;
    form.nomor_pekerja = selected.nik;
    form.jenis_pekerjaan = `${selected.fungsi || ''} (${selected.tipe_pekerjaan || ''})`;
    form.area = selected.distrik || '';
    form.lokasi_pekerjaan = selected.distrik || '';
    if (selected.latest_mcu_record?.umur) {
      form.usia = selected.latest_mcu_record.umur;
    }
  }
};

const submit = () => {
  form.post(route('daily-check-ups.store'));
};
</script>

<template>
  <AppLayout title="Input Daily Check Up Baru">
    <div class="max-w-4xl mx-auto space-y-6">
      <!-- Back Link & Header -->
      <div class="flex items-center justify-between">
        <Link
          :href="route('daily-check-ups.index')"
          class="inline-flex items-center gap-1 text-xs font-semibold text-slate-600 hover:text-red-600 transition"
        >
          <ChevronLeft class="size-4" />
          <span>Batal & Kembali ke Daftar DCU</span>
        </Link>
        <span class="text-xs text-slate-400">Skrining Kesehatan Harian Fit-to-Work</span>
      </div>

      <form @submit.prevent="submit" class="bg-white rounded-2xl border border-slate-200/90 p-6 sm:p-8 shadow-xs space-y-8">
        <!-- Form Header -->
        <div class="border-b border-slate-100 pb-5">
          <div class="flex items-center gap-3">
            <div class="size-10 rounded-xl bg-red-600 text-white flex items-center justify-center font-bold">
              <ClipboardCheck class="size-5" />
            </div>
            <div>
              <h2 class="text-lg font-bold text-slate-900">Form Pemeriksaan Daily Check Up (DCU)</h2>
              <p class="text-xs text-slate-500 mt-0.5">Lengkapi identitas, tanya jawab gejala, dan hasil pemeriksaan fisik pekerja</p>
            </div>
          </div>
        </div>

        <!-- SECTION 1: WAKTU & IDENTITAS PEKERJA -->
        <div class="space-y-4">
          <h3 class="text-xs font-bold uppercase tracking-wider text-red-600 flex items-center gap-2">
            <User class="size-4" />
            <span>1. Waktu Pemeriksaan & Identitas Pekerja</span>
          </h3>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- 1. Tanggal Pemeriksaan (Auto-generated) -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Pemeriksaan *</label>
              <input
                v-model="form.tanggal_pemeriksaan"
                type="date"
                required
                class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:outline-hidden focus:border-red-600 font-medium"
              />
            </div>

            <!-- 2. Waktu Pemeriksaan (Auto-generated) -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Waktu Pemeriksaan *</label>
              <input
                v-model="form.waktu_pemeriksaan"
                type="time"
                required
                class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:outline-hidden focus:border-red-600 font-medium"
              />
            </div>

            <!-- Pilih Pekerja Dropdown -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Master Pekerja *</label>
              <select
                v-model="form.employee_id"
                @change="onEmployeeSelected"
                required
                class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:outline-hidden focus:border-red-600 bg-white"
              >
                <option value="" disabled>-- Pilih Pekerja --</option>
                <option v-for="emp in employees" :key="emp.id" :value="emp.id">
                  {{ emp.nik }} - {{ emp.name }} ({{ emp.distrik }})
                </option>
              </select>
            </div>

            <!-- 3. Nama Pekerja -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">3. Nama Pekerja *</label>
              <input
                v-model="form.nama_pekerja"
                type="text"
                required
                placeholder="Nama Pekerja"
                class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:outline-hidden focus:border-red-600"
              />
            </div>

            <!-- 4. Nomor Pekerja (NIK) -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">4. Nomor Pekerja (NIK) *</label>
              <input
                v-model="form.nomor_pekerja"
                type="text"
                required
                placeholder="Contoh: PTM-001"
                class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:outline-hidden focus:border-red-600 font-mono"
              />
            </div>

            <!-- 5. Tanggal Lahir -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">5. Tanggal Lahir</label>
              <input
                v-model="form.tanggal_lahir"
                type="date"
                class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:outline-hidden focus:border-red-600"
              />
            </div>

            <!-- 6. Usia -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">6. Usia (Tahun)</label>
              <input
                v-model="form.usia"
                type="number"
                placeholder="Tahun"
                class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:outline-hidden focus:border-red-600"
              />
            </div>

            <!-- 7. Jenis Pekerjaan -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">7. Jenis Pekerjaan</label>
              <input
                v-model="form.jenis_pekerjaan"
                type="text"
                placeholder="Contoh: Operator Shift / Maintenance"
                class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:outline-hidden focus:border-red-600"
              />
            </div>

            <!-- 8. Region -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">8. Region</label>
              <input
                v-model="form.region"
                type="text"
                placeholder="Contoh: Region Sumatera"
                class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:outline-hidden focus:border-red-600"
              />
            </div>

            <!-- 9. Area -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">9. Area</label>
              <input
                v-model="form.area"
                type="text"
                placeholder="Contoh: Distrik Medan / Pangkalan Brandan"
                class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:outline-hidden focus:border-red-600"
              />
            </div>

            <!-- 10. Lokasi Pekerjaan -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">10. Lokasi Pekerjaan</label>
              <input
                v-model="form.lokasi_pekerjaan"
                type="text"
                placeholder="Contoh: Plant Station A"
                class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:outline-hidden focus:border-red-600"
              />
            </div>

            <!-- 11. Detail Lokasi -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">11. Detail Lokasi</label>
              <input
                v-model="form.detail_lokasi"
                type="text"
                placeholder="Contoh: Ruang Kontrol / Pipeline Zone 2"
                class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl focus:outline-hidden focus:border-red-600"
              />
            </div>
          </div>
        </div>

        <!-- SECTION 2: TANYA JAWAB (ANAMNESIS SINGKAT) -->
        <div class="space-y-4 pt-4 border-t border-slate-100">
          <h3 class="text-xs font-bold uppercase tracking-wider text-red-600 flex items-center gap-2">
            <HeartPulse class="size-4" />
            <span>2. Tanya Jawab (Anamnesis Singkat)</span>
          </h3>

          <div class="space-y-4 p-4 rounded-xl bg-slate-50 border border-slate-200/80">
            <!-- Q1: Sedang mengkonsumsi obat-obatan? -->
            <div class="space-y-2">
              <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <span class="text-xs font-bold text-slate-800">• Sedang mengkonsumsi obat-obatan saat ini?</span>
                <div class="flex items-center gap-4">
                  <label class="inline-flex items-center gap-1.5 text-xs cursor-pointer">
                    <input v-model="form.konsumsi_obat" type="radio" value="Tidak" class="text-red-600" />
                    <span>Tidak</span>
                  </label>
                  <label class="inline-flex items-center gap-1.5 text-xs cursor-pointer font-semibold text-red-600">
                    <input v-model="form.konsumsi_obat" type="radio" value="Ya" class="text-red-600" />
                    <span>Ya</span>
                  </label>
                </div>
              </div>
              <input
                v-if="form.konsumsi_obat === 'Ya'"
                v-model="form.detail_konsumsi_obat"
                type="text"
                placeholder="Sebutkan jenis obat yang dikonsumsi..."
                class="w-full text-xs px-3 py-1.5 bg-white border border-slate-300 rounded-lg focus:outline-hidden focus:border-red-600"
              />
            </div>

            <!-- Q2: Apakah ada keluhan saat ini? -->
            <div class="space-y-2 pt-3 border-t border-slate-200">
              <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <span class="text-xs font-bold text-slate-800">• Apakah ada keluhan kesehatan saat ini (pusing, demam, lemas, nyeri, dll)?</span>
                <div class="flex items-center gap-4">
                  <label class="inline-flex items-center gap-1.5 text-xs cursor-pointer">
                    <input v-model="form.keluhan_saat_ini" type="radio" value="Tidak" class="text-red-600" />
                    <span>Tidak</span>
                  </label>
                  <label class="inline-flex items-center gap-1.5 text-xs cursor-pointer font-semibold text-red-600">
                    <input v-model="form.keluhan_saat_ini" type="radio" value="Ya" class="text-red-600" />
                    <span>Ya</span>
                  </label>
                </div>
              </div>
              <input
                v-if="form.keluhan_saat_ini === 'Ya'"
                v-model="form.detail_keluhan"
                type="text"
                placeholder="Jelaskan detail keluhan kesehatan yang dirasakan..."
                class="w-full text-xs px-3 py-1.5 bg-white border border-slate-300 rounded-lg focus:outline-hidden focus:border-red-600"
              />
            </div>

            <!-- Q3: Riwayat Penyakit? -->
            <div class="space-y-2 pt-3 border-t border-slate-200">
              <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <span class="text-xs font-bold text-slate-800">• Riwayat Penyakit Kronis / Bawaan?</span>
                <div class="flex items-center gap-4">
                  <label class="inline-flex items-center gap-1.5 text-xs cursor-pointer">
                    <input v-model="form.riwayat_penyakit" type="radio" value="Tidak" class="text-red-600" />
                    <span>Tidak</span>
                  </label>
                  <label class="inline-flex items-center gap-1.5 text-xs cursor-pointer font-semibold text-red-600">
                    <input v-model="form.riwayat_penyakit" type="radio" value="Ya" class="text-red-600" />
                    <span>Ya</span>
                  </label>
                </div>
              </div>
              <input
                v-if="form.riwayat_penyakit === 'Ya'"
                v-model="form.detail_riwayat_penyakit"
                type="text"
                placeholder="Sebutkan riwayat penyakit (cth: hipertensi, asma, diabetes, dll)..."
                class="w-full text-xs px-3 py-1.5 bg-white border border-slate-300 rounded-lg focus:outline-hidden focus:border-red-600"
              />
            </div>
          </div>
        </div>

        <!-- SECTION 3: PEMERIKSAAN FISIK -->
        <div class="space-y-4 pt-4 border-t border-slate-100">
          <h3 class="text-xs font-bold uppercase tracking-wider text-red-600 flex items-center gap-2">
            <Stethoscope class="size-4" />
            <span>3. Pemeriksaan Fisik (Vital Sign & Uji Klinis)</span>
          </h3>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Kesadaran -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Kesadaran *</label>
              <select v-model="form.kesadaran" required class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl bg-white">
                <option value="Compos Mentis">Compos Mentis (Sadar Penuh)</option>
                <option value="Somnolen">Somnolen (Mengantuk Berat)</option>
                <option value="Apatis">Apatis</option>
                <option value="Hilang Sadar">Hilang Sadar / Pingsan</option>
              </select>
            </div>

            <!-- Tekanan Darah Sistolik -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Tekanan Sistolik (mmHg) *</label>
              <input v-model="form.sistolik" type="number" required class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl font-bold" />
            </div>

            <!-- Tekanan Darah Diastolik -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Tekanan Diastolik (mmHg) *</label>
              <input v-model="form.diastolik" type="number" required class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl font-bold" />
            </div>

            <!-- Nadi Istirahat -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Nadi Istirahat (bpm)</label>
              <input v-model="form.nadi_istirahat" type="number" class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl" />
            </div>

            <!-- Irama Nadi -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Irama Nadi *</label>
              <select v-model="form.irama_nadi" required class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl bg-white">
                <option value="Teratur">Teratur (Regular)</option>
                <option value="Tidak Teratur">Tidak Teratur (Irregular)</option>
              </select>
            </div>

            <!-- Frekuensi Napas -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Frekuensi Napas (x/menit)</label>
              <input v-model="form.frekuensi_napas" type="number" class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl" />
            </div>

            <!-- Temperatur -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Temperatur (°C)</label>
              <input v-model="form.temperatur" type="number" step="0.1" class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl" />
            </div>

            <!-- Saturasi Oksigen -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Saturasi Oksigen SpO2 (%)</label>
              <input v-model="form.saturasi_oksigen" type="number" step="0.1" class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl" />
            </div>

            <!-- Conjungtiva Mata -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Conjungtiva Mata *</label>
              <select v-model="form.conjungtiva_mata" required class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl bg-white">
                <option value="Tidak Pucat">Tidak Pucat (Normal)</option>
                <option value="Pucat">Pucat (Anemis)</option>
              </select>
            </div>

            <!-- Pupil Mata -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Pupil Mata *</label>
              <select v-model="form.pupil_mata" required class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl bg-white">
                <option value="Normal">Normal (Isokor)</option>
                <option value="Tidak Normal">Tidak Normal (Anisokor/Dilatasi)</option>
              </select>
            </div>

            <!-- Keseimbangan -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Uji Keseimbangan (Romberg) *</label>
              <select v-model="form.keseimbangan" required class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl bg-white">
                <option value="Negatif">Negatif (Stabil / Normal)</option>
                <option value="Positif">Positif (Goyah / Terganggu)</option>
              </select>
            </div>

            <!-- Penyalahgunaan NAPZA -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Skrining Bebas NAPZA *</label>
              <select v-model="form.penyalahgunaan_napza" required class="w-full text-xs px-3 py-2 border border-slate-200 rounded-xl bg-white">
                <option value="Negatif">Negatif (Bebas / Clear)</option>
                <option value="Positif">Positif (Terindikasi)</option>
              </select>
            </div>
          </div>
        </div>

        <!-- SECTION 4: KESIMPULAN STATUS KELAYAKAN KERJA -->
        <div class="space-y-4 pt-4 border-t border-slate-100">
          <h3 class="text-xs font-bold uppercase tracking-wider text-red-600 flex items-center gap-2">
            <AlertTriangle class="size-4" />
            <span>4. Kesimpulan & Status Kelayakan Kerja (Fit-to-Work)</span>
          </h3>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="sm:col-span-1">
              <label class="block text-xs font-bold text-slate-700 mb-1">Status Kelayakan Kerja *</label>
              <select
                v-model="form.status_kelayakan"
                required
                class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl font-bold bg-white"
                :class="{
                  'text-emerald-700 bg-emerald-50 border-emerald-300': form.status_kelayakan === 'Fit to Work',
                  'text-amber-700 bg-amber-50 border-amber-300': form.status_kelayakan === 'Fit with Note',
                  'text-rose-700 bg-rose-50 border-rose-300': form.status_kelayakan === 'Unfit',
                }"
              >
                <option value="Fit to Work">Fit to Work (Layak Bekerja)</option>
                <option value="Fit with Note">Fit with Note (Layak dengan Catatan)</option>
                <option value="Unfit">Unfit (Tidak Layak / Perlu Istirahat)</option>
              </select>
            </div>

            <div class="sm:col-span-2">
              <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Tambahan Pemeriksa</label>
              <input
                v-model="form.catatan_pemeriksa"
                type="text"
                placeholder="Misal: Istirahat 30 menit & re-tensi, rekomendasi minum air, dll..."
                class="w-full text-xs px-3 py-2.5 border border-slate-200 rounded-xl focus:outline-hidden focus:border-red-600"
              />
            </div>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-6 border-t border-slate-200 flex justify-end gap-3">
          <Link
            :href="route('daily-check-ups.index')"
            class="px-5 py-2.5 bg-slate-100 text-slate-700 font-semibold rounded-xl text-xs hover:bg-slate-200 transition"
          >
            Batal
          </Link>
          <button
            type="submit"
            :disabled="form.processing"
            class="inline-flex items-center gap-2 px-6 py-2.5 bg-red-600 text-white font-bold rounded-xl text-xs hover:bg-red-700 transition shadow-sm"
          >
            <Save class="size-4" />
            <span>Simpan Hasil Daily Check Up</span>
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
