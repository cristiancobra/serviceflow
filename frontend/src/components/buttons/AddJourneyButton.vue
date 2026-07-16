<template>
  <button 
    type="button"
    class="inline-flex items-center justify-center px-4 py-2 bg-primary text-white rounded-lg font-semibold transition-all hover:bg-orange-700 active:translate-y-px shadow-sm"
    @click="$emit('click')"
    :title="title"
  >
    <font-awesome-icon :icon="isOpen ? 'fa-solid fa-minus' : 'fa-solid fa-plus'" class="me-2" />
    {{ isOpen ? 'Fechar' : 'Adicionar Jornada' }}
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
});

defineEmits(['click']);
</script>
