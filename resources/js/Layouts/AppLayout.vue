<script setup>
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue';
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import {
  LayoutDashboard,
  Users,
  FileHeart,
  AlertTriangle,
  UserCheck,
  ShieldCheck,
  LogOut,
  ChevronLeft,
  ChevronRight,
  ChevronDown,
  Menu,
  X,
  User as UserIcon,
  HeartPulse,
  Settings,
  Building2,
  ExternalLink,
  Shield,
  ClipboardCheck,
  Search,
  Bell,
  Calendar,
  Clock,
  Home,
  SlidersHorizontal,
  KeyRound,
  Activity,
  FileText,
  Sparkles,
  ChevronRight as ChevronRightIcon,
  CheckCircle2,
  Lock,
  Eye,
  Stethoscope,
  Info
} from '@lucide/vue';

const props = defineProps({
  title: String,
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
const flash = computed(() => page.props.flash);

// Sidebar Collapse State with localStorage Persistence
const isCollapsed = ref(false);
const mobileMenuOpen = ref(false);
const userDropdownOpen = ref(false);
const searchModalOpen = ref(false);
const searchQuery = ref('');
const searchInputRef = ref(null);

watch(searchModalOpen, async (isOpen) => {
  if (isOpen) {
    searchQuery.value = '';
    await nextTick();
    searchInputRef.value?.focus();
  }
});

// Real-time Indonesian Clock & Date
const currentDateTime = ref('');
let clockTimer = null;

const updateClock = () => {
  const now = new Date();
  const options = {
    weekday: 'short',
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    hour12: false,
    timeZone: 'Asia/Jakarta'
  };
  currentDateTime.value = new Intl.DateTimeFormat('id-ID', options).format(now) + ' WIB';
};

const toggleSidebar = () => {
  isCollapsed.value = !isCollapsed.value;
  try {
    localStorage.setItem('hse_sidebar_collapsed', isCollapsed.value ? 'true' : 'false');
  } catch (e) {
    // Ignore storage quota error
  }
};

const toggleUserDropdown = () => {
  userDropdownOpen.value = !userDropdownOpen.value;
};

const closeUserDropdown = () => {
  userDropdownOpen.value = false;
};

// Close dropdown on click outside
const handleClickOutside = (event) => {
  const dropdownElement = document.getElementById('user-profile-dropdown-container');
  if (dropdownElement && !dropdownElement.contains(event.target)) {
    userDropdownOpen.value = false;
  }
};

// Keyboard shortcut (Ctrl+K or Cmd+K for quick search)
const handleKeyDown = (e) => {
  if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
    e.preventDefault();
    searchModalOpen.value = !searchModalOpen.value;
  } else if (e.key === 'Escape' && searchModalOpen.value) {
    searchModalOpen.value = false;
  }
};

onMounted(() => {
  try {
    const savedState = localStorage.getItem('hse_sidebar_collapsed');
    if (savedState !== null) {
      isCollapsed.value = savedState === 'true';
    }
  } catch (e) {}

  updateClock();
  clockTimer = setInterval(updateClock, 10000);

  document.addEventListener('click', handleClickOutside);
  window.addEventListener('keydown', handleKeyDown);
});

onUnmounted(() => {
  if (clockTimer) clearInterval(clockTimer);
  document.removeEventListener('click', handleClickOutside);
  window.removeEventListener('keydown', handleKeyDown);
});

const logout = () => {
  router.post(route('logout'));
};

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
      return 'bg-violet-50 text-violet-700 border-violet-200';
    case 'manager':
      return 'bg-blue-50 text-blue-700 border-blue-200';
    case 'hse':
      return 'bg-emerald-50 text-emerald-700 border-emerald-200';
    case 'doctor':
      return 'bg-cyan-50 text-cyan-700 border-cyan-200';
    default:
      return 'bg-slate-100 text-slate-600 border-slate-200';
  }
});

// Dynamic Intelligent Breadcrumbs based on route and title
const breadcrumbs = computed(() => {
  const currentRouteName = route().current() || '';
  const crumbs = [
    { label: 'Dashboard', routeName: 'dashboard', icon: Home, isHome: true }
  ];

  if (currentRouteName === 'dashboard') {
    return [{ label: 'Dashboard', routeName: 'dashboard', icon: Home, isHome: true, active: true }];
  }

  // Employees
  if (currentRouteName.startsWith('employees.')) {
    crumbs.push({ label: 'Data Karyawan', routeName: 'employees.index' });
    if (currentRouteName === 'employees.show') {
      crumbs.push({ label: props.title || 'Detail Profil Karyawan', active: true });
    } else {
      crumbs[crumbs.length - 1].active = true;
    }
    return crumbs;
  }

  // MCU
  if (currentRouteName.startsWith('mcu.')) {
    crumbs.push({ label: 'Data MCU Karyawan', routeName: 'mcu.index' });
    if (currentRouteName === 'mcu.create') {
      crumbs.push({ label: 'Input Data MCU Baru', active: true });
    } else if (currentRouteName === 'mcu.edit') {
      crumbs.push({ label: 'Edit MCU', active: true });
    } else if (currentRouteName === 'mcu.show') {
      crumbs.push({ label: props.title || 'Detail Hasil MCU', active: true });
    } else {
      crumbs[crumbs.length - 1].active = true;
    }
    return crumbs;
  }

  // Daily Check Up
  if (currentRouteName.startsWith('daily-check-ups.')) {
    crumbs.push({ label: 'Daily Check Up (DCU)', routeName: 'daily-check-ups.index' });
    if (currentRouteName === 'daily-check-ups.create') {
      crumbs.push({ label: 'Input Pemeriksaan Harian', active: true });
    } else if (currentRouteName === 'daily-check-ups.edit') {
      crumbs.push({ label: 'Edit Daily Check Up', active: true });
    } else if (currentRouteName === 'daily-check-ups.show') {
      crumbs.push({ label: props.title || 'Detail Pemeriksaan', active: true });
    } else {
      crumbs[crumbs.length - 1].active = true;
    }
    return crumbs;
  }

  // Health Alerts
  if (currentRouteName.startsWith('health-alerts.')) {
    crumbs.push({ label: 'Health Alerts', routeName: 'health-alerts.index' });
    if (currentRouteName === 'health-alerts.show') {
      crumbs.push({ label: props.title || 'Detail Alert & Tindak Lanjut', active: true });
    } else {
      crumbs[crumbs.length - 1].active = true;
    }
    return crumbs;
  }

  // Users Admin
  if (currentRouteName.startsWith('users.')) {
    crumbs.push({ label: 'Sistem & Administrasi' });
    crumbs.push({ label: 'User & Hak Akses', routeName: 'users.index', active: true });
    return crumbs;
  }

  // Audit Logs
  if (currentRouteName.startsWith('audit-logs.')) {
    crumbs.push({ label: 'Sistem & Administrasi' });
    crumbs.push({ label: 'Audit Trail Logs', routeName: 'audit-logs.index', active: true });
    return crumbs;
  }

  // Profile Show
  if (currentRouteName.startsWith('profile.')) {
    crumbs.push({ label: 'Pengaturan Akun', routeName: 'profile.show', active: true });
    return crumbs;
  }

  // Fallback
  if (props.title) {
    crumbs.push({ label: props.title, active: true });
  }

  return crumbs;
});

// Quick Searchable Navigation Index with distinctive corporate colored themes
const quickSearchNavigation = [
  {
    title: 'Dashboard Overview',
    desc: 'Statistik & Indikator Kesehatan Kerja',
    routeName: 'dashboard',
    icon: LayoutDashboard,
    category: 'Menu Utama',
    iconBg: 'bg-blue-50 text-blue-600 border-blue-100/90',
    badgeClass: 'bg-blue-50 text-blue-700 border-blue-200/70',
  },
  {
    title: 'Data MCU Karyawan',
    desc: 'Rekam Medis & Pemeriksaan Berkala',
    routeName: 'mcu.index',
    icon: FileHeart,
    category: 'Kesehatan',
    roles: ['admin', 'manager', 'hse', 'doctor'],
    iconBg: 'bg-emerald-50 text-emerald-600 border-emerald-100/90',
    badgeClass: 'bg-emerald-50 text-emerald-700 border-emerald-200/70',
  },
  {
    title: 'Input MCU Baru',
    desc: 'Form Input Data Medical Check Up',
    routeName: 'mcu.create',
    icon: Activity,
    category: 'Kesehatan',
    roles: ['admin', 'manager', 'hse', 'doctor'],
    iconBg: 'bg-sky-50 text-sky-600 border-sky-100/90',
    badgeClass: 'bg-sky-50 text-sky-700 border-sky-200/70',
  },
  {
    title: 'Daily Check Up (DCU)',
    desc: 'Pemeriksaan Kesehatan Harian Fit to Work',
    routeName: 'daily-check-ups.index',
    icon: ClipboardCheck,
    category: 'Kesehatan',
    iconBg: 'bg-violet-50 text-violet-600 border-violet-100/90',
    badgeClass: 'bg-violet-50 text-violet-700 border-violet-200/70',
  },
  {
    title: 'Input DCU Baru',
    desc: 'Form Pemeriksaan Fit to Work Harian',
    routeName: 'daily-check-ups.create',
    icon: Stethoscope,
    category: 'Kesehatan',
    roles: ['admin', 'manager', 'hse', 'doctor'],
    iconBg: 'bg-rose-50 text-rose-600 border-rose-100/90',
    badgeClass: 'bg-rose-50 text-rose-700 border-rose-200/70',
  },
  {
    title: 'Data Karyawan',
    desc: 'Daftar Karyawan, NIK & Informasi Kerja',
    routeName: 'employees.index',
    icon: Users,
    category: 'Karyawan',
    roles: ['admin', 'manager', 'hse', 'doctor'],
    iconBg: 'bg-indigo-50 text-indigo-600 border-indigo-100/90',
    badgeClass: 'bg-indigo-50 text-indigo-700 border-indigo-200/70',
  },
  {
    title: 'Health Alerts & Monitoring',
    desc: 'Pantauan Karyawan Butuh Perhatian Medis',
    routeName: 'health-alerts.index',
    icon: AlertTriangle,
    category: 'Monitoring',
    iconBg: 'bg-amber-50 text-amber-600 border-amber-100/90',
    badgeClass: 'bg-amber-50 text-amber-700 border-amber-200/70',
  },
  {
    title: 'Profil & Pengaturan Akun',
    desc: 'Biodata Karyawan, Password & Keamanan',
    routeName: 'profile.show',
    icon: Settings,
    category: 'Pengaturan',
    iconBg: 'bg-slate-100 text-slate-600 border-slate-200/90',
    badgeClass: 'bg-slate-100 text-slate-700 border-slate-200/70',
  },
  {
    title: 'User & Role Management',
    desc: 'Kelola Hak Akses Pengguna & Role',
    routeName: 'users.index',
    icon: UserCheck,
    category: 'Admin',
    roles: ['admin'],
    iconBg: 'bg-purple-50 text-purple-600 border-purple-100/90',
    badgeClass: 'bg-purple-50 text-purple-700 border-purple-200/70',
  },
  {
    title: 'Audit Trail System',
    desc: 'Log Aktivitas & Audit Keamanan Sistem',
    routeName: 'audit-logs.index',
    icon: ShieldCheck,
    category: 'Admin',
    roles: ['admin'],
    iconBg: 'bg-teal-50 text-teal-600 border-teal-100/90',
    badgeClass: 'bg-teal-50 text-teal-700 border-teal-200/70',
  },
];

const filteredSearchResults = computed(() => {
  const q = searchQuery.value.trim().toLowerCase();
  if (!q) return quickSearchNavigation.filter(item => !item.roles || item.roles.includes(user.value?.role));
  return quickSearchNavigation.filter(item => {
    const hasRole = !item.roles || item.roles.includes(user.value?.role);
    const matchesQuery = item.title.toLowerCase().includes(q) || item.desc.toLowerCase().includes(q) || item.category.toLowerCase().includes(q);
    return hasRole && matchesQuery;
  });
});
</script>

<template>
  <div class="min-h-screen bg-gray-50 text-slate-800 font-sans flex flex-col antialiased selection:bg-blue-600 selection:text-white">
    <Head :title="title ? `${title} — HSE Pertamina Gas` : 'HSE Pertamina Gas'" />

    <!-- ============================================================== -->
    <!-- FLASH TOAST NOTIFICATIONS                                       -->
    <!-- ============================================================== -->
    <div class="fixed top-5 right-5 z-50 flex flex-col gap-2 max-w-sm pointer-events-none">
      <Transition
        enter-active-class="transition duration-300 ease-out transform"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition duration-200 ease-in transform"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-if="flash?.success" class="pointer-events-auto bg-white text-slate-800 px-4 py-3.5 rounded-xl shadow-lg border border-emerald-200 flex items-center justify-between gap-3 text-sm">
          <div class="flex items-center gap-3">
            <div class="size-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
              <CheckCircle2 class="size-4.5" />
            </div>
            <div>
              <p class="font-bold text-xs text-slate-900">Berhasil</p>
              <p class="font-medium text-[11px] text-slate-600 mt-0.5">{{ flash.success }}</p>
            </div>
          </div>
          <button @click="flash.success = null" class="text-slate-400 hover:text-slate-600 transition p-1">&times;</button>
        </div>
      </Transition>

      <Transition
        enter-active-class="transition duration-300 ease-out transform"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition duration-200 ease-in transform"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-if="flash?.error" class="pointer-events-auto bg-white text-slate-800 px-4 py-3.5 rounded-xl shadow-lg border border-amber-200 flex items-center justify-between gap-3 text-sm">
          <div class="flex items-center gap-3">
            <div class="size-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
              <AlertTriangle class="size-4.5" />
            </div>
            <div>
              <p class="font-bold text-xs text-slate-900">Pemberitahuan</p>
              <p class="font-medium text-[11px] text-slate-600 mt-0.5">{{ flash.error }}</p>
            </div>
          </div>
          <button @click="flash.error = null" class="text-slate-400 hover:text-slate-600 transition p-1">&times;</button>
        </div>
      </Transition>
    </div>

    <!-- ============================================================== -->
    <!-- MAIN APP SHELL                                                  -->
    <!-- ============================================================== -->
    <div class="flex flex-1 overflow-hidden">
      <!-- ============================================================== -->
      <!-- DESKTOP SIDEBAR                                                 -->
      <!-- ============================================================== -->
      <aside
        class="hidden md:flex flex-col bg-white border-r border-slate-200/80 transition-all duration-300 ease-in-out relative z-30 shrink-0 select-none"
        :class="isCollapsed ? 'w-[72px]' : 'w-[260px]'"
      >
        <!-- Top Logo Area -->
        <div
          class="h-16 flex items-center border-b border-slate-100/90 relative"
          :class="isCollapsed ? 'justify-center px-3' : 'justify-between px-4 sm:px-5'"
        >
          <!-- Expanded Logo -->
          <Link v-if="!isCollapsed" :href="route('dashboard')" class="flex items-center gap-2.5 group overflow-hidden">
            <img
              src="/images/Pertamina Gas Corporate Logo.png"
              alt="Pertamina Gas"
              class="h-7 w-auto object-contain shrink-0 group-hover:opacity-90 transition-opacity"
            />
            <div class="leading-none overflow-hidden whitespace-nowrap pl-2.5 border-l border-slate-200">
              <span class="font-bold text-slate-900 tracking-tight text-xs block uppercase">HSE Health</span>
              <span class="text-[9px] font-medium text-slate-400 tracking-widest uppercase block mt-1">Portal Sistem</span>
            </div>
          </Link>

          <!-- Collapsed Logo -->
          <Link v-else :href="route('dashboard')" class="p-1.5 rounded-xl hover:bg-slate-100 transition-colors flex items-center justify-center group" title="HSE Pertamina Gas">
            <img
              src="/images/logo.png"
              alt="Pertamina Gas"
              class="h-8 w-8 object-contain transition-transform duration-200 group-hover:scale-105"
            />
          </Link>

          <!-- Collapse Toggle Button -->
          <button
            v-if="!isCollapsed"
            @click="toggleSidebar"
            class="size-7 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100/80 transition-colors"
            title="Ciutkan Sidebar"
          >
            <ChevronLeft class="size-4" />
          </button>
        </div>

        <!-- Collapsed Expand Button -->
        <div v-if="isCollapsed" class="py-2 flex justify-center border-b border-slate-100">
          <button
            @click="toggleSidebar"
            class="size-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition"
            title="Buka Sidebar"
          >
            <ChevronRight class="size-4" />
          </button>
        </div>

        <!-- Navigation Menu Links -->
        <nav class="flex-1 py-3 px-3 space-y-1 overflow-y-auto overflow-x-hidden sidebar-scrollbar">
          <!-- SECTION: MENU UTAMA -->
          <div v-if="!isCollapsed" class="px-3 pt-3 pb-1 text-[10px] font-bold tracking-wider text-slate-400 uppercase">
            Menu Utama
          </div>
          <div v-else class="my-2 border-t border-slate-100 mx-2"></div>

          <!-- Dashboard -->
          <Link
            :href="route('dashboard')"
            class="flex items-center rounded-lg text-[13px] transition-all duration-150 group"
            :class="[
              route().current('dashboard')
                ? 'bg-blue-50/90 text-blue-700 font-semibold border border-blue-100/90 shadow-2xs'
                : 'text-slate-600 hover:bg-slate-100/70 hover:text-slate-900 border border-transparent font-medium',
              isCollapsed ? 'justify-center p-2.5' : 'gap-3 px-3 py-2'
            ]"
            :title="isCollapsed ? 'Dashboard' : ''"
          >
            <LayoutDashboard
              class="size-[18px] shrink-0 transition-colors"
              :class="route().current('dashboard') ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600'"
            />
            <span v-if="!isCollapsed" class="truncate">Dashboard</span>
          </Link>

          <!-- SECTION: KESEHATAN KERJA -->
          <div v-if="!isCollapsed" class="px-3 pt-4 pb-1 text-[10px] font-bold tracking-wider text-slate-400 uppercase">
            Kesehatan Kerja
          </div>
          <div v-else class="my-2 border-t border-slate-100 mx-2"></div>

          <!-- Data MCU Karyawan -->
          <Link
            v-if="user?.role !== 'employee'"
            :href="route('mcu.index')"
            class="flex items-center rounded-lg text-[13px] transition-all duration-150 group"
            :class="[
              route().current('mcu.*')
                ? 'bg-blue-50/90 text-blue-700 font-semibold border border-blue-100/90 shadow-2xs'
                : 'text-slate-600 hover:bg-slate-100/70 hover:text-slate-900 border border-transparent font-medium',
              isCollapsed ? 'justify-center p-2.5' : 'gap-3 px-3 py-2'
            ]"
            :title="isCollapsed ? 'Data MCU Karyawan' : ''"
          >
            <FileHeart
              class="size-[18px] shrink-0 transition-colors"
              :class="route().current('mcu.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600'"
            />
            <span v-if="!isCollapsed" class="truncate">Data MCU Karyawan</span>
          </Link>

          <!-- Daily Check Up -->
          <Link
            :href="route('daily-check-ups.index')"
            class="flex items-center rounded-lg text-[13px] transition-all duration-150 group"
            :class="[
              route().current('daily-check-ups.*')
                ? 'bg-blue-50/90 text-blue-700 font-semibold border border-blue-100/90 shadow-2xs'
                : 'text-slate-600 hover:bg-slate-100/70 hover:text-slate-900 border border-transparent font-medium',
              isCollapsed ? 'justify-center p-2.5' : 'gap-3 px-3 py-2'
            ]"
            :title="isCollapsed ? 'Daily Check Up (Fit to Work)' : ''"
          >
            <ClipboardCheck
              class="size-[18px] shrink-0 transition-colors"
              :class="route().current('daily-check-ups.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600'"
            />
            <div v-if="!isCollapsed" class="flex items-center justify-between flex-1 min-w-0">
              <span class="truncate">Daily Check Up</span>
              <span
                class="text-[10px] font-medium px-2 py-0.5 rounded-full border transition-colors"
                :class="route().current('daily-check-ups.*') ? 'bg-blue-100 text-blue-700 border-blue-200/80 font-semibold' : 'bg-slate-100 text-slate-500 border-slate-200/60'"
              >
                Harian
              </span>
            </div>
          </Link>

          <!-- Data Karyawan -->
          <Link
            v-if="user?.role !== 'employee'"
            :href="route('employees.index')"
            class="flex items-center rounded-lg text-[13px] transition-all duration-150 group"
            :class="[
              route().current('employees.*')
                ? 'bg-blue-50/90 text-blue-700 font-semibold border border-blue-100/90 shadow-2xs'
                : 'text-slate-600 hover:bg-slate-100/70 hover:text-slate-900 border border-transparent font-medium',
              isCollapsed ? 'justify-center p-2.5' : 'gap-3 px-3 py-2'
            ]"
            :title="isCollapsed ? 'Data Karyawan' : ''"
          >
            <Users
              class="size-[18px] shrink-0 transition-colors"
              :class="route().current('employees.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600'"
            />
            <span v-if="!isCollapsed" class="truncate">Data Karyawan</span>
          </Link>

          <!-- Health Alerts -->
          <Link
            :href="route('health-alerts.index')"
            class="flex items-center rounded-lg text-[13px] transition-all duration-150 group"
            :class="[
              route().current('health-alerts.*')
                ? 'bg-amber-50/90 text-amber-800 font-semibold border border-amber-200/80 shadow-2xs'
                : 'text-slate-600 hover:bg-slate-100/70 hover:text-slate-900 border border-transparent font-medium',
              isCollapsed ? 'justify-center p-2.5' : 'gap-3 px-3 py-2'
            ]"
            :title="isCollapsed ? 'Health Alerts & Monitoring' : ''"
          >
            <AlertTriangle
              class="size-[18px] shrink-0 transition-colors"
              :class="route().current('health-alerts.*') ? 'text-amber-500' : 'text-slate-400 group-hover:text-slate-600'"
            />
            <div v-if="!isCollapsed" class="flex items-center justify-between flex-1 min-w-0">
              <span class="truncate">Health Alerts</span>
              <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
              </span>
            </div>
          </Link>

          <!-- Employee Personal Profile -->
          <Link
            v-if="user?.role === 'employee' && user?.employee_id"
            :href="route('employees.show', user.employee_id)"
            class="flex items-center rounded-lg text-[13px] transition-all duration-150 group"
            :class="[
              route().current('employees.show')
                ? 'bg-blue-50/90 text-blue-700 font-semibold border border-blue-100/90 shadow-2xs'
                : 'text-slate-600 hover:bg-slate-100/70 hover:text-slate-900 border border-transparent font-medium',
              isCollapsed ? 'justify-center p-2.5' : 'gap-3 px-3 py-2'
            ]"
            :title="isCollapsed ? 'Profil Kesehatan Saya' : ''"
          >
            <UserIcon
              class="size-[18px] shrink-0 transition-colors"
              :class="route().current('employees.show') ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600'"
            />
            <span v-if="!isCollapsed" class="truncate">Profil Kesehatan Saya</span>
          </Link>

          <!-- SECTION: ADMINISTRASI (ADMIN ONLY) -->
          <template v-if="user?.role === 'admin'">
            <div v-if="!isCollapsed" class="px-3 pt-4 pb-1 text-[10px] font-bold tracking-wider text-slate-400 uppercase">
              Administrasi
            </div>
            <div v-else class="my-2 border-t border-slate-100 mx-2"></div>

            <Link
              :href="route('users.index')"
              class="flex items-center rounded-lg text-[13px] transition-all duration-150 group"
              :class="[
                route().current('users.*')
                  ? 'bg-blue-50/90 text-blue-700 font-semibold border border-blue-100/90 shadow-2xs'
                  : 'text-slate-600 hover:bg-slate-100/70 hover:text-slate-900 border border-transparent font-medium',
                isCollapsed ? 'justify-center p-2.5' : 'gap-3 px-3 py-2'
              ]"
              :title="isCollapsed ? 'User & Role Management' : ''"
            >
              <UserCheck
                class="size-[18px] shrink-0 transition-colors"
                :class="route().current('users.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600'"
              />
              <span v-if="!isCollapsed" class="truncate">User & Roles</span>
            </Link>

            <Link
              :href="route('audit-logs.index')"
              class="flex items-center rounded-lg text-[13px] transition-all duration-150 group"
              :class="[
                route().current('audit-logs.*')
                  ? 'bg-blue-50/90 text-blue-700 font-semibold border border-blue-100/90 shadow-2xs'
                  : 'text-slate-600 hover:bg-slate-100/70 hover:text-slate-900 border border-transparent font-medium',
                isCollapsed ? 'justify-center p-2.5' : 'gap-3 px-3 py-2'
              ]"
              :title="isCollapsed ? 'Audit Logs Trail' : ''"
            >
              <ShieldCheck
                class="size-[18px] shrink-0 transition-colors"
                :class="route().current('audit-logs.*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600'"
              />
              <span v-if="!isCollapsed" class="truncate">Audit Trail Logs</span>
            </Link>
          </template>

          <!-- SECTION: PENGATURAN -->
          <div v-if="!isCollapsed" class="px-3 pt-4 pb-1 text-[10px] font-bold tracking-wider text-slate-400 uppercase">
            Pengaturan
          </div>
          <div v-else class="my-2 border-t border-slate-100 mx-2"></div>

          <Link
            :href="route('profile.show')"
            class="flex items-center rounded-lg text-[13px] transition-all duration-150 group"
            :class="[
              route().current('profile.show')
                ? 'bg-blue-50/90 text-blue-700 font-semibold border border-blue-100/90 shadow-2xs'
                : 'text-slate-600 hover:bg-slate-100/70 hover:text-slate-900 border border-transparent font-medium',
              isCollapsed ? 'justify-center p-2.5' : 'gap-3 px-3 py-2'
            ]"
            :title="isCollapsed ? 'Pengaturan Profil' : ''"
          >
            <Settings
              class="size-[18px] shrink-0 transition-colors"
              :class="route().current('profile.show') ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600'"
            />
            <span v-if="!isCollapsed" class="truncate">Profil & Akun</span>
          </Link>
        </nav>

        <!-- Sidebar Footer -->
        <div class="p-3 border-t border-slate-100">
          <div v-if="!isCollapsed" class="flex items-center justify-between px-3 py-2 rounded-lg bg-slate-50/80 border border-slate-200/70 text-[10px]">
            <div class="flex items-center gap-2">
              <span class="relative flex h-1.5 w-1.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
              </span>
              <span class="text-slate-600 font-medium">Sistem Online</span>
            </div>
            <span class="text-[9px] font-mono text-slate-500 bg-white px-1.5 py-0.5 rounded border border-slate-200/80 shadow-2xs">v2.4</span>
          </div>
          <div v-else class="flex justify-center py-1">
            <span class="relative flex h-2 w-2" title="Sistem Online">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
          </div>
        </div>
      </aside>

      <!-- ============================================================== -->
      <!-- MOBILE DRAWER SIDEBAR                                           -->
      <!-- ============================================================== -->
      <div v-if="mobileMenuOpen" class="fixed inset-0 z-50 flex md:hidden">
        <!-- Backdrop -->
        <div
          class="fixed inset-0 bg-black/30 backdrop-blur-sm transition-opacity duration-300"
          @click="mobileMenuOpen = false"
        ></div>

        <!-- Drawer Content -->
        <div class="relative flex flex-col w-[280px] max-w-[85vw] bg-white h-full shadow-2xl z-10 animate-in slide-in-from-left duration-300 border-r border-slate-200">
          <!-- Mobile Brand Header -->
          <div class="h-16 flex items-center justify-between px-5 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
              <img
                src="/images/Pertamina Gas Corporate Logo.png"
                alt="Pertamina Gas"
                class="h-6 w-auto object-contain"
              />
              <div class="border-l border-slate-200 pl-2.5">
                <span class="font-extrabold text-slate-900 tracking-tight text-[11px] block uppercase">HSE Health</span>
                <span class="text-[9px] font-semibold text-slate-400 uppercase tracking-widest">Pertamina Gas</span>
              </div>
            </div>
            <button
              @click="mobileMenuOpen = false"
              class="p-2 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition"
            >
              <X class="size-5" />
            </button>
          </div>

          <!-- Mobile Nav Items -->
          <nav class="flex-1 py-3 px-3 space-y-1 overflow-y-auto">
            <div class="px-3 pt-1 pb-1 text-[10px] font-bold tracking-wider text-slate-400 uppercase">
              Navigasi
            </div>

            <Link
              :href="route('dashboard')"
              @click="mobileMenuOpen = false"
              class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150"
              :class="route().current('dashboard') ? 'bg-blue-50/90 text-blue-700 font-semibold border border-blue-100/90 shadow-2xs' : 'text-slate-600 hover:bg-slate-100/70 hover:text-slate-900 border border-transparent font-medium'"
            >
              <LayoutDashboard class="size-[18px]" :class="route().current('dashboard') ? 'text-blue-600' : 'text-slate-400'" />
              <span>Dashboard</span>
            </Link>

            <Link
              v-if="user?.role !== 'employee'"
              :href="route('mcu.index')"
              @click="mobileMenuOpen = false"
              class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150"
              :class="route().current('mcu.*') ? 'bg-blue-50/90 text-blue-700 font-semibold border border-blue-100/90 shadow-2xs' : 'text-slate-600 hover:bg-slate-100/70 hover:text-slate-900 border border-transparent font-medium'"
            >
              <FileHeart class="size-[18px]" :class="route().current('mcu.*') ? 'text-blue-600' : 'text-slate-400'" />
              <span>Data MCU Karyawan</span>
            </Link>

            <Link
              :href="route('daily-check-ups.index')"
              @click="mobileMenuOpen = false"
              class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150"
              :class="route().current('daily-check-ups.*') ? 'bg-blue-50/90 text-blue-700 font-semibold border border-blue-100/90 shadow-2xs' : 'text-slate-600 hover:bg-slate-100/70 hover:text-slate-900 border border-transparent font-medium'"
            >
              <ClipboardCheck class="size-[18px]" :class="route().current('daily-check-ups.*') ? 'text-blue-600' : 'text-slate-400'" />
              <div class="flex items-center justify-between flex-1 min-w-0">
                <span>Daily Check Up</span>
                <span
                  class="text-[10px] font-medium px-2 py-0.5 rounded-full border transition-colors"
                  :class="route().current('daily-check-ups.*') ? 'bg-blue-100 text-blue-700 border-blue-200/80 font-semibold' : 'bg-slate-100 text-slate-500 border-slate-200/60'"
                >
                  Harian
                </span>
              </div>
            </Link>

            <Link
              v-if="user?.role !== 'employee'"
              :href="route('employees.index')"
              @click="mobileMenuOpen = false"
              class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150"
              :class="route().current('employees.*') ? 'bg-blue-50/90 text-blue-700 font-semibold border border-blue-100/90 shadow-2xs' : 'text-slate-600 hover:bg-slate-100/70 hover:text-slate-900 border border-transparent font-medium'"
            >
              <Users class="size-[18px]" :class="route().current('employees.*') ? 'text-blue-600' : 'text-slate-400'" />
              <span>Data Karyawan</span>
            </Link>

            <Link
              :href="route('health-alerts.index')"
              @click="mobileMenuOpen = false"
              class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150"
              :class="route().current('health-alerts.*') ? 'bg-amber-50/90 text-amber-800 font-semibold border border-amber-200/80 shadow-2xs' : 'text-slate-600 hover:bg-slate-100/70 hover:text-slate-900 border border-transparent font-medium'"
            >
              <AlertTriangle class="size-[18px]" :class="route().current('health-alerts.*') ? 'text-amber-500' : 'text-slate-400'" />
              <div class="flex items-center justify-between flex-1 min-w-0">
                <span>Health Alerts</span>
                <span class="relative flex h-2 w-2">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                </span>
              </div>
            </Link>

            <Link
              v-if="user?.role === 'employee' && user?.employee_id"
              :href="route('employees.show', user.employee_id)"
              @click="mobileMenuOpen = false"
              class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150"
              :class="route().current('employees.show') ? 'bg-blue-50/90 text-blue-700 font-semibold border border-blue-100/90 shadow-2xs' : 'text-slate-600 hover:bg-slate-100/70 hover:text-slate-900 border border-transparent font-medium'"
            >
              <UserIcon class="size-[18px]" :class="route().current('employees.show') ? 'text-blue-600' : 'text-slate-400'" />
              <span>Profil Kesehatan Saya</span>
            </Link>

            <template v-if="user?.role === 'admin'">
              <div class="px-3 pt-3 pb-1 text-[10px] font-bold tracking-wider text-slate-400 uppercase">
                Administrasi
              </div>
              <Link
                :href="route('users.index')"
                @click="mobileMenuOpen = false"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150"
                :class="route().current('users.*') ? 'bg-blue-50/90 text-blue-700 font-semibold border border-blue-100/90 shadow-2xs' : 'text-slate-600 hover:bg-slate-100/70 hover:text-slate-900 border border-transparent font-medium'"
              >
                <UserCheck class="size-[18px]" :class="route().current('users.*') ? 'text-blue-600' : 'text-slate-400'" />
                <span>User & Roles</span>
              </Link>
              <Link
                :href="route('audit-logs.index')"
                @click="mobileMenuOpen = false"
                class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150"
                :class="route().current('audit-logs.*') ? 'bg-blue-50/90 text-blue-700 font-semibold border border-blue-100/90 shadow-2xs' : 'text-slate-600 hover:bg-slate-100/70 hover:text-slate-900 border border-transparent font-medium'"
              >
                <ShieldCheck class="size-[18px]" :class="route().current('audit-logs.*') ? 'text-blue-600' : 'text-slate-400'" />
                <span>Audit Logs</span>
              </Link>
            </template>

            <div class="px-3 pt-3 pb-1 text-[10px] font-bold tracking-wider text-slate-400 uppercase">
              Akun
            </div>
            <Link
              :href="route('profile.show')"
              @click="mobileMenuOpen = false"
              class="flex items-center gap-3 px-3 py-2 rounded-lg text-[13px] transition-all duration-150"
              :class="route().current('profile.show') ? 'bg-blue-50/90 text-blue-700 font-semibold border border-blue-100/90 shadow-2xs' : 'text-slate-600 hover:bg-slate-100/70 hover:text-slate-900 border border-transparent font-medium'"
            >
              <Settings class="size-[18px] text-slate-400" />
              <span>Pengaturan Profil</span>
            </Link>
          </nav>

          <!-- Mobile Footer -->
          <div class="p-4 border-t border-slate-100 flex flex-col gap-3">
            <div class="flex items-center gap-3">
              <div class="size-10 rounded-xl bg-gradient-to-br from-blue-600 to-blue-700 text-white flex items-center justify-center font-bold text-sm shadow-sm shrink-0 overflow-hidden">
                <img v-if="user?.profile_photo_url" :src="user.profile_photo_url" :alt="user.name" class="size-full object-cover" />
                <span v-else>{{ user?.name ? user.name.charAt(0).toUpperCase() : 'U' }}</span>
              </div>
              <div class="overflow-hidden min-w-0">
                <p class="text-xs font-bold text-slate-900 truncate">{{ user?.name }}</p>
                <p class="text-[10px] text-slate-500 truncate">{{ user?.email }}</p>
              </div>
            </div>

            <button
              @click="logout"
              class="w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-slate-900 text-white rounded-lg text-xs font-semibold hover:bg-slate-800 transition shadow-sm"
            >
              <LogOut class="size-4" />
              <span>Keluar (Logout)</span>
            </button>
          </div>
        </div>
      </div>

      <!-- ============================================================== -->
      <!-- MAIN WORKSPACE CONTENT AREA                                     -->
      <!-- ============================================================== -->
      <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <!-- ============================================================ -->
        <!-- TOPBAR NAVIGATION BAR                                        -->
        <!-- ============================================================ -->
        <!-- ============================================================ -->
        <!-- TOPBAR NAVIGATION BAR                                        -->
        <!-- ============================================================ -->
        <header class="h-16 bg-white/95 backdrop-blur-md border-b border-slate-200/80 px-4 sm:px-6 lg:px-8 flex items-center justify-between shrink-0 sticky top-0 z-20 transition-colors">
          <!-- Left: Mobile Menu & Breadcrumbs -->
          <div class="flex items-center gap-3 min-w-0">
            <!-- Mobile Menu Toggle -->
            <button
              @click="mobileMenuOpen = true"
              class="md:hidden size-9 flex items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition"
              title="Buka Menu"
            >
              <Menu class="size-5" />
            </button>

            <!-- Breadcrumbs & Title with Generous, Balanced Vertical Spacing -->
            <div class="flex flex-col justify-center min-w-0">
              <nav class="flex items-center gap-1.5 text-[11px] text-slate-400 font-medium overflow-hidden whitespace-nowrap mb-0.5">
                <template v-for="(crumb, idx) in breadcrumbs" :key="idx">
                  <ChevronRightIcon v-if="idx > 0" class="size-3 text-slate-300 shrink-0" />
                  <Link
                    v-if="crumb.isHome"
                    :href="route(crumb.routeName)"
                    class="flex items-center gap-1 text-slate-400 hover:text-slate-700 transition font-medium group"
                    title="Kembali ke Dashboard"
                  >
                    <Home class="size-3.5 text-slate-400 group-hover:text-slate-600 transition-colors" />
                    <span class="hidden sm:inline">Beranda</span>
                  </Link>
                  <Link
                    v-else-if="crumb.routeName && !crumb.active"
                    :href="route(crumb.routeName)"
                    class="text-slate-400 hover:text-slate-700 transition truncate max-w-[140px] md:max-w-[200px]"
                  >
                    {{ crumb.label }}
                  </Link>
                  <span
                    v-else
                    class="font-semibold truncate max-w-[160px] md:max-w-[280px]"
                    :class="crumb.active ? 'text-slate-700' : 'text-slate-400'"
                  >
                    {{ crumb.label }}
                  </span>
                </template>
              </nav>

              <h1 class="text-sm sm:text-[15px] font-bold text-slate-900 tracking-tight truncate leading-none">
                {{ title || 'Dashboard Information Center' }}
              </h1>
            </div>
          </div>

          <!-- Right: Search, Clock, Divider, Bell & Profile (Harmonious Unified Heights) -->
          <div class="flex items-center gap-2 sm:gap-2.5 shrink-0">
            <!-- Quick Search Button: Sleek Unified h-9 Height (Desktop) -->
            <button
              @click="searchModalOpen = true"
              class="hidden xl:flex items-center gap-2.5 h-9 px-3 rounded-lg bg-slate-50/80 hover:bg-slate-100/90 border border-slate-200/80 hover:border-slate-300 text-xs text-slate-500 transition duration-150 group shadow-2xs"
              title="Cari cepat data & menu (Ctrl + K)"
            >
              <Search class="size-3.5 text-slate-400 group-hover:text-slate-600 transition-colors" />
              <span class="text-slate-500 font-medium text-[11px]">Cari data / menu...</span>
              <kbd class="px-1.5 py-0.5 text-[9px] font-semibold font-mono bg-white rounded border border-slate-200 text-slate-400 shadow-2xs ml-0.5">⌘K</kbd>
            </button>

            <!-- Quick Search Button (Mobile & Tablet) -->
            <button
              @click="searchModalOpen = true"
              class="flex xl:hidden size-9 rounded-lg border border-slate-200/80 text-slate-500 hover:text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition duration-150 items-center justify-center shrink-0"
              title="Cari cepat data & menu"
              aria-label="Cari data"
            >
              <Search class="size-4 text-slate-500" />
            </button>

            <!-- Live Clock: Minimalist Status Pill with Real-time Pulse Dot -->
            <div class="hidden lg:flex items-center gap-2 h-9 px-3 rounded-lg bg-slate-50/70 border border-slate-200/70 text-[10px] font-medium text-slate-500">
              <span class="relative flex h-1.5 w-1.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
              </span>
              <Clock class="size-3.5 text-slate-400" />
              <span class="tabular-nums">{{ currentDateTime }}</span>
            </div>

            <!-- District Badge (Optional if present) -->
            <div v-if="user?.employee?.distrik" class="hidden 2xl:flex items-center gap-1.5 h-9 px-3 rounded-lg border border-slate-200/70 bg-slate-50/70 text-[10px] font-semibold text-slate-600">
              <Building2 class="size-3.5 text-slate-400" />
              <span class="truncate max-w-[130px]">{{ user.employee.distrik }}</span>
            </div>

            <!-- Subtle Vertical Separator -->
            <div class="hidden sm:block h-5 w-px bg-slate-200/80 mx-0.5"></div>

            <!-- Notifications Bell: Exact size-9 (36px) -->
            <Link
              :href="route('health-alerts.index')"
              class="relative size-9 rounded-lg border border-slate-200/80 text-slate-500 hover:text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition duration-150 flex items-center justify-center shrink-0"
              title="Health Alerts & Monitoring"
            >
              <Bell class="size-[17px]" />
              <span class="absolute top-2 right-2 size-2 bg-amber-500 rounded-full ring-2 ring-white"></span>
            </Link>

            <!-- ======================================================== -->
            <!-- USER PROFILE DROPDOWN: Sleek, High-End Pill               -->
            <!-- ======================================================== -->
            <div id="user-profile-dropdown-container" class="relative">
              <button
                @click.stop="toggleUserDropdown"
                class="flex items-center gap-2.5 h-9 sm:h-10 pl-1.5 pr-2.5 rounded-lg border transition-all duration-200 focus:outline-hidden group"
                :class="userDropdownOpen
                  ? 'bg-slate-50 border-slate-300 ring-2 ring-blue-100 shadow-xs'
                  : 'border-slate-200/80 hover:border-slate-300 hover:bg-slate-50/80 bg-white'"
              >
                <!-- Avatar with soft status ring -->
                <div class="relative shrink-0">
                  <div class="size-7 sm:size-7.5 rounded-md bg-gradient-to-br from-blue-600 to-blue-700 text-white flex items-center justify-center font-bold text-xs shadow-xs overflow-hidden">
                    <img v-if="user?.profile_photo_url" :src="user.profile_photo_url" :alt="user.name" class="size-full object-cover" />
                    <span v-else class="text-[11px]">{{ user?.name ? user.name.charAt(0).toUpperCase() : 'U' }}</span>
                  </div>
                  <span class="size-2 rounded-full bg-emerald-500 absolute -bottom-0.5 -right-0.5 border-[1.5px] border-white"></span>
                </div>

                <!-- User Info: Clean Stack (Name + Role Subtitle) -->
                <div class="hidden sm:flex flex-col text-left leading-none min-w-0 pr-0.5">
                  <span class="text-xs font-semibold text-slate-800 truncate max-w-[120px] md:max-w-[140px] leading-tight">
                    {{ user?.name }}
                  </span>
                  <span class="text-[10px] text-slate-400 font-medium truncate max-w-[120px] md:max-w-[140px] leading-tight mt-0.5">
                    {{ roleLabel }}
                  </span>
                </div>

                <ChevronDown
                  class="size-3.5 text-slate-400 group-hover:text-slate-600 transition-transform duration-200 shrink-0 ml-0.5"
                  :class="userDropdownOpen ? 'rotate-180 text-slate-700' : ''"
                />
              </button>

              <!-- ====================================================== -->
              <!-- USER DROPDOWN POPUP MENU                               -->
              <!-- ====================================================== -->
              <Transition
                enter-active-class="transition duration-150 ease-out transform"
                enter-from-class="opacity-0 scale-95 -translate-y-1"
                enter-to-class="opacity-100 scale-100 translate-y-0"
                leave-active-class="transition duration-100 ease-in transform"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-95"
              >
                <div
                  v-if="userDropdownOpen"
                  class="absolute right-0 mt-2 w-80 bg-white rounded-xl border border-slate-200 shadow-xl py-1.5 z-50 overflow-hidden"
                >
                  <!-- Profile Header -->
                  <div class="px-4 py-3.5 border-b border-slate-100 flex items-center gap-3">
                    <div class="size-11 rounded-xl bg-gradient-to-br from-blue-600 to-blue-700 text-white flex items-center justify-center font-bold text-base shadow-sm shrink-0 overflow-hidden">
                      <img v-if="user?.profile_photo_url" :src="user.profile_photo_url" :alt="user.name" class="size-full object-cover" />
                      <span v-else>{{ user?.name ? user.name.charAt(0).toUpperCase() : 'U' }}</span>
                    </div>
                    <div class="overflow-hidden min-w-0 flex-1">
                      <p class="text-xs font-bold text-slate-900 truncate">{{ user?.name }}</p>
                      <p class="text-[11px] text-slate-500 truncate">{{ user?.email }}</p>
                      <div class="flex items-center gap-1.5 mt-1">
                        <span class="inline-block px-2 py-0.5 text-[9px] font-bold rounded border uppercase" :class="roleBadgeClass">
                          {{ roleLabel }}
                        </span>
                        <span v-if="user?.employee?.nik" class="text-[9px] font-mono text-slate-500 bg-slate-50 px-1.5 py-0.5 rounded border border-slate-100">
                          NIK: {{ user.employee.nik }}
                        </span>
                      </div>
                    </div>
                  </div>

                  <!-- Dropdown Links -->
                  <div class="py-1 px-1.5 text-xs space-y-0.5">
                    <Link
                      :href="route('profile.show')"
                      @click="closeUserDropdown"
                      class="flex items-start gap-3 px-3 py-2.5 rounded-lg text-slate-700 hover:bg-slate-50 font-medium transition group"
                    >
                      <div class="p-1.5 rounded-lg bg-slate-100 group-hover:bg-slate-200 text-slate-500 transition shrink-0 mt-0.5">
                        <UserIcon class="size-4" />
                      </div>
                      <div class="leading-tight">
                        <span class="font-semibold text-slate-800 group-hover:text-slate-900 block">Profil & Biodata</span>
                        <span class="text-[10px] text-slate-400 font-normal">Edit data personal, domisili, APD & foto</span>
                      </div>
                    </Link>

                    <Link
                      :href="route('profile.show')"
                      @click="closeUserDropdown"
                      class="flex items-start gap-3 px-3 py-2.5 rounded-lg text-slate-700 hover:bg-slate-50 font-medium transition group"
                    >
                      <div class="p-1.5 rounded-lg bg-slate-100 group-hover:bg-slate-200 text-slate-500 transition shrink-0 mt-0.5">
                        <KeyRound class="size-4" />
                      </div>
                      <div class="leading-tight">
                        <span class="font-semibold text-slate-800 group-hover:text-slate-900 block">Keamanan Akun</span>
                        <span class="text-[10px] text-slate-400 font-normal">Ubah password & otentikasi dua faktor</span>
                      </div>
                    </Link>

                    <Link
                      :href="route('daily-check-ups.index')"
                      @click="closeUserDropdown"
                      class="flex items-start gap-3 px-3 py-2.5 rounded-lg text-slate-700 hover:bg-slate-50 font-medium transition group"
                    >
                      <div class="p-1.5 rounded-lg bg-slate-100 group-hover:bg-slate-200 text-slate-500 transition shrink-0 mt-0.5">
                        <ClipboardCheck class="size-4" />
                      </div>
                      <div class="leading-tight">
                        <span class="font-semibold text-slate-800 group-hover:text-slate-900 block">Daily Check Up (DCU)</span>
                        <span class="text-[10px] text-slate-400 font-normal">Pemeriksaan kesehatan kelaikan kerja</span>
                      </div>
                    </Link>

                    <Link
                      v-if="user?.role === 'employee' && user?.employee_id"
                      :href="route('employees.show', user.employee_id)"
                      @click="closeUserDropdown"
                      class="flex items-start gap-3 px-3 py-2.5 rounded-lg text-slate-700 hover:bg-slate-50 font-medium transition group"
                    >
                      <div class="p-1.5 rounded-lg bg-slate-100 group-hover:bg-slate-200 text-slate-500 transition shrink-0 mt-0.5">
                        <HeartPulse class="size-4" />
                      </div>
                      <div class="leading-tight">
                        <span class="font-semibold text-slate-800 group-hover:text-slate-900 block">Rekam Medis MCU Saya</span>
                        <span class="text-[10px] text-slate-400 font-normal">Hasil pemeriksaan berkala & sertifikat</span>
                      </div>
                    </Link>

                    <!-- Admin Links -->
                    <template v-if="user?.role === 'admin'">
                      <div class="border-t border-slate-100 my-1 pt-1"></div>
                      <Link
                        :href="route('users.index')"
                        @click="closeUserDropdown"
                        class="flex items-start gap-3 px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-50 font-medium transition group"
                      >
                        <div class="p-1.5 rounded-lg bg-slate-100 text-slate-500 transition shrink-0">
                          <UserCheck class="size-4" />
                        </div>
                        <div class="leading-tight">
                          <span class="font-semibold text-slate-800 group-hover:text-slate-900 block">User Management</span>
                          <span class="text-[10px] text-slate-400 font-normal">Kelola pengguna & hak akses role</span>
                        </div>
                      </Link>

                      <Link
                        :href="route('audit-logs.index')"
                        @click="closeUserDropdown"
                        class="flex items-start gap-3 px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-50 font-medium transition group"
                      >
                        <div class="p-1.5 rounded-lg bg-slate-100 text-slate-500 transition shrink-0">
                          <ShieldCheck class="size-4" />
                        </div>
                        <div class="leading-tight">
                          <span class="font-semibold text-slate-800 group-hover:text-slate-900 block">Audit Trail System</span>
                          <span class="text-[10px] text-slate-400 font-normal">Riwayat aktivitas & kepatuhan sistem</span>
                        </div>
                      </Link>
                    </template>
                  </div>

                  <!-- Logout -->
                  <div class="border-t border-slate-100 mt-1 p-1.5">
                    <button
                      @click="logout"
                      class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg bg-slate-50 hover:bg-slate-900 text-slate-600 hover:text-white text-xs font-semibold transition duration-200 group"
                    >
                      <LogOut class="size-4 text-slate-400 group-hover:text-white transition-colors" />
                      <span>Keluar dari Portal (Logout)</span>
                    </button>
                  </div>
                </div>
              </Transition>
            </div>
          </div>
        </header>

        <!-- ============================================================ -->
        <!-- MAIN WORKSPACE PAGE CONTENT                                  -->
        <!-- ============================================================ -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-gray-50">
          <slot />
        </main>

        <!-- ============================================================ -->
        <!-- FOOTER                                                       -->
        <!-- ============================================================ -->
        <footer class="bg-white border-t border-slate-200/80 py-3 px-6 text-center text-[11px] text-slate-500 shrink-0 flex flex-col sm:flex-row items-center justify-between gap-1.5">
          <div class="flex items-center gap-2">
            <span class="size-1.5 rounded-full bg-emerald-500"></span>
            <span class="font-semibold text-slate-700">PT Pertamina Gas</span>
            <span class="text-slate-300">|</span>
            <span>HSE Health Portal</span>
          </div>
          <p class="text-[10px] text-slate-400">&copy; 2026 PT Pertamina Gas. Hak Cipta Dilindungi Undang-Undang.</p>
        </footer>
      </div>
    </div>

    <!-- ============================================================== -->
    <!-- QUICK SEARCH MODAL (CTRL + K TRIGGER)                           -->
    <!-- ============================================================== -->
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <!-- Modal Backdrop: click outside to close -->
      <div
        v-if="searchModalOpen"
        @click="searchModalOpen = false"
        class="fixed inset-0 z-50 flex items-start justify-center pt-[10vh] sm:pt-[14vh] px-4 bg-slate-900/40 backdrop-blur-xs transition-opacity duration-200 cursor-pointer"
      >
        <!-- Modal Card -->
        <div
          class="relative w-full max-w-xl bg-white rounded-2xl shadow-2xl border border-slate-200/90 overflow-hidden animate-in zoom-in-95 duration-200 cursor-default"
          @click.stop
        >
          <!-- Search Header Input Bar -->
          <div class="flex items-center gap-3 px-4 sm:px-5 py-3.5 border-b border-slate-100 bg-white">
            <Search class="size-4.5 text-slate-400 shrink-0" />
            <input
              ref="searchInputRef"
              v-model="searchQuery"
              type="text"
              placeholder="Ketik untuk mencari modul, data MCU, DCU, karyawan..."
              class="w-full bg-transparent border-0 text-sm font-medium text-slate-800 placeholder:text-slate-400 focus:outline-hidden focus:ring-0 p-0"
            />
            <!-- Close Button (X) -->
            <button
              type="button"
              @click="searchModalOpen = false"
              class="size-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 active:scale-95 transition-all shrink-0 ml-1 cursor-pointer"
              title="Tutup Pencarian"
              aria-label="Tutup pencarian"
            >
              <X class="size-4.5" />
            </button>
          </div>

          <!-- Search Results List -->
          <div class="max-h-[380px] overflow-y-auto p-2 sm:p-2.5 space-y-1 sidebar-scrollbar">
            <template v-if="filteredSearchResults.length > 0">
              <Link
                v-for="(item, idx) in filteredSearchResults"
                :key="idx"
                :href="route(item.routeName)"
                @click="searchModalOpen = false"
                class="flex items-center gap-3.5 px-3 py-2.5 rounded-xl hover:bg-slate-50 transition-all duration-150 group text-left border border-transparent hover:border-slate-200/80 hover:shadow-2xs"
              >
                <!-- Colored Icon Container -->
                <div
                  class="size-10 rounded-xl flex items-center justify-center shrink-0 border transition-all duration-150 group-hover:scale-105 group-hover:shadow-xs"
                  :class="item.iconBg || 'bg-blue-50 text-blue-600 border-blue-100'"
                >
                  <component :is="item.icon" class="size-5" />
                </div>

                <!-- Text Info -->
                <div class="flex-1 min-w-0">
                  <p class="text-[13px] font-semibold text-slate-800 group-hover:text-blue-600 transition-colors truncate leading-tight">
                    {{ item.title }}
                  </p>
                  <p class="text-[11px] text-slate-400 group-hover:text-slate-500 truncate transition-colors mt-0.5 leading-normal">
                    {{ item.desc }}
                  </p>
                </div>

                <!-- Category Badge & Action Indicator -->
                <div class="flex items-center gap-2 shrink-0 pl-2">
                  <span
                    class="text-[9.5px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md border transition-colors"
                    :class="item.badgeClass || 'bg-slate-100 text-slate-500 border-slate-200/60'"
                  >
                    {{ item.category }}
                  </span>
                  <ChevronRight class="size-4 text-slate-300 group-hover:text-blue-600 group-hover:translate-x-0.5 transition-all" />
                </div>
              </Link>
            </template>
            <div v-else class="py-12 text-center text-slate-400 text-xs">
              <Search class="size-8 text-slate-300 mx-auto mb-2" />
              Tidak ditemukan hasil untuk "<span class="font-semibold text-slate-600">{{ searchQuery }}</span>"
            </div>
          </div>

          <!-- Bottom Footer Information -->
          <div class="px-4.5 py-2.5 bg-slate-50/80 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400 select-none">
            <span class="flex items-center gap-1.5">
              <span class="font-semibold text-slate-600 tabular-nums">{{ filteredSearchResults.length }}</span> modul & menu
            </span>
            <span class="hidden sm:inline-flex items-center gap-1 text-[10.5px] text-slate-400">
              Klik tanda <span class="font-semibold text-slate-600">✕</span> atau area luar untuk menutup
            </span>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<style scoped>
/* Custom Minimalist Scrollbar for Sidebar */
.sidebar-scrollbar::-webkit-scrollbar {
  width: 3px;
}
.sidebar-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.sidebar-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(0, 0, 0, 0.06);
  border-radius: 3px;
}
.sidebar-scrollbar::-webkit-scrollbar-thumb:hover {
  background: rgba(0, 0, 0, 0.12);
}
</style>
