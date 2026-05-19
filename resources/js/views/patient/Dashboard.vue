<template>
  <div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg p-8">
      <h2 class="text-2xl font-semibold text-gray-900 mb-2">Meu Perfil</h2>
      <p class="text-gray-500 mb-6">Bem-vindo ao LaraHealth, {{ user.name }}!</p>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="p-4 bg-emerald-50 rounded-xl">
          <p class="text-sm text-gray-500">Nome</p>
          <p class="font-medium text-gray-900">{{ user.name }}</p>
        </div>
        <div class="p-4 bg-emerald-50 rounded-xl">
          <p class="text-sm text-gray-500">E-mail</p>
          <p class="font-medium text-gray-900">{{ user.email }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const user = computed(() => JSON.parse(localStorage.getItem('user') || '{}'));
</script>
