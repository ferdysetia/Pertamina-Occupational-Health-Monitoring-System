<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  links: {
    type: Array,
    required: true,
  },
});
</script>

<template>
  <div v-if="links && links.length > 3" class="flex flex-wrap items-center justify-between gap-2 py-3 px-4 border-t border-slate-200 bg-white">
    <div class="flex flex-wrap gap-1">
      <template v-for="(link, key) in links" :key="key">
        <div
          v-if="link.url === null"
          class="px-3 py-1.5 text-xs text-slate-400 border border-slate-200 rounded-md cursor-not-allowed bg-slate-50"
          v-html="link.label"
        />
        <Link
          v-else
          :href="link.url"
          class="px-3 py-1.5 text-xs rounded-md border font-medium transition-colors"
          :class="link.active ? 'bg-red-600 text-white border-red-600 font-semibold' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50'"
          v-html="link.label"
        />
      </template>
    </div>
  </div>
</template>
