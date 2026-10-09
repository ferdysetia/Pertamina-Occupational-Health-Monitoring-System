<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import { Trash2, AlertTriangle } from '@lucide/vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    setTimeout(() => passwordInput.value?.focus(), 250);
};

const deleteUser = () => {
    form.delete(route('current-user.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.reset();
};
</script>

<template>
  <div class="bg-white rounded-3xl border border-rose-200 shadow-xs overflow-hidden">
    <!-- Header -->
    <div class="px-6 sm:px-8 py-6 border-b border-rose-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-rose-50/40">
      <div>
        <h3 class="text-base sm:text-lg font-bold text-rose-900 flex items-center gap-2">
          <Trash2 class="size-5 text-rose-600" />
          <span>Hapus Akun Pengguna</span>
        </h3>
        <p class="text-xs text-rose-700/80 mt-1">
          Tindakan ini akan menghapus akun secara permanen. Semua data akses tidak dapat dipulihkan.
        </p>
      </div>

      <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-rose-100/70 border border-rose-200 text-[11px] font-bold text-rose-800 self-start sm:self-auto">
        <AlertTriangle class="size-3.5" />
        <span>Zona Bahaya</span>
      </div>
    </div>

    <!-- Body -->
    <div class="p-6 sm:p-8 space-y-4">
      <p class="text-xs text-slate-600 leading-relaxed max-w-2xl">
        Setelah akun Anda dihapus, semua kredensial login dan sesi Anda akan dihapus secara permanen dari server. Pastikan Anda telah mengunduh data penting yang ingin Anda simpan sebelum melanjutkan.
      </p>

      <div class="pt-2">
        <button
          type="button"
          @click="confirmUserDeletion"
          class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-xs transition duration-150"
        >
          <Trash2 class="size-4" />
          <span>Hapus Akun Saya</span>
        </button>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <DialogModal :show="confirmingUserDeletion" @close="closeModal">
      <template #title>
        <div class="flex items-center gap-2 text-rose-900">
          <AlertTriangle class="size-5 text-rose-600" />
          <span>Konfirmasi Hapus Akun</span>
        </div>
      </template>

      <template #content>
        <p class="text-xs text-slate-600 leading-relaxed">
          Apakah Anda yakin ingin menghapus akun ini secara permanen? Silakan masukkan kata sandi akun Anda untuk konfirmasi.
        </p>

        <div class="mt-4">
          <input
            ref="passwordInput"
            v-model="form.password"
            type="password"
            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-rose-600 focus:ring-2 focus:ring-rose-600/10 text-xs text-slate-900 bg-white"
            placeholder="Kata Sandi Anda"
            autocomplete="current-password"
            @keyup.enter="deleteUser"
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
          @click="deleteUser"
          class="ml-3 px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition disabled:opacity-50"
        >
          {{ form.processing ? 'Menghapus...' : 'Ya, Hapus Akun' }}
        </button>
      </template>
    </DialogModal>
  </div>
</template>
