<template>
  <div class="mb-5">
    <label class="form-label" :for="name">{{ label }}</label>
      <input
        class="form-control"
        :type="type"
        :id="name"
        :name="name"
        :value="modelValue"
        :placeholder="placeholder"
        @input="updateInput"
      />
  </div>
</template>
    
  <script>
export default {
  data() {
    return {
      type: 'datetime-local',
    }
  },
  props: {
    label: String,
    name: String,
    modelValue: [String, Number],
    placeholder: String,
  },
  methods: {
    updateInput(event) {
      this.$emit("update:modelValue", event.target.value);
    },
  },
};
</script>
    
  <style scoped>
  label {
    text-align: right;
  }
</style>
  