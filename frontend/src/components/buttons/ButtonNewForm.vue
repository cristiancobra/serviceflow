<template>
  <div class="flex-[2] flex justify-center">
    <button
      type="button"
      class="btn btn-primary ml-auto flex items-center gap-2"
      @click="emitOpen"
      :title="title"
    >
      <font-awesome-icon icon="fa-solid fa-plus" class="text-white" />
      <font-awesome-icon v-if="extraIcon" :icon="extraIcon" class="text-white" />
    </button>
  </div>
</template>

<script setup>
const props = defineProps({
  target: {
    type: String,
    required: true,
  },
  extraIcon: {
    type: String,
    default: '',
  },
  title: {
    type: String,
    default: '',
  },
})

const emit = defineEmits(['open-modal'])

function emitOpen() {
  emit('open-modal', props.target)
}
</script>
