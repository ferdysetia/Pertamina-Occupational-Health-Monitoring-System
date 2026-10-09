<script setup>
import { Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const isLoaded = ref(false);

onMounted(() => {
    setTimeout(() => {
        isLoaded.value = true;
    }, 100);
});
</script>

<template>
    <div class="relative flex min-h-screen overflow-hidden">
        <!-- ========== LEFT PANEL — Brand Showcase ========== -->
        <div class="relative hidden w-[55%] lg:flex lg:flex-col lg:justify-between overflow-hidden">
            <!-- Background Image -->
            <img
                src="/images/Photo 1.jpg"
                alt="Pertamina Gas Operations"
                class="absolute inset-0 h-full w-full object-cover"
            />
            <!-- Overlay Gradients -->
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900/90 via-slate-800/80 to-blue-900/75"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent"></div>

            <!-- Grid Pattern -->
            <div class="absolute inset-0 opacity-[0.03]" style="background-image: linear-gradient(rgba(255,255,255,.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.1) 1px, transparent 1px); background-size: 60px 60px;"></div>

            <!-- Floating Glows -->
            <div class="absolute top-1/4 -left-20 h-80 w-80 rounded-full bg-blue-500/15 blur-[100px] animate-pulse"></div>
            <div class="absolute bottom-1/3 right-0 h-64 w-64 rounded-full bg-cyan-400/10 blur-[80px] animate-pulse" style="animation-delay: 1.5s;"></div>

            <!-- Brand Content -->
            <div class="relative z-10 flex flex-1 flex-col justify-between p-10 xl:p-14">
                <!-- Top: Logo -->
                <div>
                    <Link href="/" class="inline-block">
                        <div class="flex items-center rounded-xl bg-white/10 px-5 py-3 backdrop-blur-xl border border-white/10 transition-all duration-300 hover:bg-white/15 hover:border-white/20">
                            <img
                                src="/images/Pertamina Gas Corporate Logo.png"
                                alt="Pertamina Gas"
                                class="h-8 w-auto xl:h-10"
                            />
                        </div>
                    </Link>
                </div>

                <!-- Center: Tagline -->
                <div
                    class="max-w-md"
                    :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-6 opacity-0': !isLoaded }"
                    style="transition: all 1s cubic-bezier(0.16, 1, 0.3, 1) 0.3s;"
                >

                    <h1 class="text-3xl font-extrabold leading-[1.15] tracking-tight text-white xl:text-4xl">
                        Occupational
                        <span class="bg-gradient-to-r from-blue-400 via-cyan-400 to-teal-300 bg-clip-text text-transparent">
                            Health
                        </span>
                        <br />
                        Monitoring System
                    </h1>
                    <p class="mt-4 text-sm leading-relaxed text-slate-300/80 xl:text-base">
                        Sistem terintegrasi untuk pemantauan dan pengelolaan data kesehatan kerja karyawan PT Pertamina Gas.
                    </p>
                </div>

                <!-- Bottom: Features -->
                <div
                    class="flex items-center gap-6"
                    :class="{ 'translate-y-0 opacity-100': isLoaded, 'translate-y-4 opacity-0': !isLoaded }"
                    style="transition: all 1s cubic-bezier(0.16, 1, 0.3, 1) 0.6s;"
                >
                    <div class="flex items-center gap-2 text-xs text-slate-400">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-500/15">
                            <svg class="h-4 w-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                            </svg>
                        </div>
                        <span class="font-medium text-slate-300">Secure</span>
                    </div>
                    <div class="h-4 w-px bg-white/10"></div>
                    <div class="flex items-center gap-2 text-xs text-slate-400">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-500/15">
                            <svg class="h-4 w-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                            </svg>
                        </div>
                        <span class="font-medium text-slate-300">Real-time</span>
                    </div>
                    <div class="h-4 w-px bg-white/10"></div>
                    <div class="flex items-center gap-2 text-xs text-slate-400">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-500/15">
                            <svg class="h-4 w-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                            </svg>
                        </div>
                        <span class="font-medium text-slate-300">Health First</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========== RIGHT PANEL — Form Area ========== -->
        <div class="relative flex w-full flex-col items-center justify-center bg-gray-50 px-6 py-8 lg:w-[45%] lg:px-12 xl:px-20">
            <!-- Subtle background pattern for right panel -->
            <div class="absolute inset-0 opacity-[0.015]" style="background-image: radial-gradient(circle at 1px 1px, rgb(0,0,0) 1px, transparent 0); background-size: 32px 32px;"></div>

            <!-- Mobile Logo (visible only on small screens) -->
            <div class="mb-8 lg:hidden">
                <Link href="/">
                    <div class="flex items-center justify-center rounded-xl bg-white px-5 py-3 shadow-lg shadow-gray-200/50 border border-gray-100">
                        <img
                            src="/images/Pertamina Gas Corporate Logo.png"
                            alt="Pertamina Gas"
                            class="h-8 w-auto"
                        />
                    </div>
                </Link>
            </div>

            <!-- Form Slot Container -->
            <div class="relative z-10 w-full max-w-md">
                <slot />
            </div>

            <!-- Bottom branding for right panel -->
            <div class="absolute bottom-4 left-0 right-0 flex justify-center">
                <p class="text-[11px] font-medium tracking-wide text-gray-400">
                    &copy; {{ new Date().getFullYear() }} PT Pertamina Gas. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</template>
