<template>
  <button 
    type="button"
    class="btn-cancel"
    @click="$emit('click')"
    :title="title"
  >
    <font-awesome-icon icon="fa-solid fa-xmark" class="text-white" />
  </button>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    default: 'Cancelar',
  },
});

defineEmits(['click']);
</script>

<style scoped>
.btn-cancel {
  background-color: #6b7280;
  color: white;
  font-weight: 500;
  padding: 0.5rem 0.75rem;
  border-radius: 0.25rem;
  font-size: 0.875rem;
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  transition: all 0.2s;
}

.btn-cancel:hover {
  background-color: #4b5563;
  transform: translateY(-1px);
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.btn-cancel:active {
  transform: translateY(0);
}
</style>
