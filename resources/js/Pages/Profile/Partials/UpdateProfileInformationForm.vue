<script setup>
import { ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import InputError from '@/Components/InputError.vue';
import { User, Mail, CheckCircle2, Save, Sparkles } from '@lucide/vue';

const props = defineProps({
    user: Object,
});

const form = useForm({
    _method: 'PUT',
    name: props.user.name,
    email: props.user.email,
});

const verificationLinkSent = ref(null);

const updateProfileInformation = () => {
    form.post(route('user-profile-information.update'), {
        errorBag: 'updateProfileInformation',
        preserveScroll: true,
    });
};

const sendEmailVerification = () => {
    verificationLinkSent.value = true;
};
</script>

<template>
  <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
    <!-- Card Header -->
    <div class="px-6 sm:px-8 py-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-slate-50/40">
      <div>
        <h3 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2">
          <User class="size-5 text-slate-700" />
          <span>Informasi Akun & Email Login</span>
        </h3>
        <p class="text-xs text-slate-500 mt-1">
          Perbarui nama tampilan akun dan alamat email yang digunakan untuk masuk ke portal HSE Pertamina Gas.
        </p>
      </div>

      <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-slate-100 border border-slate-200 text-[11px] font-semibold text-slate-600 self-start sm:self-auto">
        <Sparkles class="size-3.5 text-slate-500" />
        <span>Kredensial Utama</span>
      </div>
    </div>

    <!-- Form Body -->
    <form @submit.prevent="updateProfileInformation" class="p-6 sm:p-8 space-y-6">
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <!-- Nama Akun -->
        <div>
          <label for="user_name" class="block text-xs font-bold text-slate-700 mb-1.5">
            Nama Pengguna Akun <span class="text-rose-500">*</span>
          </label>
          <div class="relative">
            <input
              id="user_name"
              v-model="form.name"
              type="text"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 text-xs font-semibold text-slate-900 bg-white placeholder:text-slate-400"
              required
              autocomplete="name"
              placeholder="Nama Akun"
            />
          </div>
          <InputError :message="form.errors.name" class="mt-1" />
        </div>

        <!-- Email Akun -->
        <div>
          <label for="user_email" class="block text-xs font-bold text-slate-700 mb-1.5">
            Alamat Email Login <span class="text-rose-500">*</span>
          </label>
          <div class="relative">
            <input
              id="user_email"
              v-model="form.email"
              type="email"
              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 text-xs text-slate-900 bg-white placeholder:text-slate-400"
              required
              autocomplete="username"
              placeholder="nama@pertamina.com"
            />
          </div>
          <InputError :message="form.errors.email" class="mt-1" />

          <!-- Email Verification Notice -->
          <div v-if="$page.props.jetstream.hasEmailVerification && user.email_verified_at === null" class="mt-2.5 p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800">
            <p>Alamat email Anda belum terverifikasi.</p>
            <Link
              :href="route('verification.send')"
              method="post"
              as="button"
              class="mt-1 font-bold underline hover:text-amber-900 text-xs"
              @click.prevent="sendEmailVerification"
            >
              Klik di sini untuk mengirim ulang tautan verifikasi email.
            </Link>

            <div v-show="verificationLinkSent" class="mt-2 font-bold text-xs text-emerald-700">
              Tautan verifikasi baru telah dikirim ke alamat email Anda.
            </div>
          </div>
        </div>
      </div>

      <!-- Footer Actions -->
      <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
        <ActionMessage :on="form.recentlySuccessful">
          <div class="flex items-center gap-1.5 text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-200">
            <CheckCircle2 class="size-4" />
            <span>Informasi Akun Berhasil Diperbarui</span>
          </div>
        </ActionMessage>

        <button
          type="submit"
          :disabled="form.processing"
          class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-xs transition duration-150 disabled:opacity-50"
        >
          <Save class="size-4" />
          <span>{{ form.processing ? 'Menyimpan...' : 'Simpan Akun' }}</span>
        </button>
      </div>
    </form>
  </div>
</template>
