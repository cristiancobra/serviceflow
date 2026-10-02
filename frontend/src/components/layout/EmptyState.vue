<template>
  <div class="text-center py-12 px-5 text-base-content/60">
    <font-awesome-icon v-if="icon" :icon="icon" class="text-5xl mb-4 opacity-50" />
    <p class="text-lg" :class="{ 'font-semibold text-base-content': description }">{{ text }}</p>
    <p v-if="description" class="mt-2 text-sm text-base-content/50">{{ description }}</p>
    <!-- Ação opcional, ex: botão "Criar o primeiro..." -->
    <div v-if="$slots.default" class="mt-4">
      <slot />
    </div>
  </div>
</template>

<script>
// Estado vazio padrão de listas e seções: ícone, frase e descrição opcional.
export default {
  name: "EmptyState",
  props: {
    text: {
      type: String,
      required: true,
    },
    icon: {
      type: String,
      default: "",
    },
    description: {
      type: String,
      default: "",
    },
  },
};
</script>
