<script setup>
import { ref, computed } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import {
  User,
  Activity,
  MapPin,
  Building2,
  Phone,
  Calendar,
  AlertCircle,
  Home,
  HeartHandshake,
  Droplet,
  ShieldAlert,
  CreditCard,
  Camera,
  Trash2,
  Save,
  CheckCircle2,
  Sparkles,
  HeartPulse,
  Scale,
  Ruler
} from '@lucide/vue';

const props = defineProps({
  user: Object,
});

const employee = computed(() => props.user?.employee || {});
const photoPreview = ref(null);
const photoInput = ref(null);

const form = useForm({
  _method: 'PUT',
  photo: null,
  nik: employee.value.nik || (props.user?.role === 'admin' ? 'ADM-001' : props.user?.role === 'manager' ? 'MGR-001' : props.user?.role === 'doctor' ? 'DOC-001' : props.user?.role === 'hse' ? 'HSE-001' : 'PTM-000'),
  name: employee.value.name || props.user?.name || '',
  tanggal_lahir: employee.value.tanggal_lahir || '',
  jenis_kelamin: employee.value.jenis_kelamin || 'Laki-laki',
  tinggi_badan: employee.value.tinggi_badan || '',
  berat_badan: employee.value.berat_badan || '',
  
  // 1. Golongan Darah & Rhesus
  golongan_darah: employee.value.golongan_darah || 'O',
  rhesus: employee.value.rhesus || '+',

  // 2. Riwayat Alergi
  alergi_obat: employee.value.alergi_obat || '',
  alergi_makanan: employee.value.alergi_makanan || '',

  // Penempatan
  distrik: employee.value.distrik || 'Distrik Prabumulih',
  lokasi_kerja: employee.value.lokasi_kerja || '',
  fungsi: employee.value.fungsi || (props.user?.role === 'doctor' ? 'Medical / Klinik' : props.user?.role === 'hse' ? 'HSSE & Safety' : props.user?.role === 'manager' ? 'HSE Management' : props.user?.role === 'admin' ? 'IT & Sistem' : 'Operasi'),
  tipe_pekerjaan: employee.value.tipe_pekerjaan || (props.user?.role === 'employee' ? 'Shift' : 'Non-Shift'),
  no_telepon: employee.value.no_telepon || '',
  
  // Alamat Domisili Terpisah
  alamat_jalan: employee.value.alamat_jalan || employee.value.alamat || '',
  kecamatan: employee.value.kecamatan || '',
  kabupaten_kota: employee.value.kabupaten_kota || '',
  provinsi: employee.value.provinsi || 'Sumatera Selatan',
  kode_pos: employee.value.kode_pos || '',

  // Kontak Darurat
  nama_kontak_darurat: employee.value.nama_kontak_darurat || '',
  hubungan_kontak_darurat: employee.value.hubungan_kontak_darurat || 'Suami/Istri',
  no_telepon_darurat: employee.value.no_telepon_darurat || '',

  // 3. Jaminan Sosial / BPJS
  bpjs_kesehatan: employee.value.bpjs_kesehatan || '',
  bpjs_ketenagakerjaan: employee.value.bpjs_ketenagakerjaan || '',
});

const selectNewPhoto = () => {
  photoInput.value.click();
};

const updatePhotoPreview = () => {
  const photo = photoInput.value.files[0];
  if (!photo) return;
  const reader = new FileReader();
  reader.onload = (e) => {
    photoPreview.value = e.target.result;
  };
  reader.readAsDataURL(photo);
};

const deletePhoto = () => {
  router.delete(route('current-user-photo.destroy'), {
    preserveScroll: true,
    onSuccess: () => {
      photoPreview.value = null;
      clearPhotoFileInput();
    },
  });
};

const clearPhotoFileInput = () => {
  if (photoInput.value?.value) {
    photoInput.value.value = null;
  }
};

// Dynamic BMI Calculation
const calculatedBmi = computed(() => {
  const heightInMeters = Number(form.tinggi_badan) / 100;
  const weight = Number(form.berat_badan);
  if (heightInMeters > 0.5 && weight > 20) {
    const bmi = weight / (heightInMeters * heightInMeters);
    return bmi.toFixed(1);
  }
  return null;
});

const bmiCategory = computed(() => {
  if (!calculatedBmi.value) return null;
  const val = Number(calculatedBmi.value);
  if (val < 18.5) return { text: 'Underweight (Kurus)', color: 'text-amber-700 bg-amber-50 border-amber-200' };
  if (val <= 24.9) return { text: 'Normal (Ideal)', color: 'text-emerald-700 bg-emerald-50 border-emerald-200' };
  if (val <= 29.9) return { text: 'Overweight (Kelebihan BB)', color: 'text-amber-700 bg-amber-50 border-amber-200' };
  return { text: 'Obesitas (Perlu Perhatian)', color: 'text-rose-700 bg-rose-50 border-rose-200' };
});

const updateEmployeeProfile = () => {
  if (photoInput.value?.files?.[0]) {
    form.photo = photoInput.value.files[0];
  }

  form.post(route('user-employee-profile.update'), {
    preserveScroll: true,
    onSuccess: () => clearPhotoFileInput(),
  });
};
</script>

<template>
  <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
    <!-- Form Card Header -->
    <div class="px-6 sm:px-8 py-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-slate-50/40">
      <div>
        <h3 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2">
          <IdCard class="size-5 text-slate-700" />
          <span>Data Umum & Medis Karyawan</span>
        </h3>
        <p class="text-xs text-slate-500 mt-1">
          Lengkapi foto profil resmi, biodata umum, pengukuran fisik, riwayat alergi, penempatan kerja, domisili, dan kontak darurat.
        </p>
      </div>

      <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-100 border border-slate-200 text-[11px] font-semibold text-slate-600 self-start sm:self-auto">
        <Sparkles class="size-3.5 text-slate-500" />
        <span>Terintegrasi Database HSE</span>
      </div>
    </div>

    <!-- Main Form Content -->
    <form @submit.prevent="updateEmployeeProfile" class="p-6 sm:p-8 space-y-8">
      <!-- ============================================================== -->
      <!-- SECTION 0: FOTO PROFIL RESMI                                   -->
      <!-- ============================================================== -->
      <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col sm:flex-row items-center gap-6">
        <input
          id="employee_photo"
          ref="photoInput"
          type="file"
          class="hidden"
          accept="image/*"
          @change="updatePhotoPreview"
        />

        <!-- Avatar Preview -->
        <div class="relative shrink-0">
          <div v-show="!photoPreview" class="size-24 rounded-2xl overflow-hidden border-2 border-white shadow-md bg-slate-800 text-white flex items-center justify-center text-3xl font-black">
            <img v-if="user?.profile_photo_url" :src="user.profile_photo_url" :alt="user.name" class="size-full object-cover" />
            <span v-else>{{ user?.name ? user.name.charAt(0).toUpperCase() : 'U' }}</span>
          </div>

          <div v-show="photoPreview" class="size-24 rounded-2xl overflow-hidden border-2 border-white shadow-md">
            <span
              class="block size-full bg-cover bg-no-repeat bg-center"
              :style="'background-image: url(\'' + photoPreview + '\');'"
            />
          </div>
        </div>

        <!-- Photo Actions & Guidance -->
        <div class="space-y-1.5 text-center sm:text-left flex-1">
          <label class="block text-xs font-bold text-slate-900">Foto Profil Resmi Pekerja</label>
          <p class="text-xs text-slate-500 leading-relaxed">
            Format file: JPG, JPEG, PNG, atau WEBP (Maksimal 2 MB). Foto akan ditampilkan di portal dan sertifikat kelaikan.
          </p>
          
          <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 pt-2">
            <button
              type="button"
              @click.prevent="selectNewPhoto"
              class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-100 hover:text-slate-900 transition shadow-2xs"
            >
              <Camera class="size-3.5 text-slate-500" />
              <span>Pilih / Ganti Foto</span>
            </button>

            <button
              v-if="user?.profile_photo_path"
              type="button"
              @click.prevent="deletePhoto"
              class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold hover:bg-rose-100 transition shadow-2xs"
            >
              <Trash2 class="size-3.5" />
              <span>Hapus Foto</span>
            </button>
          </div>
          <InputError :message="form.errors.photo" class="mt-1" />
        </div>
      </div>

      <!-- ============================================================== -->
      <!-- SECTION 1: IDENTITAS UTAMA & PENGUKURAN FISIK                  -->
      <!-- ============================================================== -->
      <div class="space-y-4">
        <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
          <User class="size-4.5 text-slate-700" />
          <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900">1. Identitas Pokok & Pengukuran Fisik</h4>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
          <!-- ID / NIK -->
          <div>
            <label for="nik" class="block text-xs font-bold text-slate-700 mb-1.5">Nomor Pekerja / NIK <span class="text-rose-500">*</span></label>
            <input
              id="nik"
              v-model="form.nik"
              type="text"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 text-xs font-mono uppercase font-bold text-slate-900 bg-white placeholder:text-slate-400"
              required
              placeholder="Contoh: PTM-001 / MGR-001"
            />
            <InputError :message="form.errors.nik" class="mt-1" />
          </div>

          <!-- Nama Lengkap -->
          <div>
            <label for="emp_name" class="block text-xs font-bold text-slate-700 mb-1.5">Nama Lengkap Pekerja <span class="text-rose-500">*</span></label>
            <input
              id="emp_name"
              v-model="form.name"
              type="text"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 text-xs font-semibold text-slate-900 bg-white placeholder:text-slate-400"
              required
              placeholder="Nama sesuai SK / KTP"
            />
            <InputError :message="form.errors.name" class="mt-1" />
          </div>

          <!-- Tanggal Lahir -->
          <div>
            <label for="tanggal_lahir" class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Lahir</label>
            <input
              id="tanggal_lahir"
              v-model="form.tanggal_lahir"
              type="date"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 text-xs text-slate-900 bg-white"
            />
            <InputError :message="form.errors.tanggal_lahir" class="mt-1" />
          </div>

          <!-- Jenis Kelamin -->
          <div>
            <label for="jenis_kelamin" class="block text-xs font-bold text-slate-700 mb-1.5">Jenis Kelamin</label>
            <select
              id="jenis_kelamin"
              v-model="form.jenis_kelamin"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 text-xs font-medium text-slate-900 bg-white"
            >
              <option value="Laki-laki">Laki-laki</option>
              <option value="Perempuan">Perempuan</option>
            </select>
            <InputError :message="form.errors.jenis_kelamin" class="mt-1" />
          </div>

          <!-- Tinggi Badan -->
          <div>
            <label for="tinggi_badan" class="block text-xs font-bold text-slate-700 mb-1.5">Tinggi Badan (cm)</label>
            <div class="relative">
              <input
                id="tinggi_badan"
                v-model="form.tinggi_badan"
                type="number"
                step="0.1"
                class="w-full px-3.5 py-2.5 pr-10 rounded-xl border border-slate-300 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 text-xs font-semibold text-slate-900 bg-white placeholder:text-slate-400"
                placeholder="170"
              />
              <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs font-bold text-slate-400">cm</span>
            </div>
            <InputError :message="form.errors.tinggi_badan" class="mt-1" />
          </div>

          <!-- Berat Badan -->
          <div>
            <label for="berat_badan" class="block text-xs font-bold text-slate-700 mb-1.5">Berat Badan (kg)</label>
            <div class="relative">
              <input
                id="berat_badan"
                v-model="form.berat_badan"
                type="number"
                step="0.1"
                class="w-full px-3.5 py-2.5 pr-10 rounded-xl border border-slate-300 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 text-xs font-semibold text-slate-900 bg-white placeholder:text-slate-400"
                placeholder="68"
              />
              <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs font-bold text-slate-400">kg</span>
            </div>
            <InputError :message="form.errors.berat_badan" class="mt-1" />
          </div>
        </div>

        <!-- Dynamic Live BMI Result Card -->
        <div v-if="calculatedBmi" class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col sm:flex-row items-center justify-between gap-3">
          <div class="flex items-center gap-3">
            <div class="p-2 rounded-xl bg-white border border-slate-200 text-slate-700 shadow-2xs">
              <Scale class="size-5" />
            </div>
            <div>
              <p class="text-xs font-bold text-slate-800">Indeks Massa Tubuh (BMI Otomatis)</p>
              <p class="text-[11px] text-slate-500">Skor: <span class="font-mono font-bold text-slate-900">{{ calculatedBmi }} kg/m²</span></p>
            </div>
          </div>
          <span class="px-3 py-1 rounded-xl text-xs font-bold border" :class="bmiCategory?.color">
            {{ bmiCategory?.text }}
          </span>
        </div>
      </div>

      <!-- ============================================================== -->
      <!-- SECTION 2: GOLONGAN DARAH & RIWAYAT ALERGI                     -->
      <!-- ============================================================== -->
      <div class="space-y-4">
        <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
          <Droplet class="size-4.5 text-slate-700" />
          <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900">2. Golongan Darah, Rhesus & Riwayat Alergi</h4>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
          <!-- Golongan Darah -->
          <div>
            <label for="golongan_darah" class="block text-xs font-bold text-slate-700 mb-1.5">Golongan Darah</label>
            <select
              id="golongan_darah"
              v-model="form.golongan_darah"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 text-xs font-bold text-slate-900 bg-white"
            >
              <option value="A">Tipe A</option>
              <option value="B">Tipe B</option>
              <option value="AB">Tipe AB</option>
              <option value="O">Tipe O</option>
            </select>
            <InputError :message="form.errors.golongan_darah" class="mt-1" />
          </div>

          <!-- Rhesus -->
          <div>
            <label for="rhesus" class="block text-xs font-bold text-slate-700 mb-1.5">Faktor Rhesus</label>
            <select
              id="rhesus"
              v-model="form.rhesus"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 text-xs font-bold text-slate-900 bg-white"
            >
              <option value="+">Positif (+)</option>
              <option value="-">Negatif (-)</option>
            </select>
            <InputError :message="form.errors.rhesus" class="mt-1" />
          </div>

          <!-- Alergi Obat -->
          <div class="sm:col-span-2 lg:col-span-1">
            <label for="alergi_obat" class="block text-xs font-bold text-slate-700 mb-1.5">Riwayat Alergi Obat</label>
            <input
              id="alergi_obat"
              v-model="form.alergi_obat"
              type="text"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 text-xs text-slate-900 bg-white placeholder:text-slate-400"
              placeholder="Contoh: Penisilin, Sulfa, Tidak ada"
            />
            <InputError :message="form.errors.alergi_obat" class="mt-1" />
          </div>

          <!-- Alergi Makanan / Lainnya -->
          <div class="sm:col-span-2 lg:col-span-1">
            <label for="alergi_makanan" class="block text-xs font-bold text-slate-700 mb-1.5">Alergi Makanan / Udara</label>
            <input
              id="alergi_makanan"
              v-model="form.alergi_makanan"
              type="text"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 text-xs text-slate-900 bg-white placeholder:text-slate-400"
              placeholder="Contoh: Seafood, Debu, Tidak ada"
            />
            <InputError :message="form.errors.alergi_makanan" class="mt-1" />
          </div>
        </div>
      </div>

      <!-- ============================================================== -->
      <!-- SECTION 3: PENEMPATAN & LOKASI PENUGASAN                       -->
      <!-- ============================================================== -->
      <div class="space-y-4">
        <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
          <Building2 class="size-4.5 text-slate-700" />
          <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900">3. Penempatan Kerja & Fungsi Organisasi</h4>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
          <!-- Distrik -->
          <div>
            <label for="distrik" class="block text-xs font-bold text-slate-700 mb-1.5">Distrik Kerja</label>
            <select
              id="distrik"
              v-model="form.distrik"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 text-xs font-medium text-slate-900 bg-white"
            >
              <option value="Distrik Prabumulih">Distrik Prabumulih</option>
              <option value="Distrik Medan">Distrik Medan</option>
              <option value="Distrik Dumai">Distrik Dumai</option>
              <option value="Distrik Balongan">Distrik Balongan</option>
              <option value="Distrik Cilacap">Distrik Cilacap</option>
              <option value="Distrik Jawa Timur">Distrik Jawa Timur</option>
              <option value="Kantor Pusat">Kantor Pusat Jakarta</option>
            </select>
            <InputError :message="form.errors.distrik" class="mt-1" />
          </div>

          <!-- Lokasi Kerja Spesifik -->
          <div>
            <label for="lokasi_kerja" class="block text-xs font-bold text-slate-700 mb-1.5">Lokasi Kerja / Site</label>
            <input
              id="lokasi_kerja"
              v-model="form.lokasi_kerja"
              type="text"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 text-xs text-slate-900 bg-white placeholder:text-slate-400"
              placeholder="Contoh: Booster Station / Metering Station"
            />
            <InputError :message="form.errors.lokasi_kerja" class="mt-1" />
          </div>

          <!-- Fungsi / Departemen -->
          <div>
            <label for="fungsi" class="block text-xs font-bold text-slate-700 mb-1.5">Fungsi / Departemen</label>
            <input
              id="fungsi"
              v-model="form.fungsi"
              type="text"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 text-xs text-slate-900 bg-white placeholder:text-slate-400"
              placeholder="Contoh: Operasi, HSSE, Maintenance"
            />
            <InputError :message="form.errors.fungsi" class="mt-1" />
          </div>

          <!-- Tipe Pekerjaan -->
          <div>
            <label for="tipe_pekerjaan" class="block text-xs font-bold text-slate-700 mb-1.5">Tipe Pekerjaan</label>
            <select
              id="tipe_pekerjaan"
              v-model="form.tipe_pekerjaan"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 text-xs font-medium text-slate-900 bg-white"
            >
              <option value="Non-Shift">Non-Shift (Regular Office)</option>
              <option value="Shift">Shift (Operasional Lapangan)</option>
            </select>
            <InputError :message="form.errors.tipe_pekerjaan" class="mt-1" />
          </div>

          <!-- No. Telepon Pekerja -->
          <div class="sm:col-span-2">
            <label for="no_telepon" class="block text-xs font-bold text-slate-700 mb-1.5">Nomor Handphone / WhatsApp</label>
            <input
              id="no_telepon"
              v-model="form.no_telepon"
              type="text"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 text-xs font-medium text-slate-900 bg-white placeholder:text-slate-400"
              placeholder="Contoh: 081234567890"
            />
            <InputError :message="form.errors.no_telepon" class="mt-1" />
          </div>
        </div>
      </div>

      <!-- ============================================================== -->
      <!-- SECTION 4: ALAMAT DOMISILI TERPISAH                            -->
      <!-- ============================================================== -->
      <div class="space-y-4">
        <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
          <Home class="size-4.5 text-slate-700" />
          <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900">4. Alamat Tempat Tinggal / Domisili Lengkap</h4>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
          <!-- Alamat Jalan -->
          <div class="sm:col-span-2 lg:col-span-4">
            <label for="alamat_jalan" class="block text-xs font-bold text-slate-700 mb-1.5">Alamat Jalan / Komplek / RT / RW</label>
            <textarea
              id="alamat_jalan"
              v-model="form.alamat_jalan"
              rows="2"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 text-xs text-slate-900 bg-white placeholder:text-slate-400"
              placeholder="Nama Jalan, Nomor Rumah, RT/RW, Kelurahan/Desa"
            ></textarea>
            <InputError :message="form.errors.alamat_jalan" class="mt-1" />
          </div>

          <!-- Kecamatan -->
          <div>
            <label for="kecamatan" class="block text-xs font-bold text-slate-700 mb-1.5">Kecamatan</label>
            <input
              id="kecamatan"
              v-model="form.kecamatan"
              type="text"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 text-xs text-slate-900 bg-white placeholder:text-slate-400"
              placeholder="Nama Kecamatan"
            />
            <InputError :message="form.errors.kecamatan" class="mt-1" />
          </div>

          <!-- Kota / Kabupaten -->
          <div>
            <label for="kabupaten_kota" class="block text-xs font-bold text-slate-700 mb-1.5">Kota / Kabupaten</label>
            <input
              id="kabupaten_kota"
              v-model="form.kabupaten_kota"
              type="text"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 text-xs text-slate-900 bg-white placeholder:text-slate-400"
              placeholder="Contoh: Kota Prabumulih / Palembang"
            />
            <InputError :message="form.errors.kabupaten_kota" class="mt-1" />
          </div>

          <!-- Provinsi -->
          <div>
            <label for="provinsi" class="block text-xs font-bold text-slate-700 mb-1.5">Provinsi</label>
            <input
              id="provinsi"
              v-model="form.provinsi"
              type="text"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 text-xs text-slate-900 bg-white placeholder:text-slate-400"
              placeholder="Provinsi"
            />
            <InputError :message="form.errors.provinsi" class="mt-1" />
          </div>

          <!-- Kode Pos -->
          <div>
            <label for="kode_pos" class="block text-xs font-bold text-slate-700 mb-1.5">Kode Pos</label>
            <input
              id="kode_pos"
              v-model="form.kode_pos"
              type="text"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 text-xs font-mono text-slate-900 bg-white placeholder:text-slate-400"
              placeholder="31111"
            />
            <InputError :message="form.errors.kode_pos" class="mt-1" />
          </div>
        </div>
      </div>

      <!-- ============================================================== -->
      <!-- SECTION 5: KONTAK DARURAT & JAMINAN SOSIAL BPJS               -->
      <!-- ============================================================== -->
      <div class="space-y-4">
        <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
          <HeartHandshake class="size-4.5 text-slate-700" />
          <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900">5. Kontak Darurat & Jaminan Sosial (BPJS)</h4>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
          <!-- Nama Kontak Darurat -->
          <div>
            <label for="nama_kontak_darurat" class="block text-xs font-bold text-slate-700 mb-1.5">Nama Kontak Darurat</label>
            <input
              id="nama_kontak_darurat"
              v-model="form.nama_kontak_darurat"
              type="text"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 text-xs text-slate-900 bg-white placeholder:text-slate-400"
              placeholder="Nama Lengkap Kerabat"
            />
            <InputError :message="form.errors.nama_kontak_darurat" class="mt-1" />
          </div>

          <!-- Hubungan Kontak Darurat -->
          <div>
            <label for="hubungan_kontak_darurat" class="block text-xs font-bold text-slate-700 mb-1.5">Hubungan / Relasi</label>
            <select
              id="hubungan_kontak_darurat"
              v-model="form.hubungan_kontak_darurat"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 text-xs font-medium text-slate-900 bg-white"
            >
              <option value="Suami/Istri">Suami / Istri</option>
              <option value="Orang Tua">Orang Tua (Ayah/Ibu)</option>
              <option value="Anak">Anak</option>
              <option value="Saudara Kandung">Saudara Kandung</option>
              <option value="Kerabat Lain">Kerabat Lain</option>
            </select>
            <InputError :message="form.errors.hubungan_kontak_darurat" class="mt-1" />
          </div>

          <!-- Nomor Telepon Darurat -->
          <div>
            <label for="no_telepon_darurat" class="block text-xs font-bold text-slate-700 mb-1.5">No. Telepon Darurat</label>
            <input
              id="no_telepon_darurat"
              v-model="form.no_telepon_darurat"
              type="text"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 text-xs text-slate-900 bg-white placeholder:text-slate-400"
              placeholder="Contoh: 081298765432"
            />
            <InputError :message="form.errors.no_telepon_darurat" class="mt-1" />
          </div>

          <!-- No. BPJS Kesehatan -->
          <div class="sm:col-span-1.5">
            <label for="bpjs_kesehatan" class="block text-xs font-bold text-slate-700 mb-1.5">Nomor BPJS Kesehatan</label>
            <input
              id="bpjs_kesehatan"
              v-model="form.bpjs_kesehatan"
              type="text"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 text-xs font-mono text-slate-900 bg-white placeholder:text-slate-400"
              placeholder="0001234567890"
            />
            <InputError :message="form.errors.bpjs_kesehatan" class="mt-1" />
          </div>

          <!-- No. BPJS Ketenagakerjaan -->
          <div class="sm:col-span-1.5">
            <label for="bpjs_ketenagakerjaan" class="block text-xs font-bold text-slate-700 mb-1.5">Nomor BPJS Ketenagakerjaan</label>
            <input
              id="bpjs_ketenagakerjaan"
              v-model="form.bpjs_ketenagakerjaan"
              type="text"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 text-xs font-mono text-slate-900 bg-white placeholder:text-slate-400"
              placeholder="12345678901"
            />
            <InputError :message="form.errors.bpjs_ketenagakerjaan" class="mt-1" />
          </div>
        </div>
      </div>

      <!-- ============================================================== -->
      <!-- FOOTER ACTIONS                                                 -->
      <!-- ============================================================== -->
      <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2">
          <ActionMessage :on="form.recentlySuccessful">
            <div class="flex items-center gap-1.5 text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-200">
              <CheckCircle2 class="size-4" />
              <span>Data Karyawan Berhasil Disimpan</span>
            </div>
          </ActionMessage>
        </div>

        <button
          type="submit"
          :disabled="form.processing"
          class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-xs transition duration-150 disabled:opacity-50"
        >
          <Save class="size-4" />
          <span>{{ form.processing ? 'Menyimpan Perubahan...' : 'Simpan Data Karyawan' }}</span>
        </button>
      </div>
    </form>
  </div>
</template>
