<template>
  <div class="max-w-6xl mx-auto">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-emerald-900">Pacientes</h1>
        <p class="text-emerald-600 mt-1">Gerencie os cadastros de pacientes.</p>
      </div>
      <div class="flex items-center gap-3">
        <div class="relative">
          <input v-model="search" type="text" placeholder="Buscar paciente..."
            class="w-64 pl-9 pr-3 py-2 border border-emerald-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none bg-emerald-50/30"
            @input="debouncedSearch">
          <svg class="absolute left-3 top-2.5 w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
        <router-link to="/staff/patients/new"
          class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors text-sm shadow-sm inline-flex items-center gap-1.5">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
          Novo Paciente
        </router-link>
      </div>
    </div>

    <div v-if="loading" class="text-center py-12 text-emerald-500">Carregando...</div>

    <div v-else class="bg-white rounded-xl shadow-sm border border-emerald-200 overflow-hidden">
      <table class="w-full">
        <thead>
          <tr class="border-b border-emerald-200 bg-emerald-50">
            <th class="text-left px-6 py-3 text-xs font-medium text-emerald-700 uppercase">Nome</th>
            <th class="text-left px-6 py-3 text-xs font-medium text-emerald-700 uppercase">E-mail</th>
            <th class="text-left px-6 py-3 text-xs font-medium text-emerald-700 uppercase">CPF</th>
            <th class="text-left px-6 py-3 text-xs font-medium text-emerald-700 uppercase">Data Nasc.</th>
            <th class="text-right px-6 py-3 text-xs font-medium text-emerald-700 uppercase">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-emerald-100">
          <tr v-for="patient in patients" :key="patient.id" class="hover:bg-emerald-50/50 transition-colors">
            <td class="px-6 py-4 text-sm font-medium text-emerald-900">{{ patient.user?.name || patient.full_name }}</td>
            <td class="px-6 py-4 text-sm text-emerald-600">{{ patient.user?.email || '-' }}</td>
            <td class="px-6 py-4 text-sm text-emerald-600">{{ patient.cpf_masked || '***' }}</td>
            <td class="px-6 py-4 text-sm text-emerald-600">{{ patient.birth_date || '-' }}</td>

            <td class="px-6 py-4 text-right">
              <router-link :to="`/staff/patients/${patient.id}`"
                class="inline-flex items-center gap-1 text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                Detalhes
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
              </router-link>
            </td>
          </tr>
          <tr v-if="patients.length === 0">
            <td colspan="5" class="px-6 py-12 text-center text-emerald-500">Nenhum paciente encontrado.</td>
          </tr>
        </tbody>
      </table>

      <div v-if="pagination.last_page > 1" class="px-6 py-4 border-t border-emerald-200 flex items-center justify-between">
        <p class="text-sm text-emerald-600">Mostrando {{ pagination.from }} a {{ pagination.to }} de {{ pagination.total }}</p>
        <div class="flex gap-2">
          <button :disabled="!pagination.prev_page_url" @click="changePage(pagination.current_page - 1)"
            class="px-3 py-1.5 text-sm border border-emerald-300 rounded-lg hover:bg-emerald-50 text-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed">Anterior</button>
          <button :disabled="!pagination.next_page_url" @click="changePage(pagination.current_page + 1)"
            class="px-3 py-1.5 text-sm border border-emerald-300 rounded-lg hover:bg-emerald-50 text-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed">Próximo</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const patients = ref([]);

const loading = ref(true);
const search = ref('');
const pagination = ref({});
let debounceTimer = null;

async function fetchPatients(page = 1) {
  loading.value = true;
  try {
    const params = { page };
    if (search.value) params.search = search.value;
    const { data } = await axios.get('/staff/patients', { params });
    const rawData = data.data || data;
    patients.value = Array.isArray(rawData) ? rawData : (rawData?.data || []);
    pagination.value = {
      current_page: data.current_page || rawData?.current_page || 1,
      last_page: data.last_page || rawData?.last_page || 1,
      from: data.from || rawData?.from || 0,
      to: data.to || rawData?.to || 0,
      total: data.total || rawData?.total || 0,
      prev_page_url: data.prev_page_url || rawData?.prev_page_url,
      next_page_url: data.next_page_url || rawData?.next_page_url,
    };
  } catch (e) {
    console.error('Erro ao buscar pacientes:', e);
    patients.value = [];
  } finally {
    loading.value = false;
  }
}

function changePage(page) {
  fetchPatients(page);
}

function debouncedSearch() {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => fetchPatients(), 300);
}

onMounted(() => fetchPatients());
</script>
