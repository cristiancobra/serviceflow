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

export default {
  data() {
    return {
      localValue: this.modelValue,
    };
  },
  props: {
    label: String,
    name: String,
    modelValue: [String, Number],
  },
  watch: {
    modelValue(newValue) {
      this.localValue = newValue;
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