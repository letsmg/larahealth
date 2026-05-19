<template>
  <!-- AuthFooter: rodapé simples para área autenticada -->
  <footer class="bg-white border-t border-gray-200 mt-auto">
    <div class="px-8 py-4">
      <p class="text-sm text-gray-500 text-center">
        &copy; {{ year }} LaraHealth. Todos os direitos reservados.
      </p>
    </div>
  </footer>
</template>

<script setup lang="ts">
// AuthFooter - rodapé para páginas autenticadas (Staff / Patient)
const year = new Date().getFullYear()
</script>
