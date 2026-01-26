<template>
  <div class="flex-[2] flex justify-center">
    <button
      type="button"
      class="btn btn-primary ml-auto"
      @click="emitOpen"
    >
      <font-awesome-icon icon="fa-solid fa-plus" class="text-white" />
    </button>
  </div>
</template>

<script setup>
const props = defineProps({
  target: {
    type: String,
    required: true,
  },
})

const emit = defineEmits(['open-modal'])

function emitOpen() {
  emit('open-modal', props.target)
}
</script>
