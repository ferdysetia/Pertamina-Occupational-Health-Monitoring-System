<script setup>
import { ref, computed, watch } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import ConfirmsPassword from '@/Components/ConfirmsPassword.vue';
import InputError from '@/Components/InputError.vue';
import { ShieldCheck, ShieldAlert, QrCode, Key, CheckCircle2, Lock } from '@lucide/vue';

const props = defineProps({
    requiresConfirmation: Boolean,
});

const page = usePage();
const enabling = ref(false);
const confirming = ref(false);
const disabling = ref(false);
const qrCode = ref(null);
const setupKey = ref(null);
const recoveryCodes = ref([]);

const confirmationForm = useForm({
    code: '',
});

const twoFactorEnabled = computed(
    () => ! enabling.value && page.props.auth.user?.two_factor_enabled,
);

watch(twoFactorEnabled, () => {
    if (! twoFactorEnabled.value) {
        confirmationForm.reset();
        confirmationForm.clearErrors();
    }
});

const enableTwoFactorAuthentication = () => {
    enabling.value = true;

    router.post(route('two-factor.enable'), {}, {
        preserveScroll: true,
        onSuccess: () => Promise.all([
            showQrCode(),
            showSetupKey(),
            showRecoveryCodes(),
        ]),
        onFinish: () => {
            enabling.value = false;
            confirming.value = props.requiresConfirmation;
        },
    });
};

const showQrCode = () => {
    return axios.get(route('two-factor.qr-code')).then(response => {
        qrCode.value = response.data.svg;
    });
};

const showSetupKey = () => {
    return axios.get(route('two-factor.secret-key')).then(response => {
        setupKey.value = response.data.secretKey;
    });
};

const showRecoveryCodes = () => {
    return axios.get(route('two-factor.recovery-codes')).then(response => {
        recoveryCodes.value = response.data;
    });
};

const confirmTwoFactorAuthentication = () => {
    confirmationForm.post(route('two-factor.confirm'), {
        errorBag: 'confirmTwoFactorAuthentication',
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            confirming.value = false;
            qrCode.value = null;
            setupKey.value = null;
        },
    });
};

const regenerateRecoveryCodes = () => {
    axios
        .post(route('two-factor.recovery-codes'))
        .then(() => showRecoveryCodes());
};

const disableTwoFactorAuthentication = () => {
    disabling.value = true;

    router.delete(route('two-factor.disable'), {
        preserveScroll: true,
        onSuccess: () => {
            disabling.value = false;
            confirming.value = false;
        },
    });
};
</script>

<template>
  <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
    <!-- Header -->
    <div class="px-6 sm:px-8 py-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-slate-50/40">
      <div>
        <h3 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2">
          <ShieldCheck class="size-5 text-slate-700" />
          <span>Otentikasi Dua Faktor (2FA)</span>
        </h3>
        <p class="text-xs text-slate-500 mt-1">
          Tambahkan lapisan perlindungan ekstra pada akun Anda dengan kode keamanan dari Google Authenticator.
        </p>
      </div>

      <div class="self-start sm:self-auto">
        <span
          class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold border"
          :class="twoFactorEnabled ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-200'"
        >
          <span class="size-2 rounded-full" :class="twoFactorEnabled ? 'bg-emerald-500' : 'bg-slate-400'"></span>
          <span>{{ twoFactorEnabled ? '2FA Aktif' : '2FA Nonaktif' }}</span>
        </span>
      </div>
    </div>

    <!-- Content Body -->
    <div class="p-6 sm:p-8 space-y-6">
      <div v-if="twoFactorEnabled && !confirming">
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 flex items-center gap-3">
          <CheckCircle2 class="size-5 text-emerald-600 shrink-0" />
          <span>Otentikasi dua faktor saat ini aktif dan melindungi akun Anda dari akses yang tidak sah.</span>
        </div>
      </div>

      <div v-else-if="twoFactorEnabled && confirming">
        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-800 flex items-center gap-3">
          <ShieldAlert class="size-5 text-amber-600 shrink-0" />
          <span>Selesaikan aktivasi otentikasi dua faktor dengan memindai QR Code di bawah.</span>
        </div>
      </div>

      <div v-else>
        <p class="text-xs text-slate-600 leading-relaxed">
          Saat fitur ini aktif, Anda akan diminta memasukkan kode token acak 6-digit dari aplikasi Google Authenticator atau Microsoft Authenticator di smartphone Anda setiap kali melakukan login.
        </p>
      </div>

      <!-- QR Code & Setup Key -->
      <div v-if="twoFactorEnabled">
        <div v-if="qrCode" class="p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
          <p class="text-xs font-bold text-slate-800">
            {{ confirming ? 'Pindai QR Code berikut menggunakan aplikasi Authenticator Anda:' : 'QR Code Otentikasi Dua Faktor:' }}
          </p>

          <div class="p-4 bg-white rounded-2xl inline-block border border-slate-200 shadow-xs" v-html="qrCode" />

          <div v-if="setupKey" class="text-xs text-slate-600">
            <span class="font-bold text-slate-800">Kunci Setup Manual:</span>
            <code class="ml-2 px-2 py-1 bg-white rounded-lg border border-slate-200 font-mono font-bold text-slate-900" v-html="setupKey"></code>
          </div>

          <!-- Confirmation Code Input -->
          <div v-if="confirming" class="pt-3 max-w-sm space-y-2">
            <label for="code" class="block text-xs font-bold text-slate-700">Masukkan Kode 6-Digit dari Aplikasi Authenticator</label>
            <div class="flex items-center gap-2">
              <input
                id="code"
                v-model="confirmationForm.code"
                type="text"
                inputmode="numeric"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/10 text-xs font-mono font-bold text-center tracking-widest text-slate-900 bg-white"
                placeholder="123456"
                @keyup.enter="confirmTwoFactorAuthentication"
              />
              <button
                type="button"
                @click="confirmTwoFactorAuthentication"
                class="px-4 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition"
              >
                Konfirmasi
              </button>
            </div>
            <InputError :message="confirmationForm.errors.code" class="mt-1" />
          </div>
        </div>

        <!-- Recovery Codes -->
        <div v-if="recoveryCodes.length > 0 && !confirming" class="p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
          <p class="text-xs font-bold text-slate-800">Kode Pemulihan Cadangan (Recovery Codes)</p>
          <p class="text-xs text-slate-500">Simpan kode-kode pemulihan ini di tempat yang aman. Kode ini dapat digunakan jika Anda kehilangan akses ke perangkat autentikasi.</p>
          
          <div class="grid grid-cols-2 gap-2 max-w-md p-3 bg-white rounded-xl border border-slate-200 font-mono text-xs text-slate-800">
            <div v-for="code in recoveryCodes" :key="code" class="py-0.5">
              {{ code }}
            </div>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="pt-4 border-t border-slate-100 flex flex-wrap items-center gap-3">
        <template v-if="!twoFactorEnabled">
          <ConfirmsPassword @confirmed="enableTwoFactorAuthentication">
            <button
              type="button"
              :disabled="enabling"
              class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-xs transition duration-150 disabled:opacity-50"
            >
              <ShieldCheck class="size-4" />
              <span>Aktifkan 2FA</span>
            </button>
          </ConfirmsPassword>
        </template>

        <template v-else>
          <ConfirmsPassword @confirmed="confirmTwoFactorAuthentication">
            <button
              v-if="confirming"
              type="button"
              :disabled="enabling"
              class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-xs transition duration-150"
            >
              <span>Konfirmasi & Selesai</span>
            </button>
          </ConfirmsPassword>

          <ConfirmsPassword @confirmed="regenerateRecoveryCodes">
            <button
              v-if="recoveryCodes.length > 0 && !confirming"
              type="button"
              class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold text-xs transition"
            >
              <Key class="size-4 text-slate-500" />
              <span>Buat Ulang Recovery Codes</span>
            </button>
          </ConfirmsPassword>

          <ConfirmsPassword @confirmed="showRecoveryCodes">
            <button
              v-if="recoveryCodes.length === 0 && !confirming"
              type="button"
              class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold text-xs transition"
            >
              <span>Lihat Recovery Codes</span>
            </button>
          </ConfirmsPassword>

          <ConfirmsPassword @confirmed="disableTwoFactorAuthentication">
            <button
              type="button"
              :disabled="disabling"
              class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-rose-50 border border-rose-200 hover:bg-rose-100 text-rose-700 font-semibold text-xs transition"
            >
              <span>Nonaktifkan 2FA</span>
            </button>
          </ConfirmsPassword>
        </template>
      </div>
    </div>
  </div>
</template>
