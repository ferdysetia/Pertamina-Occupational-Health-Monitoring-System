<script setup>
import { computed } from 'vue';

const props = defineProps({
  code: {
    type: String,
    required: true,
  },
  showLabel: {
    type: Boolean,
    default: true,
  },
});

const config = computed(() => {
  const code = (props.code || '').toUpperCase().trim();
  switch (code) {
    case 'P1':
      return {
        label: 'P1 — Fit / Normal',
        colorClass: 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100',
        dotClass: 'bg-emerald-500',
      };
    case 'P2':
      return {
        label: 'P2 — Kelainan Ringan',
        colorClass: 'bg-sky-50 text-sky-700 border-sky-200 hover:bg-sky-100',
        dotClass: 'bg-sky-500',
      };
    case 'P3':
      return {
        label: 'P3 — Perhatian Ringan',
        colorClass: 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100',
        dotClass: 'bg-amber-500',
      };
    case 'P4':
      return {
        label: 'P4 — Perhatian Sedang',
        colorClass: 'bg-orange-50 text-orange-700 border-orange-200 hover:bg-orange-100',
        dotClass: 'bg-orange-500',
      };
    case 'P5':
      return {
        label: 'P5 — Perlu Tindak Lanjut',
        colorClass: 'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100',
        dotClass: 'bg-rose-500',
      };
    case 'P6':
      return {
        label: 'P6 — Perhatian Tinggi',
        colorClass: 'bg-purple-50 text-purple-700 border-purple-200 hover:bg-purple-100',
        dotClass: 'bg-purple-600',
      };
    case 'P7':
      return {
        label: 'P7 — Kondisi Kurang Baik',
        colorClass: 'bg-red-100 text-red-800 border-red-300 font-bold hover:bg-red-200',
        dotClass: 'bg-red-600',
      };
    default:
      return {
        label: code || 'Tidak Ada',
        colorClass: 'bg-gray-100 text-gray-700 border-gray-200',
        dotClass: 'bg-gray-400',
      };
  }
});
</script>

<template>
  <span
    class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full border transition-all"
    :class="config.colorClass"
  >
    <span class="size-1.5 rounded-full" :class="config.dotClass"></span>
    <span>{{ showLabel ? config.label : code }}</span>
  </span>
</template>
