<template>
  <!-- Header público: navegação superior para páginas não autenticadas -->
  <header class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        <!-- Logo -->
        <router-link to="/" class="flex items-center gap-2">
          <div class="w-8 h-8 bg-emerald-600 rounded-lg flex items-center justify-center">
            <span class="text-white font-bold text-sm">LH</span>
          </div>
          <span class="text-lg font-bold text-gray-900">Blink</span>
        </router-link>

        <!-- Navigation -->
        <nav class="flex items-center gap-4">
          <router-link
            to="/login"
            class="text-sm font-medium text-gray-600 hover:text-emerald-600 transition-colors"
          >
            Entrar
          </router-link>
          <router-link
            to="/register"
            class="text-sm font-medium px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors"
          >
            Cadastre-se
          </router-link>
        </nav>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
// Header público - usado em páginas não autenticadas (Login, Register)
</script>
