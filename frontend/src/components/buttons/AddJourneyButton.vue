<template>
  <button 
    type="button"
    class="btn-add-journey"
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

<style scoped>
.btn-add-journey {
  padding: 0.5rem 1rem;
  background-color: #f97316;
  color: white;
  border-radius: 0.5rem;
  font-weight: 600;
  transition: all 0.2s;
  border: none;
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.btn-add-journey:hover {
  background-color: #c2410c;
}

.btn-add-journey:active {
  transform: translateY(1px);
}
</style>
