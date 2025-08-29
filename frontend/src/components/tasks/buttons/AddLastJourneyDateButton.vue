<template>
  <div class="flex flex-row items-center space-x-4">
    <button
      v-if="showEndTaskButton"
      class="w-8 h-8 flex items-center justify-center rounded-full bg-primary text-white hover:bg-secondary transition duration-200 ease-in-out"
      @click="emitUpdateDateConclusion"
      :title="endTaskTitle"
    >
      <font-awesome-icon icon="fa-solid fa-check-square" />
    </button>
    <div v-else class="w-8 h-8"></div>
  </div>
</template>

<script>
export default {
  name: "AddLastJourneyDateButton",
  props: {
    task: {
      type: Object,
      required: true,
    },
    showEndTaskButton: {
      type: Boolean,
      default: true,
    },
    endTaskTitle: {
      type: String,
      default: "Finalizar Tarefa",
    },
  },
  data() {
    return {
      localDateConclusion: this.task.date_conclusion || null,
    };
  },
  watch: {
    "task.date_conclusion"(newValue) {
      this.localDateConclusion = newValue;
    },
  },
  methods: {
    emitUpdateDateConclusion() {
      this.$emit("add-last-journey-date", this.localDateConclusion);
    },
  },
};
</script>