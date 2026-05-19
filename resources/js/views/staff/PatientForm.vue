<template>
  <div class="max-w-2xl mx-auto">
    <div class="mb-6">
      <router-link to="/staff/patients" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium inline-flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Voltar
      </router-link>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-emerald-200 p-6">
      <h1 class="text-2xl font-bold text-emerald-900 mb-6">Novo Paciente</h1>

      <form @submit.prevent="handleSubmit" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-emerald-800 mb-1">Nome Completo</label>
            <input v-model="form.full_name" type="text" required
              class="w-full px-4 py-2.5 border border-emerald-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none bg-emerald-50/30" />
          </div>
          <div>
            <label class="block text-sm font-medium text-emerald-800 mb-1">E-mail</label>
            <input v-model="form.email" type="email" required
              class="w-full px-4 py-2.5 border border-emerald-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none bg-emerald-50/30" />
          </div>
          <div>
            <label class="block text-sm font-medium text-emerald-800 mb-1">CPF</label>
            <input v-model="form.cpf" type="text" required placeholder="000.000.000-00"
              class="w-full px-4 py-2.5 border border-emerald-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none bg-emerald-50/30" />
          </div>
          <div>
            <label class="block text-sm font-medium text-emerald-800 mb-1">Data de Nascimento</label>
            <input v-model="form.date_of_birth" type="date" required
              class="w-full px-4 py-2.5 border border-emerald-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none bg-emerald-50/30" />
          </div>
          <div>
            <label class="block text-sm font-medium text-emerald-800 mb-1">Senha</label>
            <input v-model="form.password" type="password" required minlength="8"
              class="w-full px-4 py-2.5 border border-emerald-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none bg-emerald-50/30" />
          </div>
          <div>
            <label class="block text-sm font-medium text-emerald-800 mb-1">Confirmar Senha</label>
            <input v-model="form.password_confirmation" type="password" required
              class="w-full px-4 py-2.5 border border-emerald-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none bg-emerald-50/30" />
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-emerald-800 mb-1">Queixa Principal</label>
          <textarea v-model="form.main_complaint" rows="2"
            class="w-full px-4 py-2.5 border border-emerald-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none bg-emerald-50/30 resize-none"></textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-emerald-800 mb-1">Logradouro</label>
            <input v-model="form.street" type="text"
              class="w-full px-4 py-2.5 border border-emerald-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none bg-emerald-50/30" />
          </div>
          <div>
            <label class="block text-sm font-medium text-emerald-800 mb-1">Bairro</label>
            <input v-model="form.neighborhood" type="text"
              class="w-full px-4 py-2.5 border border-emerald-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none bg-emerald-50/30" />
          </div>
          <div>
            <label class="block text-sm font-medium text-emerald-800 mb-1">Cidade</label>
            <input v-model="form.city" type="text"
              class="w-full px-4 py-2.5 border border-emerald-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none bg-emerald-50/30" />
          </div>
          <div>
            <label class="block text-sm font-medium text-emerald-800 mb-1">Estado (UF)</label>
            <input v-model="form.state" type="text" maxlength="2" placeholder="SP"
              class="w-full px-4 py-2.5 border border-emerald-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none bg-emerald-50/30" />
          </div>
        </div>

        <div v-if="error" class="p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-600">
          {{ error }}
        </div>

        <div class="flex gap-3 pt-2">
          <button type="button" @click="fillTestData"
            class="px-4 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 font-medium rounded-lg transition-colors text-sm">
            Preencher Teste
          </button>
          <button type="button" @click="$router.push('/staff/patients')"
            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors text-sm">
            Cancelar
          </button>
          <button type="submit" :disabled="loading"
            class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors text-sm disabled:opacity-50 shadow-sm">
            {{ loading ? 'Salvando...' : 'Cadastrar' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const loading = ref(false);
const error = ref('');

const form = ref({
  full_name: '',
  email: '',
  cpf: '',
  date_of_birth: '',
  password: '',
  password_confirmation: '',
  main_complaint: '',
  street: '',
  neighborhood: '',
  city: '',
  state: '',
});

function fillTestData() {
  const names = ['Maria Silva', 'João Santos', 'Ana Oliveira', 'Carlos Pereira', 'Juliana Costa'];
  const complaints = ['Dor de cabeça frequente', 'Consulta de rotina', 'Dor nas costas', 'Febre há 3 dias', 'Acompanhamento'];
  const streets = ['Rua das Flores, 123', 'Av. Principal, 456', 'Rua do Comércio, 789', 'Av. Brasil, 321', 'Rua dos Pinheiros, 654'];
  const neighborhoods = ['Centro', 'Jardim América', 'Vila Nova', 'Bela Vista', 'Santa Cecília'];
  const cities = ['São Paulo', 'Rio de Janeiro', 'Belo Horizonte', 'Curitiba', 'Porto Alegre'];
  const states = ['SP', 'RJ', 'MG', 'PR', 'RS'];

  const idx = Math.floor(Math.random() * 5);
  form.value = {
    full_name: names[idx],
    email: `paciente${Date.now()}@email.com`,
    cpf: String(Math.floor(Math.random() * 99999999999)).padStart(11, '0'),
    date_of_birth: `198${Math.floor(Math.random() * 9) + 1}-0${Math.floor(Math.random() * 9) + 1}-${String(Math.floor(Math.random() * 28) + 1).padStart(2, '0')}`,
    password: 'password',
    password_confirmation: 'password',
    main_complaint: complaints[idx],
    street: streets[idx],
    neighborhood: neighborhoods[idx],
    city: cities[idx],
    state: states[idx],
  };
  error.value = '';
}

async function handleSubmit() {
  loading.value = true;
  error.value = '';

  try {
    await axios.post('/register', {
      name: form.value.full_name,
      email: form.value.email,
      cpf: form.value.cpf,
      date_of_birth: form.value.date_of_birth,
      password: form.value.password,
      password_confirmation: form.value.password_confirmation,
      main_complaint: form.value.main_complaint || null,
      street: form.value.street || null,
      neighborhood: form.value.neighborhood || null,
      city: form.value.city || null,
      state: form.value.state || null,
    });

    router.push('/staff/patients');
  } catch (e) {
    error.value = e.response?.data?.message || 'Erro ao cadastrar paciente.';
  } finally {
    loading.value = false;
  }
}
</script>
