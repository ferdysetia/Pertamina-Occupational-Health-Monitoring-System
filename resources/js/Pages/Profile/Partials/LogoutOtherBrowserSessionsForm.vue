<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import { Laptop, Smartphone, Monitor, ShieldAlert, LogOut, CheckCircle2 } from '@lucide/vue';

defineProps({
    sessions: Array,
});

const confirmingLogout = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmLogout = () => {
    confirmingLogout.value = true;
    setTimeout(() => passwordInput.value?.focus(), 250);
};

const logoutOtherBrowserSessions = () => {
    form.delete(route('other-browser-sessions.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingLogout.value = false;
    form.reset();
};
</script>

<template>
  <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
    <!-- Header -->
    <div class="px-6 sm:px-8 py-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-slate-50/40">
      <div>
        <h3 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2">
          <Laptop class="size-5 text-slate-700" />
          <span>Sesi Login Perangkat & Browser</span>
        </h3>
        <p class="text-xs text-slate-500 mt-1">
          Pantau dan keluarkan (*logout*) sesi aktif akun Anda di perangkat atau browser lain untuk menjaga privasi.
        </p>
      </div>

      <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-100 border border-slate-200 text-[11px] font-semibold text-slate-600 self-start sm:self-auto">
        <span>Pengawasan Akses</span>
      </div>
    </div>

    <!-- Body -->
    <div class="p-6 sm:p-8 space-y-6">
      <p class="text-xs text-slate-600 leading-relaxed">
        Daftar berikut menampilkan sesi aktif terbaru yang menggunakan akun Anda. Jika ada aktivitas yang tidak Anda kenali, segera keluarkan sesi tersebut dan perbarui kata sandi akun Anda.
      </p>

      <!-- Active Sessions List -->
      <div v-if="sessions && sessions.length > 0" class="space-y-3">
        <div
          v-for="(session, i) in sessions"
          :key="i"
          class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-200/80"
        >
          <div class="flex items-center gap-3.5">
            <div class="p-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 shadow-2xs">
              <Monitor v-if="session.agent.is_desktop" class="size-5" />
              <Smartphone v-else class="size-5" />
            </div>

            <div class="leading-tight">
              <p class="text-xs font-bold text-slate-900">
                {{ session.agent.platform ? session.agent.platform : 'Perangkat Tidak Dikenal' }} — {{ session.agent.browser ? session.agent.browser : 'Browser' }}
              </p>
              <p class="text-[11px] text-slate-500 mt-0.5">
                IP: <span class="font-mono text-slate-700">{{ session.ip_address }}</span>
                <span class="mx-1.5 text-slate-300">•</span>
                <span v-if="session.is_current_device" class="text-emerald-700 font-bold">Perangkat Ini (Aktif Sekarang)</span>
                <span v-else class="text-slate-500">Terakhir aktif: {{ session.last_active }}</span>
              </p>
            </div>
          </div>

          <span v-if="session.is_current_device" class="px-2.5 py-0.5 text-[10px] font-bold rounded-lg bg-emerald-100 text-emerald-800 border border-emerald-200">
            Aktif
          </span>
        </div>
      </div>

      <!-- Action Button -->
      <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
        <ActionMessage :on="form.recentlySuccessful">
          <div class="flex items-center gap-1.5 text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-200">
            <CheckCircle2 class="size-4" />
            <span>Semua Sesi Perangkat Lain Telah Dikeluarkan</span>
          </div>
        </ActionMessage>

        <button
          type="button"
          @click="confirmLogout"
          class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-xs transition duration-150"
        >
          <LogOut class="size-4" />
          <span>Keluarkan Sesi di Perangkat Lain</span>
        </button>
      </div>
    </div>

    <!-- Password Confirmation Modal -->
    <DialogModal :show="confirmingLogout" @close="closeModal">
      <template #title>
        <div class="flex items-center gap-2 text-slate-900">
          <ShieldAlert class="size-5 text-slate-700" />
          <span>Konfirmasi Logout Perangkat Lain</span>
        </div>
      </template>

      <template #content>
        <p class="text-xs text-slate-600 leading-relaxed">
          Silakan masukkan kata sandi akun Anda untuk mengonfirmasi pengeluaran sesi dari seluruh browser dan perangkat lain.
        </p>

        <div class="mt-4">
          <input
            ref="passwordInput"
            v-model="form.password"
            type="password"
            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 text-xs text-slate-900 bg-white"
            placeholder="Kata Sandi Anda"
            autocomplete="current-password"
            @keyup.enter="logoutOtherBrowserSessions"
          />
          <InputError :message="form.errors.password" class="mt-1" />
        </div>
      </template>

      <template #footer>
        <button
          type="button"
          @click="closeModal"
          class="px-4 py-2 rounded-xl bg-white border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition"
        >
          Batal
        </button>

        <button
          type="button"
          :disabled="form.processing"
          @click="logoutOtherBrowserSessions"
          class="ml-3 px-5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition disabled:opacity-50"
        >
          {{ form.processing ? 'Memproses...' : 'Keluarkan Sesi Lain' }}
        </button>
      </template>
    </DialogModal>
  </div>
</template>
