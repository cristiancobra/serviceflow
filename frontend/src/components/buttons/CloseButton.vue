<template>
  <button 
    type="button"
    class="btn btn-ghost btn-sm btn-square text-base-content/50 hover:text-base-content"
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
