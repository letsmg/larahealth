<template>
  <!-- Layout autenticado: AuthHeader (com sidebar) + conteúdo + AuthFooter -->
  <div class="flex flex-col min-h-screen">
    <AuthHeader>
      <slot />
    </AuthHeader>
    <AuthFooter />
  </div>
</template>

<script setup lang="ts">
import AuthHeader from '../components/AuthHeader.vue'
import AuthFooter from '../components/AuthFooter.vue'
</script>
