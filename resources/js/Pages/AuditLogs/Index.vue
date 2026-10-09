<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { ShieldCheck, Clock, User } from '@lucide/vue';

defineProps({
  logs: Object,
});
</script>

<template>
  <AppLayout title="Audit Log System — Admin Only">
    <div class="space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
            <ShieldCheck class="size-6 text-purple-600" />
            <span>Audit Trail Activity Log System</span>
          </h2>
          <p class="text-xs text-slate-500 mt-0.5">Jejak riwayat perubahan data MCU, health alert, dan akun pengguna</p>
        </div>
      </div>

      <!-- Activity Logs Table -->
      <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider">
              <tr>
                <th class="py-3 px-4">Waktu Log</th>
                <th class="py-3 px-4">Pengguna (User)</th>
                <th class="py-3 px-4">Aksi Perubahan</th>
                <th class="py-3 px-4">Entitas Target</th>
                <th class="py-3 px-4">Detail Perubahan</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="log in logs.data" :key="log.id" class="hover:bg-slate-50/80 transition">
                <td class="py-3 px-4 font-mono text-[11px] text-slate-500 whitespace-nowrap">
                  {{ new Date(log.created_at).toLocaleString('id-ID') }}
                </td>
                <td class="py-3 px-4 font-semibold text-slate-800">
                  {{ log.user?.name || 'System / Auto' }}
                </td>
                <td class="py-3 px-4">
                  <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                    {{ log.action }}
                  </span>
                </td>
                <td class="py-3 px-4 text-slate-600 font-mono text-[11px]">
                  {{ log.entity_type }} #{{ log.entity_id }}
                </td>
                <td class="py-3 px-4 text-slate-700 max-w-md truncate">
                  {{ JSON.stringify(log.changes) }}
                </td>
              </tr>

              <tr v-if="logs.data.length === 0">
                <td colspan="5" class="py-8 text-center text-slate-400">
                  Belum ada log aktivitas tercatat.
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <Pagination :links="logs.links" />
      </div>
    </div>
  </AppLayout>
</template>
