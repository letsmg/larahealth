<template>
  <div>
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-emerald-900">Relatório de Pacientes</h1>
      <p class="text-emerald-600 mt-1">Visualize e filtre pacientes por profissional, ordem alfabética ou idade.</p>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-xl border border-emerald-200 p-4 mb-6 shadow-sm">
      <div class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
          <label class="block text-sm font-medium text-emerald-800 mb-1">Profissional</label>
          <select
            v-model="filterProfessional"
            @change="fetchReport"
            class="w-full px-3 py-2 border border-emerald-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-colors bg-emerald-50/30 text-sm"
          >
            <option value="">Todos os profissionais</option>
            <option v-for="prof in professionals" :key="prof.id" :value="prof.id">
              {{ prof.full_name }} ({{ prof.specialty }})
            </option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-emerald-800 mb-1">Ordenar por</label>
          <select
            v-model="filterOrder"
            @change="fetchReport"
            class="w-full px-3 py-2 border border-emerald-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-colors bg-emerald-50/30 text-sm"
          >
            <option value="name">Nome (A-Z)</option>
            <option value="name_desc">Nome (Z-A)</option>
            <option value="age_asc">Mais velhos primeiro</option>
            <option value="age_desc">Mais novos primeiro</option>
          </select>
        </div>

        <div>
          <button
            @click="fetchReport"
            class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors text-sm shadow-sm"
          >
            Filtrar
          </button>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="text-center py-12">
      <div class="inline-block w-8 h-8 border-4 border-emerald-200 border-t-emerald-600 rounded-full animate-spin"></div>
      <p class="text-emerald-600 mt-2 text-sm">Carregando...</p>
    </div>

    <!-- Tabela de pacientes -->
    <div v-else class="bg-white rounded-xl border border-emerald-200 overflow-hidden shadow-sm">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="bg-emerald-50 border-b border-emerald-200">
              <th class="text-left px-4 py-3 text-xs font-semibold text-emerald-700 uppercase tracking-wider">Nome</th>
              <th class="text-left px-4 py-3 text-xs font-semibold text-emerald-700 uppercase tracking-wider">Data de Nascimento</th>
              <th class="text-left px-4 py-3 text-xs font-semibold text-emerald-700 uppercase tracking-wider">Idade</th>
              <th class="text-left px-4 py-3 text-xs font-semibold text-emerald-700 uppercase tracking-wider">Cidade</th>
              <th class="text-left px-4 py-3 text-xs font-semibold text-emerald-700 uppercase tracking-wider">Queixa Principal</th>
              <th class="text-center px-4 py-3 text-xs font-semibold text-emerald-700 uppercase tracking-wider">Aniversário 🎂</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="patients.length === 0">
              <td colspan="6" class="px-4 py-8 text-center text-emerald-500 text-sm">
                Nenhum paciente encontrado.
              </td>
            </tr>
            <tr
              v-for="patient in patients"
              :key="patient.id"
              class="border-b border-emerald-100 last:border-b-0 hover:bg-emerald-50/50 transition-colors"
              :class="{ 'bg-amber-50/50': isBirthday(patient.date_of_birth) }"
            >
              <td class="px-4 py-3">
                <div class="flex items-center gap-2">
                  <span class="text-sm font-medium text-emerald-900">{{ patient.full_name }}</span>
                  <span v-if="isBirthday(patient.date_of_birth)" class="text-lg" title="Aniversário hoje!">🎉</span>
                </div>
              </td>
              <td class="px-4 py-3 text-sm text-emerald-700">
                {{ formatDate(patient.date_of_birth) }}
              </td>
              <td class="px-4 py-3 text-sm text-emerald-700">
                {{ calculateAge(patient.date_of_birth) }} anos
              </td>
              <td class="px-4 py-3 text-sm text-emerald-700">
                {{ patient.city || '-' }}
              </td>
              <td class="px-4 py-3 text-sm text-emerald-700 max-w-[200px] truncate" :title="patient.main_complaint">
                {{ patient.main_complaint || '-' }}
              </td>
              <td class="px-4 py-3 text-center">
                <span
                  v-if="isBirthday(patient.date_of_birth)"
                  class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium bg-amber-100 text-amber-700 rounded-full"
                >
                  🎂 Hoje!
                </span>
                <span v-else class="text-xs text-emerald-400">—</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Paginação -->
      <div v-if="pagination.last_page > 1" class="px-4 py-3 border-t border-emerald-100 flex items-center justify-between">
        <p class="text-sm text-emerald-600">
          Mostrando {{ pagination.from }} a {{ pagination.to }} de {{ pagination.total }} pacientes
        </p>
        <div class="flex gap-2">
          <button
            :disabled="!pagination.prev_page_url"
            @click="goToPage(pagination.current_page - 1)"
            class="px-3 py-1.5 text-sm rounded-lg border border-emerald-300 disabled:opacity-40 disabled:cursor-not-allowed hover:bg-emerald-50 transition-colors"
          >
            Anterior
          </button>
          <button
            :disabled="!pagination.next_page_url"
            @click="goToPage(pagination.current_page + 1)"
            class="px-3 py-1.5 text-sm rounded-lg border border-emerald-300 disabled:opacity-40 disabled:cursor-not-allowed hover:bg-emerald-50 transition-colors"
          >
            Próximo
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'

const patients = ref<any[]>([])
const professionals = ref<any[]>([])
const loading = ref(false)
const filterProfessional = ref('')
const filterOrder = ref('name')

const pagination = ref({
  current_page: 1,
  last_page: 1,
  from: 0,
  to: 0,
  total: 0,
  prev_page_url: null as string | null,
  next_page_url: null as string | null,
})

function formatDate(date: string) {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('pt-BR')
}

function calculateAge(dateOfBirth: string): number {
  if (!dateOfBirth) return 0
  const birth = new Date(dateOfBirth)
  const today = new Date()
  let age = today.getFullYear() - birth.getFullYear()
  const monthDiff = today.getMonth() - birth.getMonth()
  if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
    age--
  }
  return age
}

function isBirthday(dateOfBirth: string): boolean {
  if (!dateOfBirth) return false
  const birth = new Date(dateOfBirth)
  const today = new Date()
  return birth.getDate() === today.getDate() && birth.getMonth() === today.getMonth()
}

function parseOrder(order: string): { order: string; direction: string } {
  switch (order) {
    case 'name_desc': return { order: 'name', direction: 'desc' }
    case 'age_asc': return { order: 'age', direction: 'asc' }
    case 'age_desc': return { order: 'age', direction: 'desc' }
    default: return { order: 'name', direction: 'asc' }
  }
}

async function fetchReport() {
  loading.value = true
  try {
    const { order, direction } = parseOrder(filterOrder.value)
    const params: any = { order, direction, per_page: 50 }
    if (filterProfessional.value) {
      params.professional_id = filterProfessional.value
    }
    const { data } = await axios.get('/staff/reports/patients', { params })
    const paginated = data.data
    patients.value = paginated.data || []
    pagination.value = {
      current_page: paginated.current_page,
      last_page: paginated.last_page,
      from: paginated.from,
      to: paginated.to,
      total: paginated.total,
      prev_page_url: paginated.prev_page_url,
      next_page_url: paginated.next_page_url,
    }
  } catch {
    patients.value = []
  } finally {
    loading.value = false
  }
}

async function fetchProfessionals() {
  try {
    const { data } = await axios.get('/staff/reports/professionals')
    professionals.value = data.data || []
  } catch {
    professionals.value = []
  }
}

function goToPage(page: number) {
  // Recarrega com a página específica
  // Como o backend usa paginate(), podemos passar ?page=
  loading.value = true
  const { order, direction } = parseOrder(filterOrder.value)
  const params: any = { order, direction, per_page: 50, page }
  if (filterProfessional.value) {
    params.professional_id = filterProfessional.value
  }
  axios.get('/staff/reports/patients', { params }).then(({ data }) => {
    const paginated = data.data
    patients.value = paginated.data || []
    pagination.value = {
      current_page: paginated.current_page,
      last_page: paginated.last_page,
      from: paginated.from,
      to: paginated.to,
      total: paginated.total,
      prev_page_url: paginated.prev_page_url,
      next_page_url: paginated.next_page_url,
    }
  }).catch(() => {
    patients.value = []
  }).finally(() => {
    loading.value = false
  })
}

onMounted(() => {
  fetchProfessionals()
  fetchReport()
})
</script>
