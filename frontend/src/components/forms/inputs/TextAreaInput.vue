<template>
  <div class="mb-5">
    <label class="form-label" :for="name">{{ label }}</label>
    <textarea
      class="form-control"
      :id="name"
      :name="name"
      :value="modelValue"
      :placeholder="placeholder"
      :rows="rows" 
      @input="updateInput"
    ></textarea>
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
    rows: Number,
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
  