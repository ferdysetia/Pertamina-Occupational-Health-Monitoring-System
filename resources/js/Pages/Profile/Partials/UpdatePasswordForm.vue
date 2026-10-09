<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import InputError from '@/Components/InputError.vue';
import { KeyRound, Lock, Eye, EyeOff, CheckCircle2, Save, ShieldCheck } from '@lucide/vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const showCurrentPassword = ref(false);
const showNewPassword = ref(false);
const showConfirmPassword = ref(false);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('user-password.update'), {
        errorBag: 'updatePassword',
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value?.focus();
            }

            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value?.focus();
            }
        },
    });
};
</script>

<template>
  <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
    <!-- Card Header -->
    <div class="px-6 sm:px-8 py-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-slate-50/40">
      <div>
        <h3 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2">
          <KeyRound class="size-5 text-slate-700" />
          <span>Ubah Kata Sandi Akun</span>
        </h3>
        <p class="text-xs text-slate-500 mt-1">
          Pastikan akun Anda menggunakan kata sandi yang panjang, unik, dan aman untuk melindungi data operasional HSE.
        </p>
      </div>

      <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-100 border border-slate-200 text-[11px] font-semibold text-slate-600 self-start sm:self-auto">
        <ShieldCheck class="size-3.5 text-slate-500" />
        <span>Keamanan Tingkat Tinggi</span>
      </div>
    </div>

    <!-- Form Content -->
    <form @submit.prevent="updatePassword" class="p-6 sm:p-8 space-y-6">
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <!-- Current Password -->
        <div>
          <label for="current_password" class="block text-xs font-bold text-slate-700 mb-1.5">
            Kata Sandi Saat Ini <span class="text-rose-500">*</span>
          </label>
          <div class="relative">
            <input
              id="current_password"
              ref="currentPasswordInput"
              v-model="form.current_password"
              :type="showCurrentPassword ? 'text' : 'password'"
              class="w-full px-3.5 py-2.5 pr-10 rounded-xl border border-slate-300 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 text-xs text-slate-900 bg-white placeholder:text-slate-400"
              autocomplete="current-password"
              placeholder="••••••••"
            />
            <button
              type="button"
              @click="showCurrentPassword = !showCurrentPassword"
              class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-700 transition"
              tabindex="-1"
            >
              <EyeOff v-if="showCurrentPassword" class="size-4" />
              <Eye v-else class="size-4" />
            </button>
          </div>
          <InputError :message="form.errors.current_password" class="mt-1" />
        </div>

        <!-- New Password -->
        <div>
          <label for="new_password" class="block text-xs font-bold text-slate-700 mb-1.5">
            Kata Sandi Baru <span class="text-rose-500">*</span>
          </label>
          <div class="relative">
            <input
              id="new_password"
              ref="passwordInput"
              v-model="form.password"
              :type="showNewPassword ? 'text' : 'password'"
              class="w-full px-3.5 py-2.5 pr-10 rounded-xl border border-slate-300 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 text-xs text-slate-900 bg-white placeholder:text-slate-400"
              autocomplete="new-password"
              placeholder="Minimal 8 karakter"
            />
            <button
              type="button"
              @click="showNewPassword = !showNewPassword"
              class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-700 transition"
              tabindex="-1"
            >
              <EyeOff v-if="showNewPassword" class="size-4" />
              <Eye v-else class="size-4" />
            </button>
          </div>
          <InputError :message="form.errors.password" class="mt-1" />
        </div>

        <!-- Confirm Password -->
        <div>
          <label for="password_confirmation" class="block text-xs font-bold text-slate-700 mb-1.5">
            Konfirmasi Sandi Baru <span class="text-rose-500">*</span>
          </label>
          <div class="relative">
            <input
              id="password_confirmation"
              v-model="form.password_confirmation"
              :type="showConfirmPassword ? 'text' : 'password'"
              class="w-full px-3.5 py-2.5 pr-10 rounded-xl border border-slate-300 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 text-xs text-slate-900 bg-white placeholder:text-slate-400"
              autocomplete="new-password"
              placeholder="Ulangi sandi baru"
            />
            <button
              type="button"
              @click="showConfirmPassword = !showConfirmPassword"
              class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-700 transition"
              tabindex="-1"
            >
              <EyeOff v-if="showConfirmPassword" class="size-4" />
              <Eye v-else class="size-4" />
            </button>
          </div>
          <InputError :message="form.errors.password_confirmation" class="mt-1" />
        </div>
      </div>

      <!-- Footer Actions -->
      <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
        <ActionMessage :on="form.recentlySuccessful">
          <div class="flex items-center gap-1.5 text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-200">
            <CheckCircle2 class="size-4" />
            <span>Kata Sandi Berhasil Diperbarui</span>
          </div>
        </ActionMessage>

        <button
          type="submit"
          :disabled="form.processing"
          class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-xs transition duration-150 disabled:opacity-50"
        >
          <Save class="size-4" />
          <span>{{ form.processing ? 'Menyimpan...' : 'Perbarui Kata Sandi' }}</span>
        </button>
      </div>
    </form>
  </div>
</template>
