<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});

const isLoaded = ref(false);
const currentYear = new Date().getFullYear();

// Animated counter for stats
const stats = ref([
    { value: 0, target: 15000, suffix: '+', label: 'Karyawan Terpantau' },
    { value: 0, target: 99, suffix: '%', label: 'Tingkat Kepatuhan K3' },
    { value: 0, target: 24, suffix: '/7', label: 'Monitoring Kesehatan' },
    { value: 0, target: 500, suffix: '+', label: 'MCU per Periode' },
]);

function animateCounters() {
    stats.value.forEach((stat, index) => {
        const duration = 2000;
        const stepTime = 20;
        const steps = duration / stepTime;
        const increment = stat.target / steps;
        let current = 0;

        setTimeout(() => {
            const interval = setInterval(() => {
                current += increment;
                if (current >= stat.target) {
                    stat.value = stat.target;
                    clearInterval(interval);
                } else {
                    stat.value = Math.floor(current);
                }
            }, stepTime);
        }, index * 200);
    });
}

onMounted(() => {
    setTimeout(() => {
        isLoaded.value = true;
    }, 100);

    setTimeout(() => {
        animateCounters();
    }, 800);
});
</script>

<template>
    <Head title="Occupational Health Monitoring System">
        <meta name="description" content="Sistem Monitoring Kesehatan Kerja Karyawan PT Pertamina Gas — Memastikan keselamatan dan kesehatan kerja melalui teknologi terdepan." />
    </Head>

    <div id="landing-page" class="relative h-screen w-screen overflow-hidden">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0 z-0">
            <img
                src="/images/Photo 1.jpg"
                alt="Pertamina Gas Operations"
                class="h-full w-full object-cover"
            />
            <!-- Multi-layer gradient overlay for depth -->
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900/90 via-slate-800/80 to-blue-900/75"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
        </div>

        <!-- Animated Grid Pattern Overlay -->
        <div class="absolute inset-0 z-[1] opacity-[0.04]" style="background-image: linear-gradient(rgba(255,255,255,.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.1) 1px, transparent 1px); background-size: 60px 60px;"></div>

        <!-- Floating Glow Effects -->
        <div class="absolute top-1/4 -left-32 z-[1] h-96 w-96 rounded-full bg-blue-500/10 blur-[120px] animate-pulse"></div>
        <div class="absolute bottom-1/4 -right-32 z-[1] h-80 w-80 rounded-full bg-red-500/8 blur-[100px] animate-pulse" style="animation-delay: 1s;"></div>
        <div class="absolute top-1/2 left-1/2 z-[1] h-64 w-64 -translate-x-1/2 -translate-y-1/2 rounded-full bg-cyan-400/5 blur-[80px] animate-pulse" style="animation-delay: 2s;"></div>

        <!-- Content Container -->
        <div class="relative z-10 flex h-full flex-col">

            <!-- ========== NAVIGATION BAR ========== -->
            <nav
                id="landing-navbar"
                class="flex items-center justify-between px-6 py-4 md:px-12 lg:px-16"
                :class="{ 'translate-y-0 opacity-100': isLoaded, '-translate-y-4 opacity-0': !isLoaded }"
                style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);"
            >
                <!-- Logo -->
                <div class="flex items-center gap-4">
                    <div class="flex items-center rounded-xl bg-white/10 px-4 py-2.5 backdrop-blur-xl border border-white/10">
                        <img
                            src="/images/Pertamina Gas Corporate Logo.png"
                            alt="Pertamina Gas"
                            class="h-8 w-auto md:h-10"
                        />
                    </div>
                </div>

                <!-- Navigation Links -->
                <div v-if="canLogin" class="flex items-center gap-3">
                    <Link
                        :href="route('login')"
                        id="landing-login-btn"
                        class="group relative inline-flex items-center gap-2 rounded-xl border border-white/15 bg-white/5 px-6 py-2.5 text-sm font-semibold tracking-wide text-white/90 backdrop-blur-xl transition-all duration-300 hover:border-white/30 hover:bg-white/10 hover:text-white hover:shadow-lg hover:shadow-white/5"
                    >
                        <svg class="h-4 w-4 opacity-70 transition-opacity group-hover:opacity-100" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                        </svg>
                        Log In
                    </Link>

                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        id="landing-register-btn"
                        class="group relative inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-500 px-6 py-2.5 text-sm font-semibold tracking-wide text-white shadow-lg shadow-blue-500/25 transition-all duration-300 hover:from-blue-500 hover:to-cyan-400 hover:shadow-xl hover:shadow-blue-500/30 hover:-translate-y-0.5"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                        </svg>
                        Register
                    </Link>
                </div>
            </nav>

            <!-- ========== MAIN HERO CONTENT ========== -->
            <main class="flex flex-1 items-center px-6 md:px-12 lg:px-16">
                <div class="grid w-full gap-8 lg:grid-cols-2 lg:gap-12 items-center">

                    <!-- Left Column — Hero Text -->
                    <div class="max-w-2xl">

                        <!-- Headline -->
                        <h1
                            class="text-4xl font-extrabold leading-[1.1] tracking-tight text-white md:text-5xl lg:text-6xl"
                            :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-6 opacity-0': !isLoaded }"
                            style="transition: all 1s cubic-bezier(0.16, 1, 0.3, 1) 0.35s;"
                        >
                            Occupational
                            <span class="bg-gradient-to-r from-blue-400 via-cyan-400 to-teal-300 bg-clip-text text-transparent">
                                Health
                            </span>
                            <br />
                            Monitoring System
                        </h1>

                        <!-- Subheadline -->
                        <p
                            class="mt-5 max-w-lg text-base leading-relaxed text-slate-300/90 md:text-lg"
                            :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-6 opacity-0': !isLoaded }"
                            style="transition: all 1s cubic-bezier(0.16, 1, 0.3, 1) 0.5s;"
                        >
                            Sistem terintegrasi untuk pemantauan dan pengelolaan data kesehatan kerja karyawan
                            <strong class="text-white font-semibold">PT Pertamina Gas</strong>.
                            Memastikan keselamatan melalui teknologi terdepan.
                        </p>

                        <!-- CTA Buttons -->
                        <div
                            class="mt-8 flex flex-wrap items-center gap-4"
                            :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-6 opacity-0': !isLoaded }"
                            style="transition: all 1s cubic-bezier(0.16, 1, 0.3, 1) 0.65s;"
                        >
                            <Link
                                v-if="canLogin"
                                :href="route('login')"
                                id="landing-hero-login"
                                class="group inline-flex items-center gap-2.5 rounded-2xl bg-gradient-to-r from-blue-600 to-cyan-500 px-8 py-3.5 text-sm font-bold tracking-wide text-white shadow-2xl shadow-blue-600/30 transition-all duration-300 hover:from-blue-500 hover:to-cyan-400 hover:shadow-blue-500/40 hover:-translate-y-0.5"
                            >
                                Masuk ke Sistem
                                <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                </svg>
                            </Link>
                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                id="landing-hero-register"
                                class="inline-flex items-center gap-2 rounded-2xl border border-white/15 bg-white/5 px-8 py-3.5 text-sm font-semibold tracking-wide text-white/90 backdrop-blur-xl transition-all duration-300 hover:border-white/30 hover:bg-white/10 hover:text-white"
                            >
                                Daftar Akun Baru
                            </Link>
                        </div>
                    </div>

                    <!-- Right Column — Stats & Feature Cards -->
                    <div
                        class="hidden lg:flex flex-col gap-5"
                        :class="{ 'translate-x-0 opacity-100': isLoaded, 'translate-x-8 opacity-0': !isLoaded }"
                        style="transition: all 1.2s cubic-bezier(0.16, 1, 0.3, 1) 0.5s;"
                    >
                        <!-- Stats Grid -->
                        <div class="grid grid-cols-2 gap-4">
                            <div
                                v-for="(stat, index) in stats"
                                :key="index"
                                class="group relative overflow-hidden rounded-2xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl transition-all duration-500 hover:border-white/20 hover:bg-white/10 hover:shadow-xl hover:shadow-white/5"
                            >
                                <div class="absolute -right-4 -top-4 h-20 w-20 rounded-full bg-gradient-to-br from-blue-500/10 to-cyan-500/10 blur-2xl transition-all group-hover:scale-150"></div>
                                <p class="relative text-3xl font-extrabold tracking-tight text-white">
                                    {{ stat.value.toLocaleString() }}<span class="text-cyan-400">{{ stat.suffix }}</span>
                                </p>
                                <p class="relative mt-1 text-xs font-medium tracking-wider text-slate-400 uppercase">
                                    {{ stat.label }}
                                </p>
                            </div>
                        </div>

                        <!-- Feature Cards -->
                        <div class="grid grid-cols-3 gap-3">
                            <div class="group flex flex-col items-center gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 text-center backdrop-blur-xl transition-all duration-500 hover:border-blue-400/30 hover:bg-blue-500/10">
                                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-500/15 transition-colors group-hover:bg-blue-500/25">
                                    <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                                    </svg>
                                </div>
                                <span class="text-[11px] font-semibold tracking-wide text-slate-300 uppercase">MCU Periodik</span>
                            </div>

                            <div class="group flex flex-col items-center gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 text-center backdrop-blur-xl transition-all duration-500 hover:border-emerald-400/30 hover:bg-emerald-500/10">
                                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-500/15 transition-colors group-hover:bg-emerald-500/25">
                                    <svg class="h-5 w-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                    </svg>
                                </div>
                                <span class="text-[11px] font-semibold tracking-wide text-slate-300 uppercase">Daily Check Up</span>
                            </div>

                            <div class="group flex flex-col items-center gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 text-center backdrop-blur-xl transition-all duration-500 hover:border-amber-400/30 hover:bg-amber-500/10">
                                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-500/15 transition-colors group-hover:bg-amber-500/25">
                                    <svg class="h-5 w-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                    </svg>
                                </div>
                                <span class="text-[11px] font-semibold tracking-wide text-slate-300 uppercase">Health Alert</span>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <!-- ========== BOTTOM BAR ========== -->
            <footer
                class="flex items-center justify-between px-6 py-4 md:px-12 lg:px-16"
                :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-4 opacity-0': !isLoaded }"
                style="transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.9s;"
            >
                <p class="text-xs font-medium tracking-wide text-slate-500">
                    &copy; {{ currentYear }} PT Pertamina Gas. All rights reserved.
                </p>
                <div class="flex items-center gap-4">
                    <span class="flex items-center gap-1.5 text-xs text-slate-500">
                        <span class="relative flex h-1.5 w-1.5">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        </span>
                        System Operational
                    </span>
                    <span class="hidden text-xs text-slate-600 md:inline">|</span>
                    <span class="hidden text-xs text-slate-500 md:inline">HSE Monitoring Platform v1.0</span>
                </div>
            </footer>
        </div>
    </div>
</template>
