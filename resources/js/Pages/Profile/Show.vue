<script setup>
import { ref, computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue';
import LogoutOtherBrowserSessionsForm from '@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue';
import TwoFactorAuthenticationForm from '@/Pages/Profile/Partials/TwoFactorAuthenticationForm.vue';
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';
import UpdateEmployeeInformationForm from '@/Pages/Profile/Partials/UpdateEmployeeInformationForm.vue';
import {
  User,
  ShieldCheck,
  KeyRound,
  Laptop,
  IdCard,
  Building2,
  Mail,
  Phone,
  CheckCircle2,
  AlertCircle,
  Clock,
  HeartPulse,
  Activity,
  Droplet
} from '@lucide/vue';

const props = defineProps({
  confirmsTwoFactorAuthentication: Boolean,
  sessions: Array,
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
const employee = computed(() => user.value?.employee || {});

// Active Tab
const activeTab = ref('employee_info');

const roleLabel = computed(() => {
  switch (user.value?.role) {
    case 'admin':
      return 'Administrator';
    case 'manager':
      return 'Manager HSE';
    case 'hse':
      return 'HSE Officer';
    case 'doctor':
      return 'Dokter Perusahaan';
    case 'employee':
      return 'Karyawan';
    default:
      return 'Pengguna Portal';
  }
});

const roleBadgeClass = computed(() => {
  switch (user.value?.role) {
    case 'admin':
      return 'bg-purple-50 text-purple-700 border-purple-200';
    case 'manager':
      return 'bg-slate-100 text-slate-800 border-slate-300 font-semibold';
    case 'hse':
      return 'bg-emerald-50 text-emerald-700 border-emerald-200';
    case 'doctor':
      return 'bg-blue-50 text-blue-700 border-blue-200';
    default:
      return 'bg-slate-100 text-slate-700 border-slate-200';
  }
});
</script>

<template>
  <AppLayout title="Profil & Pengaturan Akun">
    <div class="max-w-6xl mx-auto space-y-6 pb-12">
      <!-- ============================================================== -->
      <!-- TOP HERO CARD: USER PROFILE SUMMARY                            -->
      <!-- ============================================================== -->
      <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-xs relative overflow-hidden">
        <!-- Subtle Background Decorative Gradient -->
        <div class="absolute -right-20 -top-20 size-72 rounded-full bg-slate-100/60 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row items-center md:items-start gap-6">
          <!-- Profile Avatar with Ring -->
          <div class="relative shrink-0">
            <div class="size-24 sm:size-28 rounded-3xl bg-slate-800 text-white flex items-center justify-center font-bold text-3xl shadow-sm overflow-hidden border-4 border-slate-100">
              <img
                v-if="user?.profile_photo_url"
                :src="user.profile_photo_url"
                :alt="user.name"
                class="size-full object-cover"
              />
              <span v-else>{{ user?.name ? user.name.charAt(0).toUpperCase() : 'U' }}</span>
            </div>
            <!-- Online status green dot -->
            <span class="size-4 rounded-full bg-emerald-500 absolute -bottom-1 -right-1 border-2 border-white shadow-xs"></span>
          </div>

          <!-- User Identity Details -->
          <div class="flex-1 text-center md:text-left min-w-0">
            <div class="flex flex-wrap items-center justify-center md:justify-start gap-2.5">
              <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight truncate">
                {{ user?.name }}
              </h2>
              <span class="px-2.5 py-0.5 text-xs font-bold rounded-lg border uppercase" :class="roleBadgeClass">
                {{ roleLabel }}
              </span>
              <span v-if="employee?.nik" class="px-2.5 py-0.5 text-xs font-mono font-bold rounded-lg bg-slate-100 text-slate-700 border border-slate-200">
                NIK: {{ employee.nik }}
              </span>
            </div>

            <!-- Meta Information Row -->
            <div class="mt-3 flex flex-wrap items-center justify-center md:justify-start gap-y-2 gap-x-5 text-xs text-slate-500 font-medium">
              <div class="flex items-center gap-1.5">
                <Mail class="size-4 text-slate-400" />
                <span>{{ user?.email }}</span>
              </div>
              <div v-if="employee?.no_telepon" class="flex items-center gap-1.5">
                <Phone class="size-4 text-slate-400" />
                <span>{{ employee.no_telepon }}</span>
              </div>
              <div v-if="employee?.distrik" class="flex items-center gap-1.5">
                <Building2 class="size-4 text-slate-400" />
                <span>{{ employee.distrik }}</span>
              </div>
              <div v-if="employee?.golongan_darah" class="flex items-center gap-1.5">
                <Droplet class="size-4 text-slate-400" />
                <span>Gol. Darah: <strong>{{ employee.golongan_darah }}{{ employee.rhesus || '+' }}</strong></span>
              </div>
            </div>

            <!-- Quick Status Badges -->
            <div class="mt-4 pt-4 border-t border-slate-100 flex flex-wrap items-center justify-center md:justify-start gap-3">
              <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold">
                <CheckCircle2 class="size-3.5" />
                <span>Akun Aktif & Terverifikasi</span>
              </div>
              <div v-if="employee?.fungsi" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 border border-slate-200 text-slate-700 text-xs font-medium">
                <Activity class="size-3.5 text-slate-400" />
                <span>Fungsi: {{ employee.fungsi }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ============================================================== -->
      <!-- TAB NAVIGATION CONTROLS                                         -->
      <!-- ============================================================== -->
      <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
        <button
          @click="activeTab = 'employee_info'"
          class="flex items-center gap-2 px-5 py-3 rounded-2xl text-xs sm:text-sm font-bold transition-all duration-200 whitespace-nowrap shadow-xs"
          :class="activeTab === 'employee_info'
            ? 'bg-slate-900 text-white shadow-sm'
            : 'bg-white text-slate-600 hover:text-slate-900 hover:bg-slate-100/80 border border-slate-200/80'"
        >
          <IdCard class="size-4" />
          <span>Data Karyawan & Medis</span>
        </button>

        <button
          @click="activeTab = 'account_info'"
          class="flex items-center gap-2 px-5 py-3 rounded-2xl text-xs sm:text-sm font-bold transition-all duration-200 whitespace-nowrap shadow-xs"
          :class="activeTab === 'account_info'
            ? 'bg-slate-900 text-white shadow-sm'
            : 'bg-white text-slate-600 hover:text-slate-900 hover:bg-slate-100/80 border border-slate-200/80'"
        >
          <User class="size-4" />
          <span>Akun & Email</span>
        </button>

        <button
          @click="activeTab = 'security'"
          class="flex items-center gap-2 px-5 py-3 rounded-2xl text-xs sm:text-sm font-bold transition-all duration-200 whitespace-nowrap shadow-xs"
          :class="activeTab === 'security'
            ? 'bg-slate-900 text-white shadow-sm'
            : 'bg-white text-slate-600 hover:text-slate-900 hover:bg-slate-100/80 border border-slate-200/80'"
        >
          <KeyRound class="size-4" />
          <span>Keamanan & Password</span>
        </button>

        <button
          @click="activeTab = 'sessions'"
          class="flex items-center gap-2 px-5 py-3 rounded-2xl text-xs sm:text-sm font-bold transition-all duration-200 whitespace-nowrap shadow-xs"
          :class="activeTab === 'sessions'
            ? 'bg-slate-900 text-white shadow-sm'
            : 'bg-white text-slate-600 hover:text-slate-900 hover:bg-slate-100/80 border border-slate-200/80'"
        >
          <Laptop class="size-4" />
          <span>Sesi Perangkat</span>
        </button>
      </div>

      <!-- ============================================================== -->
      <!-- TAB CONTENTS                                                   -->
      <!-- ============================================================== -->

      <!-- TAB 1: DATA KARYAWAN & MEDIS -->
      <div v-show="activeTab === 'employee_info'" class="space-y-6">
        <UpdateEmployeeInformationForm :user="user" />
      </div>

      <!-- TAB 2: AKUN & EMAIL LOGIN -->
      <div v-show="activeTab === 'account_info'" class="space-y-6">
        <UpdateProfileInformationForm :user="user" />
      </div>

      <!-- TAB 3: KEAMANAN & PASSWORD -->
      <div v-show="activeTab === 'security'" class="space-y-6">
        <UpdatePasswordForm />

        <div v-if="$page.props.jetstream.canManageTwoFactorAuthentication">
          <TwoFactorAuthenticationForm :requires-confirmation="confirmsTwoFactorAuthentication" />
        </div>
      </div>

      <!-- TAB 4: SESI LOGIN & PERANGKAT AKTIF -->
      <div v-show="activeTab === 'sessions'" class="space-y-6">
        <LogoutOtherBrowserSessionsForm :sessions="sessions" />

        <div v-if="$page.props.jetstream.hasAccountDeletionFeatures">
          <DeleteUserForm />
        </div>
      </div>
    </div>
  </AppLayout>
</template>
