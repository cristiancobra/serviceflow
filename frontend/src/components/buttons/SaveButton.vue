<template>
  <button 
    type="button"
    class="btn-save"
    @click="$emit('click')"
    :title="title"
  >
    <font-awesome-icon icon="fa-solid fa-check" class="text-white" />
  </button>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    default: 'Salvar',
  },
});

defineEmits(['click']);
</script>

<style scoped>
.btn-save {
  background-color: var(--color-primary, #B1388D);
  color: white;
  font-weight: 500;
  padding: 0.5rem 0.75rem;
  border-radius: 0.25rem;
  font-size: 0.875rem;
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  transition: all 0.2s;
}

.btn-save:hover {
  background-color: #8B2A6E;
  transform: translateY(-1px);
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.btn-save:active {
  transform: translateY(0);
}
</style>
