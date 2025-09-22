<template>
  <div class="flex flex-row items-center space-x-4">
    <div
      v-if="showEndTaskButton && task.journeys && task.journeys.length > 0"
      class="w-8 h-8"
    >
      <button
        class="w-8 h-8 flex items-center justify-center rounded-full bg-primary text-white hover:bg-secondary transition duration-200 ease-in-out"
        @click="emitUpdateDateConclusion"
        :title="endTaskTitle"
      >
        <font-awesome-icon icon="fa-solid fa-check-square" />
      </button>
    </div>
    <div v-else class="w-8 h-8">
      <button
              class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-300 text-gray-500 cursor-not-allowed"
              disabled
            >
        <font-awesome-icon icon="fa-solid fa-check-square" />
      </button>
    </div>
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