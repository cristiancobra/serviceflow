<template>
  <button 
    type="button"
    class="inline-flex items-center justify-center px-4 py-2 bg-primary text-white rounded-lg font-semibold transition-all hover:bg-warning active:translate-y-px shadow-sm"
    @click="$emit('click')"
    :title="title"
  >
    <font-awesome-icon :icon="isOpen ? 'fa-solid fa-minus' : 'fa-solid fa-plus'" :class="{ 'me-2': !iconOnly }" />
    <span v-if="!iconOnly">{{ isOpen ? 'Fechar' : 'Adicionar Jornada' }}</span>
  </button>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    default: 'Adicionar jornada',
  },
  isOpen: {
    type: Boolean,
    default: false,
  },
  // Exibe apenas o ícone (ex: modal compacto, quando há outro modal aberto ao lado)
  iconOnly: {
    type: Boolean,
    default: false,
  },
});

defineEmits(['click']);
</script>
