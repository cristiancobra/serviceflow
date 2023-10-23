<template>
  <div class="input-field">
    <label class="label" :for="name">{{ label }}</label>
    <input
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
  props: {
    label: String,
    type: String,
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
.input-field {
  margin-bottom: 15px;
  margin-right: 100px;
}

.label {
  margin-right: 20px;
}
</style>
  