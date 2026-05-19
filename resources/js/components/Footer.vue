<template>
  <!-- Footer público: rodapé para páginas não autenticadas -->
  <footer class="bg-white border-t border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <p class="text-sm text-gray-500">
          &copy; {{ year }} LaraHealth. Todos os direitos reservados.
        </p>
        <div class="flex items-center gap-4 text-sm text-gray-500">
          <a href="#" class="hover:text-emerald-600 transition-colors">Termos de Uso</a>
          <a href="#" class="hover:text-emerald-600 transition-colors">Privacidade</a>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup lang="ts">
// Footer público - usado em páginas não autenticadas (Login, Register)
const year = new Date().getFullYear()
</script>
