<template>
  <button 
    type="button"
    class="btn-close"
    @click="$emit('click')"
    :title="title"
  >
    <font-awesome-icon icon="fa-solid fa-xmark" />
  </button>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    default: 'Fechar',
  },
});

defineEmits(['click']);
</script>

<style scoped>
.btn-close {
  background: transparent;
  padding: 0.5rem;
  border-radius: 0.375rem;
  transition: all 0.2s;
  line-height: 1;
  color: #9ca3af;
  border: none;
}

.btn-close::before,
.btn-close::after {
  content: none !important;
  display: none !important;
}

.btn-close:hover {
  color: #4b5563;
  background-color: #f3f4f6;
}

.btn-close:active {
  background-color: #e5e7eb;
}
</style>
