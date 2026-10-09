<script setup>
defineProps({
  title: String,
  value: [String, Number],
  description: String,
  iconBg: {
    type: String,
    default: 'bg-slate-50 text-slate-600',
  },
  accentColor: {
    type: String,
    default: 'bg-slate-400',
  },
});
</script>

<template>
  <div class="bg-white rounded-2xl border border-slate-200/70 p-5 shadow-sm transition-all duration-200 hover:shadow-lg hover:-translate-y-0.5 group relative overflow-hidden">
    <!-- Subtle top accent line -->
    <div class="absolute top-0 inset-x-0 h-0.5 rounded-t-2xl" :class="accentColor"></div>

    <div class="flex items-start justify-between">
      <div class="flex-1 min-w-0">
        <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 leading-none">{{ title }}</p>
        <div class="mt-3">
          <span class="text-2xl font-extrabold tracking-tight text-slate-900 tabular-nums">{{ value }}</span>
        </div>
        <p v-if="description" class="mt-1.5 text-[11px] text-slate-500 font-medium leading-snug">{{ description }}</p>
      </div>
      <div class="p-2.5 rounded-xl shrink-0 ml-3" :class="iconBg">
        <slot name="icon"></slot>
      </div>
    </div>
  </div>
</template>
