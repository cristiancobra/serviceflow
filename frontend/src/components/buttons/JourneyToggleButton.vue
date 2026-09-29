<template>
  <button
    type="button"
    :class="[
      'flex items-center justify-center rounded-full text-white transition hover:scale-110',
      running ? 'bg-info hover:bg-info' : 'bg-success hover:bg-success',
      size,
    ]"
    :title="running ? stopTitle : startTitle"
    @click="$emit(running ? 'stop' : 'start')"
  >
    <font-awesome-icon :icon="running ? 'fa-solid fa-hand' : 'fa-solid fa-play'" fixed-width :class="iconSize" />
  </button>
</template>

<script setup>
defineProps({
  running: {
    type: Boolean,
    default: false,
  },
  size: {
    type: String,
    default: 'w-7 h-7',
  },
  iconSize: {
    type: String,
    default: 'text-sm',
  },
  startTitle: {
    type: String,
    default: 'Iniciar jornada',
  },
  stopTitle: {
    type: String,
    default: 'Parar jornada',
  },
});

defineEmits(['start', 'stop']);
</script>
