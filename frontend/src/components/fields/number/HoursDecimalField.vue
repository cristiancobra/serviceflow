<template>
  <div class="main-container">
    <label class="form-label" :for="name">{{ label }}</label>
      <div v-if="localValue">
        <p class="text-end">
        {{ localValue }}
        </p>
      </div>
      <div v-else>
        não informado
      </div>
  </div>
</template>

<script>
import { convertDecimalToSeconds, convertSecondsToDecimal } from "@/utils/date/dateUtils";

export default {
  data() {
    return {
      localValue: this.convertSecondsToDecimal(this.modelValue),
    };
  },
  props: {
    label: String,
    name: String,
    modelValue: [String, Number],
  },
  methods: {
    convertDecimalToSeconds,
    convertSecondsToDecimal,
  },
  watch: {
    modelValue(newValue) {
      this.localValue = this.convertSecondsToDecimal(newValue);
    },
  },
};
</script>

<style scoped>
.main-container {
  display: flex;
  flex-direction: row;
  align-items: flex-start;
}

.show-label {
  text-align: left;
  font-weight: 800;
}
</style>