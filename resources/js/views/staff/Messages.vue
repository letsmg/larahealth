<template>
  <div>
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-2xl font-bold text-emerald-900">Mensagens</h1>
        <p class="text-emerald-600 mt-1">Comunicação interna entre a equipe.</p>
      </div>
      <button
        @click="showForm = true"
        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors text-sm shadow-sm"
      >
        Nova Mensagem
      </button>
    </div>

    <!-- Send Message Form -->
    <div v-if="showForm" class="bg-white rounded-xl border border-emerald-200 p-6 mb-6 shadow-sm">
      <h2 class="text-lg font-semibold text-emerald-900 mb-4">Nova Mensagem</h2>

      <form @submit.prevent="handleSend" class="space-y-4 max-w-lg">
        <div>
          <label class="block text-sm font-medium text-emerald-800 mb-1">Destinatário</label>
          <select
            v-model="form.recipient_id"
            required
            class="w-full px-4 py-2.5 border border-emerald-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-colors bg-emerald-50/30"
          >
            <option value="" disabled>Selecione um destinatário</option>
            <option v-for="user in users" :key="user.id" :value="user.id">
              {{ user.name }} ({{ user.email }})
            </option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-emerald-800 mb-1">Assunto</label>
          <input
            v-model="form.subject"
            type="text"
            required
            class="w-full px-4 py-2.5 border border-emerald-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-colors bg-emerald-50/30"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-emerald-800 mb-1">Mensagem</label>
          <textarea
            v-model="form.body"
            required
            rows="4"
            class="w-full px-4 py-2.5 border border-emerald-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-colors resize-none bg-emerald-50/30"
          ></textarea>
        </div>

        <div v-if="error" class="p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-600">
          {{ error }}
        </div>

        <div class="flex gap-3">
          <button
            type="button"
            @click="fillTestData"
            class="px-4 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 font-medium rounded-lg transition-colors text-sm"
          >
            Preencher Teste
          </button>
          <button
            type="button"
            @click="cancelForm"
            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors text-sm"
          >
            Cancelar
          </button>
          <button
            type="submit"
            :disabled="loading"
            class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors text-sm disabled:opacity-50 shadow-sm"
          >
            {{ loading ? 'Enviando...' : 'Enviar' }}
          </button>
        </div>
      </form>
    </div>

    <!-- Messages List -->
    <div class="bg-white rounded-xl border border-emerald-200 overflow-hidden shadow-sm">
      <div v-if="messages.length === 0" class="p-8 text-center text-emerald-500">
        Nenhuma mensagem recebida.
      </div>

      <div v-for="msg in messages" :key="msg.id" class="p-4 border-b border-emerald-100 last:border-b-0 hover:bg-emerald-50/50 transition-colors">
        <div class="flex items-start justify-between">
          <div class="flex-1 min-w-0 cursor-pointer" @click="openViewModal(msg)">
            <div class="flex items-center gap-2">
              <span
                v-if="!msg.is_read"
                class="w-2 h-2 bg-emerald-500 rounded-full flex-shrink-0"
                title="Não lida"
              ></span>
              <p class="font-medium text-emerald-900 truncate">{{ msg.subject }}</p>
            </div>
            <p class="text-sm text-emerald-600 mt-1">
              {{ msg.sender?.name || 'Desconhecido' }} — {{ formatDate(msg.created_at) }}
            </p>
          </div>
          <div class="flex items-center gap-2 ml-4 flex-shrink-0">
            <button
              @click="toggleRead(msg)"
              class="px-3 py-1.5 text-xs rounded-lg transition-colors"
              :class="msg.is_read ? 'text-gray-500 hover:bg-gray-100' : 'text-emerald-600 hover:bg-emerald-100'"
              :title="msg.is_read ? 'Marcar como não lida' : 'Marcar como lida'"
            >
              {{ msg.is_read ? 'Lida' : 'Não lida' }}
            </button>
            <button
              @click="openViewModal(msg)"
              class="px-3 py-1.5 text-xs text-blue-600 hover:bg-blue-100 rounded-lg transition-colors"
              title="Visualizar mensagem"
            >
              Visualizar
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de Visualização -->
    <div v-if="showViewModal && viewingMessage" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click.self="showViewModal = false">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4">
        <div class="px-6 py-4 border-b border-emerald-200 flex items-center justify-between">
          <h3 class="text-lg font-semibold text-emerald-900">{{ viewingMessage.subject }}</h3>
          <button @click="showViewModal = false" class="text-gray-400 hover:text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        <div class="p-6 space-y-4">
          <div class="flex items-center justify-between text-sm text-emerald-600">
            <span>De: <strong>{{ viewingMessage.sender?.name || 'Desconhecido' }}</strong></span>
            <span>{{ formatDate(viewingMessage.created_at) }}</span>
          </div>
          <div class="text-emerald-900 leading-relaxed whitespace-pre-wrap">
            {{ viewingMessage.body }}
          </div>
          <div class="flex gap-2 pt-2 border-t border-emerald-100">
            <button
              @click="toggleRead(viewingMessage); showViewModal = false"
              class="px-4 py-2 text-sm rounded-lg transition-colors"
              :class="viewingMessage.is_read ? 'bg-gray-100 text-gray-700 hover:bg-gray-200' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200'"
            >
              {{ viewingMessage.is_read ? 'Marcar como não lida' : 'Marcar como lida' }}
            </button>
            <button
              @click="showViewModal = false"
              class="px-4 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors"
            >
              Fechar
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { generateTestMessageData } from '../../utils/formHelpers'
import emitter from '../../eventBus'


const loading = ref(false)
const error = ref('')
const showForm = ref(false)
const messages = ref<any[]>([])
const users = ref<any[]>([])

// Modal de visualização
const showViewModal = ref(false)
const viewingMessage = ref<any>(null)

const form = ref({
  recipient_id: '',
  subject: '',
  body: '',
})

function fillTestData() {
  const data = generateTestMessageData()
  form.value.subject = data.subject
  form.value.body = data.content
  if (users.value.length > 0) {
    const currentUser = JSON.parse(localStorage.getItem('user') || '{}')
    const otherUsers = users.value.filter((u: any) => u.id !== currentUser.id)
    if (otherUsers.length > 0) {
      form.value.recipient_id = String(otherUsers[Math.floor(Math.random() * otherUsers.length)].id)
    }
  }
  error.value = ''
}

function formatDate(date: string) {
  return new Date(date).toLocaleDateString('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

function cancelForm() {
  showForm.value = false
  form.value = { recipient_id: '', subject: '', body: '' }
  error.value = ''
}

async function openViewModal(msg: any) {
  viewingMessage.value = msg
  showViewModal.value = true
  // Marca automaticamente como lida ao visualizar
  if (!msg.is_read) {
    await markAsRead(msg)
  }
}

async function fetchMessages() {
  try {
    const { data } = await axios.get('/staff/messages')
    messages.value = data.data?.data || data.data || data
  } catch {
    // Silently fail
  }
}

async function fetchUsers() {
  try {
    const { data } = await axios.get('/staff/users')
    users.value = data.data || data
  } catch {
    // Silently fail
  }
}

async function handleSend() {
  loading.value = true
  error.value = ''

  try {
    await axios.post('/staff/messages', {
      recipient_id: Number(form.value.recipient_id),
      subject: form.value.subject,
      body: form.value.body,
    })

    cancelForm()
    await fetchMessages()
  } catch (e: any) {
    error.value = e.response?.data?.message || 'Erro ao enviar mensagem.'
  } finally {
    loading.value = false
  }
}

/**
 * Marca a mensagem como lida via API e atualiza o estado local.
 * Usado automaticamente ao abrir o modal de visualização.
 */
async function markAsRead(msg: any) {
  try {
    await axios.patch(`/staff/messages/${msg.id}/read`)
    msg.is_read = true
    // Notifica o AuthHeader para atualizar o contador instantaneamente
    emitter.emit('messages:read-status-changed')
  } catch {
    // Silently fail
  }
}

/**
 * Alterna o estado lido/não lido da mensagem.
 * Usa o valor retornado pela API para manter consistência com o banco.
 */
async function toggleRead(msg: any) {
  try {
    const response = await axios.patch(`/staff/messages/${msg.id}/toggle-read`)
    // Atualiza o estado local com o valor retornado pela API
    msg.is_read = response.data.data.is_read
    // Notifica o AuthHeader para atualizar o contador instantaneamente
    emitter.emit('messages:read-status-changed')
  } catch {
    // Silently fail
  }
}

onMounted(() => {
  fetchMessages()
  fetchUsers()
})
</script>
